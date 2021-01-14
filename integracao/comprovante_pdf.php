<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
include_once ('library/fpdf.php');
require_once ('conexao.php');
$conexao = new Conexao();
$conn = $conexao->conn();


$sql_data_inscricao = "select * from reserva.auditoriareserva where id_alunoreserva = {$_SESSION['codigo']}";
$result = pg_query($conn, $sql_data_inscricao);
$auditoria = pg_fetch_assoc($result);


$data_atual = "Data de Inscri��o: ".date("d/m/Y H:i:s", strtotime("{$auditoria['adr_d_data']}"));

$sql_aluno =" 
    select 
        alunoreserva.id_alunoreserva as codigo, 
        ed47_i_codigo, 
        ed47_v_nome, 
        ed47_v_mae, 
        ed47_d_nasc, 
        ed221_i_serie, 
        ed56_i_escola_origem, 
        ed221_i_serie_origem, 
        ed221_i_serie_origem,
        ed18_c_nome as escola_origem, 
        ed11_c_descr as serie_origem, 
        ed56_i_escola as escola, 
        status_abrev
    from reserva.alunoreserva
    join reserva.alunostatusreserva 
        on alunostatusreserva.id = reserva.alunoreserva.alunostatusreserva_id
    join reserva.escolareserva 
        on escolareserva.id_alunoreserva = alunoreserva.id_alunoreserva
    left join escola.escola 
        on ed56_i_escola_origem = ed18_i_codigo
    left join escola.serie 
        on ed11_i_codigo = ed221_i_serie_origem
    where 
        reserva.alunoreserva.id_alunoreserva = {$_SESSION['codigo']}
    ";
//die($sql_aluno);
$result = pg_query($conn, $sql_aluno);
$aluno = pg_fetch_assoc($result);

$sql_escola = "
    select 
        ed18_c_nome as escola 
    from escola 
    where 
        ed18_i_codigo = {$aluno['escola']}";

$result = pg_query($conn, $sql_escola);
$escola = pg_fetch_assoc($result);
$escola = trim($escola['escola']);

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
            ed60_c_situacao = 'MATRICULADO' and 
            ed60_d_datamatricula >= '2020-01-01' limit 1
        ";
    $result = pg_query($conn, $sql_ano);
    $ano_aluno_matriculado = pg_fetch_assoc($result);
    //echo $sql_ano;
    //exit;

}

//die(var_dump($aluno));

$nome_aluno = trim($aluno['ed47_v_nome']);
$nome_mae = trim($aluno['ed47_v_mae']);
if($nome_mae == ''){
	$nome_mae = "N�o informado";
}

$dtsnas = date('d/m/Y', strtotime($aluno['ed47_d_nasc']));
$codigo_espera = $aluno['codigo'];
$sql_serie = "
    select 
        ed11_c_descr 
    from serie 
    where 
        ed11_i_codigo = {$aluno['ed221_i_serie']}
    ";

$result = pg_query($conn, $sql_serie);			
$serie = pg_fetch_assoc($result);
$turma = trim($serie['ed11_c_descr']);

$oPdf = new FPDF();

$oPdf->AliasNbPages();
$oPdf->setfillcolor(235);
$oPdf->addPage('P', 'A4');
$oPdf->Image('img/Cabecalho_pdf.png',3,3,210);
$oPdf->SetXY(20,40);
$oPdf->SetFont('arial','b',8);
$oPdf->Text('140','23', 'Status: '.trim($aluno['status_abrev']));
$oPdf->Text('140','27', $data_atual );

$oPdf->SetXY(20,70);
$oPdf->SetFont('arial','b',14);
$oPdf->Text('77','50','Lista de Espera');

$oPdf->SetFont('arial','',10);
//$oPdf->MultiCell(170,5,"Atesto, para os devidos fins, que o aluno(a) {$nome_aluno}, est� com a reserva de matr�cula para {$turma['serie']}, $tipoensino, turno {$turma['turno']}, para o calend�rio 2020, na institui��o de ensino {$escola_nome['escola']}.");
$oPdf->MultiCell(170,5,"Atesto, para os devidos fins, que o aluno(a) {$nome_aluno},  nascido (a) em $dtsnas, filho (a) leg�timo (a) de $nome_mae, est� no cadastro de lista de espera sob o n�mero $codigo_espera, na Unidade Escolar $escola, para o calend�rio 2020 e inscrito na Etapa: $turma.");
//inscrito na Etapa: {$serie['ed11_c_descr']}



$oPdf->SetXY(20,95);
$oPdf->SetFont('arial','b',10);
$oPdf->MultiCell(170,5,"A LISTA DE ESPERA N�O SE CARACTERIZA COMO EFETIVA��O DE MATR�CULA, EST� SUJEITO A DISPONIBILIDADE DE VAGA.");
$oPdf->SetXY(20,260);
$oPdf->SetFont('arial','b',10);
$oPdf->MultiCell(170,5,"A inser��o dos dados do candidato e seu respectivo respons�vel foi realizada de forma volunt�ria e declarat�ria. A Secretaria de Educa��o n�o se responsabiliza pela veracidade dos dados informados no momento da inscri��o.");

if($aluno['escola_origem'] !=null){

    $oPdf->SetFont('arial','',10);
    $oPdf->SetXY(20,107);
    $oPdf->Cell(170,5,"Aluno matriculado no calend�rio de {$ano_aluno_matriculado['ano']} ");
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





