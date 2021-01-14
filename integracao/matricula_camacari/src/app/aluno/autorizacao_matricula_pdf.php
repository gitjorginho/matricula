<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo 'expirou';
    die();
}
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../../library/fpdf.php');
require_once('../classe/Conn.php');
require_once('../include/funcoes.php');
require_once('../../../../email.php');
Conn::conect();

$_SESSION['dataOperacao'] = date("d-m-Y H:i:s"); 
$reimpressao = $_GET['reimpressao'];
//echo $reimpressao;
//exit;

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
        ed52_i_ano,
        ed47_v_codigoseguranca
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
// 

// Reimpress�o = 0 - J� houve emiss�o do atestado; 
// Reimpress�o = 1 - N�o houve emiss�o do atestado; 
// Reimpress�o = 3 - � reimpress�o; 

    if ($reimpressao == 0 ||$reimpressao == 2){  
        $resultCriptografica = $resultconsulta['ed47_v_codigoseguranca'];
    }else{
        $dadosCriptografia = (
        $resultconsulta['ed47_v_nome'].
        $resultconsulta['ed47_d_nasc'].
        $resultconsulta['serie'].
        $resultconsulta['escola'].
        $resultconsulta['ed47_v_cpf'].
        $resultconsulta['ed47_c_nomeresp'].
        $_SESSION['dataOperacao']
        );
        $resultCriptografica = strtoupper(criptografia($dadosCriptografia));
    
    /*******************************************************************************
    GRAVA O C�DIGO DE SEGURAN�A
    *******************************************************************************/
    $sql = "           
        UPDATE reserva.alunoreserva SET 
            ed47_v_codigoseguranca='{$resultCriptografica}' 
        WHERE 
            id_alunoreserva={$_SESSION['codigo']}";  

    $stmt = Conn::$conexao->query($sql);
    /*******************************************************************************
       FIM 
    *******************************************************************************/
}
/*******************************************************************************
    GRAVA A A��O NA TABELA DE AUDITORIA - IMPRESS�O DO ATESTADO 
*       reimpress�o o status = 3 e Impress�o o status 0, os demais s�o impress�o; 
*******************************************************************************/
if ($reimpressao == 2)
{
  $textoReimpressao = "Reimpress�o"; 
}else{
  $textoReimpressao = "Impress�o";   
}
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
            'Matr�cula Agendada para o dia {$dataAgendamento} - {$textoReimpressao} do Atestado de Vaga - C�DIGO DE SEGURAN�A [{$resultCriptografica}]',
            '{$_SESSION['dataOperacao']}')";

$stmt = Conn::$conexao->prepare($sql_auditoria);
$stmt->execute();

/*******************************************************************************
    FIM 
*******************************************************************************/

$oPdf = new FPDF();

$oPdf->AliasNbPages();
$oPdf->setfillcolor(235);
$oPdf->addPage('P', 'A4');
$oPdf->Image('../../../../img/Cabecalho_pdf.png',3,3,210);
$oPdf->SetXY(20,40);
$oPdf->SetFont('arial','b',8);
$oPdf->Text('132','21', 'Status: '.trim($resultconsulta['status_descr']));
$oPdf->Text('132','25', 'Data agendamento: '.$dataAgendamento );
$oPdf->Text('132','29', 'Data cadastro: '.$dataCadastro );
$oPdf->Text('132','33', 'Impresso por: '. $_SESSION['nome']);

$oPdf->SetXY(20,70);
$oPdf->SetFont('arial','b',14);
$oPdf->Text('77','55','AUTORIZA��O DE MATR�CULA');

$oPdf->SetFont('arial','',10);

$telefoneFormatado = formataTelefone($resultconsulta['telefone']);

/* Verifica se o org�o p�blico foi inserido, caso positivo infomar como respons�vel.  */
if ($resultconsulta['vch_orgaopublico'] !=  ''){
    $oPdf->MultiCell(170,5,"Autorizada a matr�cula do aluno (a) {$resultconsulta['ed47_v_nome']}, nascido (a) em {$resultconsulta['ed47_d_nasc']}, filho (a) leg�timo (a) de {$resultconsulta['ed47_v_mae']} na etapa: {$resultconsulta['serie']}, desta unidade escolar: {$resultconsulta['escola']} e no calend�rio 2020, cujo respons�vel do aluno (a) � {$resultconsulta['vch_orgaopublico']}.",0);
}else{
    $oPdf->MultiCell(170,5,"Autorizada a matr�cula do aluno (a) {$resultconsulta['ed47_v_nome']}, nascido (a) em {$resultconsulta['ed47_d_nasc']}, filho (a) leg�timo (a) de {$resultconsulta['ed47_v_mae']} na etapa: {$resultconsulta['serie']}, desta unidade escolar: {$resultconsulta['escola']} e no calend�rio 2020, cujo respons�vel do aluno (a) � {$resultconsulta['ed47_c_nomeresp']} de CPF {$resultconsulta['ed47_v_cpf']} e contato {$telefoneFormatado}.",0);
} 

$oPdf->SetFont('arial','b',10);
$oPdf->SetXY(20,95);
$oPdf->MultiCell(170,5,"[ Matr�cula agendada {$dataAgendamento} na unidade escolar",0,C);
$oPdf->SetXY(20,100);
$oPdf->MultiCell(170,5,"  {$resultconsulta['escola']} ]",0,C);

$oPdf->SetFont('arial','',10);
$oPdf->SetXY(20,110);
$oPdf->MultiCell(170,5,"A partir da data de agendamento o atestado tem validade de 48 horas, caso o respons�vel n�o compare�a na unidade escolar com a documenta��o necess�ria para a matr�cula, a reserva da vaga ser� cancelada e novo cadastro ser� necess�rio e nova consulta de vaga dispon�vel ser� realizada.");

$oPdf->SetFont('arial','b',10);
$oPdf->SetXY(20,130);
$oPdf->MultiCell(170,5,"{$data_atual_por_extenso}".".",0,C);
$oPdf->SetXY(20,150);
$oPdf->MultiCell(170,5,"{$_SESSION['nome']}",0,C);
$oPdf->SetXY(20,155);
$oPdf->MultiCell(170,7,"Analista de Informa��es Educacionais",0,C);
$oPdf->SetXY(20,160);
$oPdf->MultiCell(170,7,"CMIE - Coordena��o de Matr�cula e Informa��es Educacionais",0,C);

$oPdf->SetXY(20,260);
$oPdf->MultiCell(170,5,"C�DIGO DE SEGURAN�A",0,C);
$oPdf->SetXY(20,264);
$oPdf->MultiCell(170,5,"[{$resultCriptografica}]",0,C);

$oPdf->Image('../../../../img/rodape.png',0,277,210);
$oPdf->SetFont('arial','i',6);
$data_atual = date("d-m-Y H:i:s");
$oPdf->Text(78,288.5,$_SESSION['id_usuario'] . '-' .$_SESSION['nome']);
$oPdf->Text(141,288.5,$data_atual);
$oPdf->Output();


