<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
include_once ('library/fpdf.php');
require_once ('conexao.php');
$conexao = new Conexao();
$conn = $conexao->conn();


//$sql_data_inscricao = "select * from reserva.auditoriareserva where id_alunoreserva = {$_SESSION['codigo']}";
$sql_data_inscricao = "select escola.confirmacaorematricula.edu01_criado_em 
                         from escola.confirmacaorematricula
                        where escola.confirmacaorematricula.edu01_aluno = {$_SESSION['codigo_sge']}
                        limit 1;";

$result = pg_query($conn, $sql_data_inscricao);
$auditoria = pg_fetch_assoc($result);


$data_atual = "Data de Inscrição: ".date("d/m/Y H:i:s", strtotime("{$auditoria['edu01_criado_em']}"));

$sql_aluno = "select a.ed47_i_codigo as codigo,
                     a.ed47_i_codigo,
                     a.ed47_v_nome, 
                     a.ed47_v_mae, 
                     a.ed47_d_nasc,
                     e.ed18_i_codigo as codigo_escola,
                     e.ed18_c_nome as escola
                from escola.matricula m,
                     escola.escola e,
                     escola.turma t,
                     escola.aluno a 
               where m.ed60_i_turma = t.ed57_i_codigo 
                 and t.ed57_i_escola = e.ed18_i_codigo
                 and m.ed60_i_aluno = a.ed47_i_codigo 
                 and a.ed47_i_codigo = {$_SESSION['codigo_sge']}
                 and m.ed60_c_situacao = 'MATRICULADO'
                 and m.ed60_c_concluida = 'N';";
//die($sql_aluno);
$result = pg_query($conn, $sql_aluno);
$aluno = pg_fetch_assoc($result);

//$sql_escola = "
//    select 
//        ed18_c_nome as escola 
//    from escola 
//    where 
//        ed18_i_codigo = {$aluno['escola']}";

//$result = pg_query($conn, $sql_escola);
//$escola = pg_fetch_assoc($result);
$escola = trim($aluno['escola']);

//echo $sql_escola; 

$ano_aluno_matriculado ['ano'] ='';

if ($aluno['ed47_i_codigo'] != ''){

    $sql_ano = "
        select 
            ed52_i_ano as ano from aluno
        join matricula 
            on ed60_i_aluno = ed47_i_codigo
        join turma 
            on ed57_i_codigo = ed60_i_turma
        join calendario 
            on ed52_i_codigo = ed57_i_calendario
        where 
            ed47_i_codigo = {$aluno['ed47_i_codigo']} and 
            ed60_c_situacao = 'MATRICULADO'  
            order by ed52_i_ano desc
            limit 1
        ";
    $result = pg_query($conn, $sql_ano);
    $ano_aluno_matriculado = pg_fetch_assoc($result);
    //echo $sql_ano;
    //exit;

}

$sql_documentacao = "select trim(ed02_c_descr) ed02_c_descr   from docaluno 
join documentacao on ed49_i_documentacao =  ed02_i_codigo
where ed49_i_aluno = {$aluno['ed47_i_codigo']} ";

    $result = pg_query($conn, $sql_documentacao);
    if (pg_num_rows($result) == 0)
    {
        $arrDocumentoaluno = null;
    }
    else
        $arrDocumentoaluno = pg_fetch_all($result);
    

//die(var_dump($aluno));

$nome_aluno = trim($aluno['ed47_v_nome']);
$nome_mae = trim($aluno['ed47_v_mae']);
if($nome_mae == ''){
	$nome_mae = "Não informado";
}

$dtsnas = date('d/m/Y', strtotime($aluno['ed47_d_nasc']));
$codigo_espera = $aluno['codigo'];
//$sql_serie = "
//    select 
//        ed11_c_descr 
//    from serie 
//    where 
//        ed11_i_codigo = {$aluno['ed221_i_serie']}
//    ";

//$result = pg_query($conn, $sql_serie);			
//$serie = pg_fetch_assoc($result);
$turma = 'A';//trim($serie['ed11_c_descr']);

$oPdf = new FPDF();
$oPdf->AliasNbPages();
$oPdf->setfillcolor(235);
$oPdf->addPage('P', 'A4');
$oPdf->Image('img/Cabecalho_pdf.png',3,3,210);
$oPdf->SetXY(20,40);
$oPdf->SetFont('arial','b',8);
$oPdf->Text('140','23', 'Confirmação de rematrícula realizada.'); //.trim($aluno['status_abrev']));
$oPdf->Text('140','27', $data_atual );

