<?php
session_start();
require_once('../../classe/Conn.php');
header("Content-Type: text/html;  charset=ISO-8859-1", true);
Conn::conect();

$sql_atualiza = 'select reserva.atualizaCodigoGAluno()'; 
$stmt = Conn::$conexao->prepare($sql_atualiza);
$stmt->execute();


$total_aluno =0; 

$sql_relatorio ='select '
                .'alunoreserva.id_alunoreserva  as "ID DO PORTAL",' 
                .'alunoreserva.ed47_i_codigo as "COD DO SGE",' 
                .'('
                .'select ed60_d_datamatricula from matricula '
                ."      where ed60_i_aluno = alunoreserva.ed47_i_codigo and ed60_c_situacao = 'MATRICULADO'"  
                ."      order by ed60_d_datamatricula desc "  
                ."      limit 1"
                .') as "DATA DA MATRICULA" ,'
                ."("
                ."select ed18_c_codigoinep from matricula"
                ."      join turma on ed57_i_codigo = ed60_i_turma "  
                ."      join escola on ed57_i_escola  = ed18_i_codigo "
                ."      where ed60_i_aluno = alunoreserva.ed47_i_codigo and ed60_c_situacao = 'MATRICULADO' "  
                ."      order by ed60_d_datamatricula desc " 
                ."      limit 1"
                .') as "COD. INEP",'
                ."("
                ."select ed18_c_nome from matricula "
                ."      join turma on ed57_i_codigo = ed60_i_turma"  
                ."      join escola on ed57_i_escola  = ed18_i_codigo"
                ."      where ed60_i_aluno = alunoreserva.ed47_i_codigo and ed60_c_situacao = 'MATRICULADO'"  
                ."      order by ed60_d_datamatricula desc " 
                ."      limit 1 "
                .') as "ESCOLA",'
                ."("
                ."select trim(ed11_c_descr) from matricula "
                ."      join turma on ed57_i_codigo = ed60_i_turma "  
                ."      join turmaserieregimemat on ed220_i_turma = ed57_i_codigo"
                ."      join serieregimemat on ed220_i_serieregimemat = ed223_i_codigo "
                ."      join serie on ed223_i_serie = ed11_i_codigo "
                ."      where ed60_i_aluno = alunoreserva.ed47_i_codigo and ed60_c_situacao = 'MATRICULADO' "  
                ."      order by ed60_d_datamatricula desc " 
                ."      limit 1"
                .') as "SERIE", '
                .'trim(alunoreserva.ed47_v_nome) as "NOME DO ALUNO", '
                .'alunoreserva.ed47_d_nasc as "DATA DE NASCIMENTO" , '
                .'alunoreserva.ed47_v_mae as "NOME DA MAE" ,'
                .'alunoreserva.ed47_c_nomeresp as "NOME DO RESPONSAVEL", '
                .'aluno.ed47_d_cadast as "DATA DO CADASTRO NO SGE",'
                ."(select to_char( data_modificacao,'DD/MM/YYYY HH24:MI:SS') from reserva.auditoriausuarioaluno where auditoriausuarioaluno.id_alunoreserva  = alunoreserva.id_alunoreserva
                order by data_modificacao desc limit 1
              ) as \"DATA_MODIFICACAO\""
                ."from reserva.alunoreserva " 
                ."join reserva.escolareserva on escolareserva.id_alunoreserva = alunoreserva.id_alunoreserva " 
                ."join escola.escola on ed18_i_codigo = ed56_i_escola "
                ."join aluno ON aluno.ed47_i_codigo = reserva.alunoreserva.ed47_i_codigo "
                ."where alunoreserva.alunostatusreserva_id = 8 "
                ."order by (escola, alunoreserva.ed47_v_nome) asc  ";

//die($sql_relatorio);

$stmt = Conn::$conexao->prepare($sql_relatorio);
$stmt->execute();
$reserva_alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

download_csv_results($reserva_alunos, 'AlunosMatriculadosPortal.csv');    

function dateToView($data)
{
    if ($data != '') {
        list($ano, $mes, $dia) = explode('-', $data);
        $ano = trim($ano);
        $mes = trim($mes);
        $dia = trim($dia);
        return "$dia/$mes/$ano";
    }
    return '';
}

function dateToDatabase($data)
{
    list($dia, $mes, $ano) = explode('/', $data);
    $ano = trim($ano);
    $mes = trim($mes);
    $dia = trim($dia);
    return "$ano-$mes-$dia";
}

function download_csv_results($results, $name)
{            
    header('Content-Type: text/csv; charset=ISO-8859-1');
    header('Content-Disposition: attachment; filename='. $name);
    header('Pragma: no-cache');
    header("Expires: 0");

    $outstream = fopen("php://output", "wb");    
    fputcsv($outstream, array_keys($results[0]),";");

    foreach($results as $result)
    {
        fputcsv($outstream, $result,";");
    }
    

    fclose($outstream);
}