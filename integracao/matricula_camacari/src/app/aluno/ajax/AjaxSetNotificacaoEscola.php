<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../../../library/fpdf.php');
require_once('../../classe/Conn.php');
require_once('../../include/funcoes.php');
require_once('../../../../../email.php');
Conn::conect();

$_SESSION['dataOperacao'] = date("d-m-Y H:i:s"); 

$dataAgendamento = $_GET['dataAgendamento']." ".$_GET['horarioAgendamento'];


/*******************************************************************************
GRAVA A DATA DE AGENDAMENTO 
*******************************************************************************/

$sql_agendamento = "           
    UPDATE reserva.alunoreserva SET 
        ed47_d_agedamento='{$dataAgendamento}' 
    WHERE 
        id_alunoreserva={$_SESSION['codigo']}";  
    
    $stmt = Conn::$conexao->query($sql_agendamento);
    //echo $sql_agendamento;
/*******************************************************************************
   FIM 
*******************************************************************************/

if ($_GET['confirmacaoImpressao'] == 'true')
{
    $array = ["true","{$_GET['paginacao']}","{$_GET['notificarEscola']}"];  
    $json =  json_encode($array);
    echo $json;

}else{   
    $array = ["false","{$_GET['paginacao']}","{$_GET['notificarEscola']}"];  
    $json =  json_encode($array);
    echo $json;
}

    $data_atual_atestado = "Data do Atestado: ".date("d/m/Y H:i:s", strtotime($_SESSION['dataOperacao']));
    $data_atual_por_extenso  = valorExtenso($_SESSION['dataOperacao']);
    
    $sql_consula = "
        select 
        AR.id_alunoreserva as codigo, 
        ed47_i_codigo, 
        rtrim(ed47_v_nome) as ed47_v_nome, 
        to_char(AR.ed47_d_nasc, 'DD/MM/YYYY') as ed47_d_nasc,        
        rtrim(ed11_c_descr)as serie, 
        trim(AR.ed47_v_mae) as ed47_v_mae,
        trim(AR.ed47_c_nomeresp) as ed47_c_nomeresp,
        trim(AR.vch_orgaopublico) as vch_orgaopublico,
        ed47_v_cpf, 
        ed47_v_telef as telefone,
        rtrim(ed18_c_nome) as escola, 
        ed18_c_email,
        coalesce(ASR.status_descr,'Cadastrado') as status_descr,
        status_abrev, 
        ed47_d_agedamento,
        adr_d_data,
        ed52_i_ano
    from reserva.alunoreserva AR
    join reserva.alunostatusreserva  ASR
        on ASR.id = AR.alunostatusreserva_id
    join reserva.escolareserva ER 
        on ER.id_alunoreserva = AR.id_alunoreserva
    left join escola.serie S
        on S.ed11_i_codigo = ER.ed221_i_serie
    left join escola.escola E
        on ER.ed56_i_escola = E.ed18_i_codigo
    join escola.calendarioescola CE
        on E.ed18_i_codigo = CE.ed38_i_escola 
    join escola.calendario c 
        on C.ed52_i_codigo = CE.ed38_i_calendario
    join reserva.auditoriareserva AUR
        on AR.id_alunoreserva = AUR.id_alunoreserva
    where 
        AR.id_alunoreserva = {$_SESSION['codigo']} order by ed52_i_ano desc limit 1";

    //die($sql_consula);
    $stmt = Conn::$conexao->prepare($sql_consula);
    $stmt->execute();
    $resultconsulta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $dataAgendamento    = date("d/m/Y H:i", strtotime($resultconsulta['ed47_d_agedamento']));
    $dataCadastro       = date("d/m/Y H:i", strtotime($resultconsulta['adr_d_data']));
    
    $dadosCriptografia = (
            $resultconsulta['ed47_v_nome'].
            $resultconsulta['ed47_d_nasc'].
            $resultconsulta['serie'].
            $resultconsulta['escola'].
            $resultconsulta['ed47_v_cpf'].
            $resultconsulta['ed47_c_nomeresp'].
            $_SESSION['dataOperacao']);
    $resultCriptografica = strtoupper(criptografia($dadosCriptografia));
    
    /*******************************************************************************
    GRAVA O CÓDIGO DE SEGURANÇA
    *******************************************************************************/

    $sql_agendamento = "           
        UPDATE reserva.alunoreserva SET 
            ed47_v_codigoseguranca='{$resultCriptografica}' 
        WHERE 
            id_alunoreserva={$_SESSION['codigo']}";  

        $stmt = Conn::$conexao->query($sql_agendamento);
        //echo $sql_agendamento;
    /*******************************************************************************
       FIM 
    *******************************************************************************/
    
    $oPdf = new FPDF();

    $oPdf->AliasNbPages();
    $oPdf->setfillcolor(235);
    $oPdf->addPage('P', 'A4');
    $oPdf->Image('../../../../../img/Cabecalho_pdf.png',3,3,210);
    $oPdf->SetXY(20,40);
    $oPdf->SetFont('arial','b',8);
    $oPdf->Text('132','21', 'Status: '.trim($resultconsulta['status_descr']));
    $oPdf->Text('132','25', 'Data agendamento: '.$dataAgendamento );
    $oPdf->Text('132','29', 'Data cadastro: '.$dataCadastro );
    $oPdf->Text('132','33', 'Impresso por: '. $_SESSION['nome']);
    $oPdf->SetXY(20,70);
    $oPdf->SetFont('arial','b',14);
    $oPdf->Text('77','55','AUTORIZAÇÃO DE MATRÍCULA');

    $oPdf->SetFont('arial','',10);

    $telefoneFormatado = formataTelefone($resultconsulta['telefone']);
    
    /* Verifica se o orgão público foi inserido, caso positivo infomar como responsável.  */
    if ($resultconsulta['vch_orgaopublico'] !=  ''){
        $oPdf->MultiCell(170,5,"Autorizada a matrícula do aluno (a) {$resultconsulta['ed47_v_nome']}, nascido (a) em {$resultconsulta['ed47_d_nasc']}, filho (a) legítimo (a) de {$resultconsulta['ed47_v_mae']} na etapa: {$resultconsulta['serie']}, desta unidade escolar: {$resultconsulta['escola']} e no calendário 2020, cujo responsável do aluno (a) é {$resultconsulta['vch_orgaopublico']}.",0);
    }else{
        $oPdf->MultiCell(170,5,"Autorizada a matrícula do aluno (a) {$resultconsulta['ed47_v_nome']}, nascido (a) em {$resultconsulta['ed47_d_nasc']}, filho (a) legítimo (a) de {$resultconsulta['ed47_v_mae']} na etapa: {$resultconsulta['serie']}, desta unidade escolar: {$resultconsulta['escola']} e no calendário 2020, cujo responsável do aluno (a) é {$resultconsulta['ed47_c_nomeresp']} de CPF {$resultconsulta['ed47_v_cpf']} e contato {$telefoneFormatado}.",0);
    } 
    
    $oPdf->SetFont('arial','b',10);
    $oPdf->SetXY(20,95);
    $oPdf->MultiCell(170,5,"[ Matrícula agendada {$dataAgendamento} na unidade escolar",0,C);
    $oPdf->SetXY(20,100);
    $oPdf->MultiCell(170,5,"  {$resultconsulta['escola']} ]",0,C);

    $oPdf->SetFont('arial','',10);
    $oPdf->SetXY(20,110);
    $oPdf->MultiCell(170,5,"A partir da data de agendamento o atestado tem validade de 48 horas, caso o responsável não compareça na unidade escolar com a documentação necessária para a matrícula, a reserva da vaga será cancelada e novo cadastro será necessário e nova consulta de vaga disponível será realizada.");

    $oPdf->SetFont('arial','b',10);
    $oPdf->SetXY(20,130);
    $oPdf->MultiCell(170,5,"{$data_atual_por_extenso}".".",0,C);
    $oPdf->SetXY(20,150);
    $oPdf->MultiCell(170,5,"{$_SESSION['nome']}",0,C);
    $oPdf->SetXY(20,155);
    $oPdf->MultiCell(170,7,"Analista de Informações Educacionais",0,C);
    $oPdf->SetXY(20,160);
    $oPdf->MultiCell(170,7,"CMIE - Coordenação de Matrícula e Informações Educacionais",0,C);
    $oPdf->SetXY(20,260);
    $oPdf->MultiCell(170,5,"CÓDIGO DE SEGURANÇA",0,C);
    $oPdf->SetXY(20,264);
    $oPdf->MultiCell(170,5,"[{$resultCriptografica}]",0,C);

    $oPdf->Image('../../../../../img/rodape.png',0,277,210);
    $oPdf->SetFont('arial','i',6);
    $oPdf->Text(78,288.5,$_SESSION['id_usuario'] . '-' .$_SESSION['nome']);
    $oPdf->Text(141,288.5,$_SESSION['dataOperacao']);
    

    $oPdf->Output('F',"/tmp/AtestadoVagaPDF.pdf");

   /* Verifica se o orgão público foi inserido, caso positivo infomar como responsável. no e-mail */
    if ($resultconsulta['vch_orgaopublico'] !=  ''){
        $textoResponsavel =  "{$resultconsulta['vch_orgaopublico']}";
    }else{
        $textoResponsavel = "{$resultconsulta['ed47_c_nomeresp']}  
                    de CPF {$resultconsulta['ed47_v_cpf']} e contato {$telefoneFormatado}";
    }
   
    $mensagem = "
    <!DOCTYPE html>
    <html lang='en'>
        <head>
            <meta charset='iso-8859-1'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>ATESTADO DE VAGA</title>
        </head>
        <body>
            <table style='width: 100%;'>
                <thead>
                    <th style='background: gray; color:white;height:50px'>AUTORIZAÇÃO DE MATRÍCULA</th>
                </thead>
            </table>
        <table style='width: 100%;'>
            <tr> 
                <td>
                    <p>Autorizada a matrícula do aluno (a) {$resultconsulta['ed47_v_nome']}, nascido (a) em {$resultconsulta['ed47_d_nasc']}, filho (a) legítimo (a) de {$resultconsulta['ed47_v_mae']} na etapa: {$resultconsulta['serie']}, 
                    desta unidade escolar: {$resultconsulta['escola']} e no calendário 2020, cujo responsável do aluno (a) é {$textoResponsavel}.</p>
                    <br>
                    <p style='text-align: center'>[ Matrícula agendada {$dataAgendamento} na unidade escolar {$resultconsulta['escola']} ]</p>
                    <br>
                        <p>A partir da data de agendamento o atestado tem validade de 48 horas, caso o responsável não compareça na unidade escolar com a documentação necessária para a matrícula, a reserva da vaga será cancelada e novo cadastro será necessário e nova consulta de vaga disponível será realizada.</p>
                    <br>
                        <p style='text-align: center'>{$data_atual_por_extenso}.</p>
                    <br>
                        <p style='text-align: center'>{$_SESSION['nome']}</p>
                        <p style='text-align: center'>Analista de Informações Educacionais</p>
                        <p style='text-align: center'>CMIE - Coordenação de Matrícula e Informações Educacionais</p>
                    <br>
                    <br>
                        <p style='text-align: center'>CÓDIGO DE SEGURANÇA</p>
                    <p style='text-align: center'>[{$resultCriptografica}]</p>
                </td>
            </tr>
        </table>
    </body>
    </html>";
    
    $mailDestinoEscola = $resultconsulta['ed18_c_email'];
    //$mailDestinoCoordMatricula = 'rodolfosaneto@hotmail.com'; // E-mail de teste. 
    $mailDestinoCoordMatricula = 'seduccmie@educa.camacari.ba.gov.br'; // E-mail da Coordenação de Matrícula
    /*
     * Se verdadeiro Notifica a escola, se false notifica apenas a coordenação de matrícula
     */
    if ($_GET['notificarEscola'] == 'true')
        {
        // Notifica a escola por e-mail.   
        envialEmail($mensagem,$mailDestinoEscola,'/tmp/AtestadoVagaPDF.pdf',$mailDestinoCoordMatricula);
    
        /*******************************************************************************
        GRAVA A AÇÃO NA TABELA DE AUDITORIA - IMPRESSÃO DO ATESTADO 
        *******************************************************************************/

        $sql_auditoria = "           
            INSERT INTO 
                reserva.auditoriausuarioaluno
                    (usuario_id, 
                    nome_usuario, 
                    id_alunoreserva, 
                    descricao,
                    data_modificacao)
                    VALUES(
                    {$_SESSION['id_usuario']},
                    '{$_SESSION['nome']}', 
                    {$_SESSION['codigo']}, 
                    'Matrícula Agendada para o dia {$dataAgendamento} - E-mail enviado para Escola - Atestado de Vaga - CÓDIGO DE SEGURANÇA [{$resultCriptografica}]',
                    '{$_SESSION['dataOperacao']}');";
        //echo $sql_auditoria; 
        $stmt = Conn::$conexao->prepare($sql_auditoria);
        $stmt->execute();
        //$dataAtestado = $stmt->fetch(PDO::FETCH_ASSOC); 

        /*******************************************************************************
            FIM 
        *******************************************************************************/
 
    }else{
        // Notifica apenas a coordenação de matrícula por e-mail.   
        envialEmail($mensagem,$mailDestinoCoordMatricula,'/tmp/AtestadoVagaPDF.pdf','');
    }
?>