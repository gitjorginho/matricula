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
    select (select true from confirmacaorematricula where edu01_aluno = ed47_i_codigo) as confirmacao_rematricula, reserva.alunoreserva.*, ap.ed79_i_serie as idserie,ac.ed56_i_escola as idescola, ac.ed56_i_calendario as idcalendario, ac.ed56_i_base as idbase from reserva.alunoreserva left join escola.alunocurso ac on ed47_i_codigo = ac.ed56_i_aluno join escola.alunopossib ap on ac.ed56_i_codigo = ap.ed79_i_alunocurso
    where ed47_i_codigo  = '$cod_aluno' limit 1 ";

    $result = pg_query($conn, $sql_matricula);
    $aluno = pg_fetch_assoc($result);

    if (pg_num_rows($result) == 0) {
        header('Location:index.php?not_found=1');
    }
    else{
        if ($aluno['confirmacao_rematricula'] == true ){
            header('Location:index.php?rematricula=1');
        }
        else{
            $sql_etapaescola = "
            select
                s2.ed11_i_codigo as idserie
            from
                escola.turma t,
                escola.turmaserieregimemat t2,
                escola.serieregimemat s,
                escola.serie s2
            where
                t.ed57_i_escola = {$aluno['idescola']}
                and t.ed57_i_calendario = {$aluno['idcalendario']}
                and t.ed57_i_base = {$aluno['idbase']}
                and t2.ed220_i_turma = t.ed57_i_codigo
                and t2.ed220_i_serieregimemat = s.ed223_i_codigo
                and s.ed223_i_serie = s2.ed11_i_codigo
            order by
                s2.ed11_c_descr desc
            limit 1;";
        
            $result = pg_query($conn, $sql_etapaescola);
            $etapaescola = pg_fetch_assoc($result);
            if($aluno['idserie'] == $etapaescola['idserie']){
                header('Location:index.php?ultimaetapa=1');
            }
            else{
                $_SESSION['codigo'] = $aluno['id_alunoreserva'];
                $_SESSION['matriculado'] = 'false';
                $_SESSION['escola'] = 'true';
                header('Location:rematricula_update.php');
            }   
        }
    }
}
else{
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
        header('Location:index.php?not_found=1');
    }
}

function dateToDatabase($date)
{
    $date = explode('/', $date);
    $date_to_database = "$date[0]-$date[1]-$date[2]";
    return $date_to_database;
}