$oPdf->SetXY(20,70);
$oPdf->SetFont('arial','b',14);
$oPdf->Text('77','50','Rematrícula 2021');

$oPdf->SetFont('arial','',10);
//$oPdf->MultiCell(170,5,"Atesto, para os devidos fins, que o aluno(a) {$nome_aluno}, está com a reserva de matrícula para {$turma['serie']}, $tipoensino, turno {$turma['turno']}, para o calendário 2020, na instituição de ensino {$escola_nome['escola']}.");
//$oPdf->MultiCell(170,5,"Atesto, para os devidos fins, que o aluno(a) {$nome_aluno},  nascido (a) em $dtsnas, filho (a) legítimo (a) de $nome_mae, está no cadastro de lista de espera sob o número $codigo_espera, na Unidade Escolar $escola, para o calendário 2020 e inscrito na Etapa: $turma.");
$oPdf->MultiCell(170,5,"Atesto, para os devidos fins, que o aluno(a) {$nome_aluno},  nascido (a) em $dtsnas, filho (a) legítimo (a) de $nome_mae, confirma rematrícula para Unidade Escolar: $escola. Caso o aluno possua pendência na documentação, a Unidade Escolar entrará em contato, ou será apresentado em 72 horas após avaliação.");
//inscrito na Etapa: {$serie['ed11_c_descr']}

$oPdf->SetXY(20,130);
// $oPdf->MultiCell(170,5,"Para realizar o processo de matrícula são necessárias as seguintes documentações:");
// $oPdf->SetXY(30,140);

 $oPdf->MultiCell(170,5,"Documentação Pendente:");
 $oPdf->SetXY(30,140);

if (!is_null($arrDocumentoaluno))
{
    foreach ($arrDocumentoaluno as $documento){
        $oPdf->MultiCell(180,5,'* '.$documento['ed02_c_descr']);
        $oPdf->SetXY(30,145);
    }

}

  
// $oPdf->MultiCell(180,5,"2. Certidão de Registro Civil ou RG;");
// $oPdf->SetXY(30,150);
// $oPdf->MultiCell(180,5,"3. Comprovante de residência;");
// $oPdf->SetXY(30,155);
// $oPdf->MultiCell(180,5,"4. Duas fotos 3x4 recentes;");
// $oPdf->SetXY(30,160);
// $oPdf->MultiCell(180,5,"5. Cartão de vacina (para educação infantil e anos iniciais);");
// $oPdf->SetXY(30,165);
// $oPdf->MultiCell(180,5,"6. Cartão do Sistema Único de Saúde (SUS).");

$oPdf->SetXY(20,95);
$oPdf->SetFont('arial','b',10);
//$oPdf->MultiCell(170,5,"A LISTA DE ESPERA NÃO SE CARACTERIZA COMO EFETIVAÇÃO DE MATRÍCULA, ESTÁ SUJEITO A DISPONIBILIDADE DE VAGA.");
//$oPdf->MultiCell(170,5,"A LISTA DE ESPERA NÃO SE CARACTERIZA COMO EFETIVAÇÃO DE MATRÍCULA, ESTÁ SUJEITO A DISPONIBILIDADE DE VAGA.");

$oPdf->MultiCell(170,5,"");
$oPdf->SetXY(20,260);
$oPdf->SetFont('arial','b',10);
$oPdf->MultiCell(170,5,"A inserção dos dados do candidato e seu respectivo responsável foi realizada de forma voluntária e declaratória. A Secretaria de Educação não se responsabiliza pela veracidade dos dados informados no momento da inscrição.");
$aluno['escola_origem'] = null;
if($aluno['escola_origem'] !=null){

    $oPdf->SetFont('arial','',10);
    $oPdf->SetXY(20,107);
    $oPdf->Cell(170,5,"Aluno matriculado no calendário de {$ano_aluno_matriculado['ano']} ");
    $oPdf->SetXY(20,112);
    $oPdf->Cell(170,5,"Unidade Escolar : {$aluno['escola_origem']} ");
    $oPdf->SetXY(20,117);
    $oPdf->Cell(170,5,"Serie: {$aluno['serie_origem']} ");
    
}
$oPdf->Image('img/rodape.png',0,277,210);
$oPdf->SetFont('arial','i',6);
$data_atual = date("d-m-Y H:i:s");
if (isset($_SESSION['nome'])){
    $oPdf->Text(78,288.5,$_SESSION['id_usuario'] . '-' .$_SESSION['nome']);
}else{
    $oPdf->Text(78,288.5,'Prefeitura');
}
$oPdf->Text(141,288.5,$data_atual);
$oPdf->Output();





