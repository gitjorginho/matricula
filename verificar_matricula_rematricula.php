<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('conexao.php');
$conexao = new Conexao();
$conn = $conexao->conn();

if (isset($_POST['vch_nome_aluno'])) {

    $nome_aluno = trim($_POST['vch_nome_aluno']);
    $data_nasc = dateToDatabase(trim($_POST['vch_data_nasc']));
    $vch_nome_resp = trim($_POST['vch_nome_resp']);
	$cod_aluno = $_POST['cod_aluno'];
}else{
	$cod_aluno = $_POST['cod_aluno'];
}

//$sql_matricula = "
//select *
//from aluno
//inner join matricula on ed60_i_aluno = ed47_i_codigo
//inner join matriculareserva on ed47_i_codigo = reserva_aluno
//where aluno.ed47_v_nome ilike '%$nome_aluno%'
//and aluno.ed47_d_nasc = '$data_nasc'
//and aluno.ed47_c_nomeresp = '$vch_nome_resp'
//";

if ($cod_aluno != ''){
	$sql_matricula = "
select * from reserva.alunoreserva 
where id_alunoreserva  = '$cod_aluno' limit 1 ";

$result = pg_query($conn, $sql_matricula);
$aluno = pg_fetch_assoc($result);
//die(var_dump($aluno));
if (pg_num_rows($result) >= 1) {
    $_SESSION['codigo'] = $aluno['id_alunoreserva'];
    $_SESSION['matriculado'] = 'false';
    $_SESSION['escola'] = 'true';
    header('Location:rematricula_update.php');
} else {
    header('Location:pesquisa_rematricula.php?not_found=1');
}
}else{

$sql_matricula = "
select * from reserva.alunoreserva 
where ed47_v_nome ilike '%$nome_aluno%' and ed47_c_nomeresp ilike '%$vch_nome_resp%' and ed47_d_nasc  = '$data_nasc' limit 1 ";

$result = pg_query($conn, $sql_matricula);
$aluno = pg_fetch_assoc($result);
//die(var_dump($aluno));
if (pg_num_rows($result) >= 1) {
    $_SESSION['codigo'] = $aluno['id_alunoreserva'];
    $_SESSION['matriculado'] = 'false';
    $_SESSION['escola'] = 'true';
    header('Location:rematricula_update.php');
} else {
    header('Location:pesquisa_rematricula.php?not_found=1');
}
}

function dateToDatabase($date)
{
    $date = explode('/', $date);
    $date_to_database = "$date[0]-$date[1]-$date[2]";
    return $date_to_database;
}







