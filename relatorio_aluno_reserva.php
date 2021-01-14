<?php

require_once('header.php');
require_once('conexao.php');

$conexao = new Conexao();
$conn = $conexao->conn();

$sql_turmas_aluno = "
select ed18_i_codigo,ed18_c_nome as escola from matriculareserva
inner join turma on  ed57_i_codigo = reserva_turma
inner join escola on ed18_i_codigo = ed57_i_escola 
group by ed18_i_codigo,ed18_c_nome
order by ed18_c_nome 
";
$result = pg_query($conn, $sql_turmas_aluno);

if (pg_num_rows($result) == 0) {
    $escolas = 0;
} else {
    $escolas = pg_fetch_all($result);
}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div class="centr">
    <br>
    <h2 class="text-center">Lista de Aluno com Reserva</h2>

    <?php if ($escolas == 0) {
        echo "<br><br><br><br><h4 class='text-center'>Não existe registro</h4> ";
    } ?>


    <div id="accordion">

        <?php
        if ($escolas != 0) {
            $id = 0;
            foreach ($escolas as $escola) {
                $id++;
                ?>

                <div class="card">
                    <div class="card-header" id="headingOne<?php echo $id; ?>">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse"
                                    data-target="#collapseOne<?php echo $id; ?>"
                                    aria-expanded="true"
                                    aria-controls="collapseOne<?php echo $id; ?>">
                                <?php echo $escola['escola'] ?>
                            </button>
                        </h5>
                    </div>

                    <div id="collapseOne<?php echo $id; ?>" class="collapse hide"
                         aria-labelledby="headingOne<?php echo $id; ?>" data-parent="#accordion">
                        <div class="card-body">

                            <!--Acordion interno-->
                            <div id="accordion<?php echo $id; ?>">

                                <?php
                                $turmas = loadTurmas($conn, $escola['ed18_i_codigo']);


                                if ($turmas != 0) {

                                    $id_interno = 0;
                                    foreach ($turmas as $turma) {
                                        $id_interno++;
                                        $id_ac_interno = $id . $id_interno;
                                        ?>
                                        <div class="card">
                                            <div class="card-header" id="headingOne<?php echo $id_ac_interno ?>">
                                                <h5 class="mb-0">
                                                    <button class="btn btn-link" data-toggle="collapse"
                                                            data-target="#collapseOne<?php echo $id_ac_interno ?>"
                                                            aria-expanded="true"
                                                            aria-controls="collapseOne<?php echo $id_ac_interno ?>">
                                                        <?php echo "<b>Serie:</b> {$turma['serie']} - <b>Turno</b> {$turma['turno']} - <b>Turma</b> {$turma['turma']} " ?>
                                                    </button>
                                                </h5>
                                            </div>
                                            <div id="collapseOne<?php echo $id_ac_interno ?>" class="collapse"
                                                 aria-labelledby="headingOne<?php echo $id_ac_interno ?>"
                                                 data-parent="#accordion<?php echo $id; ?>">
                                                <div class="card-body">
                                                    <!--Corpo do card do acordion 1 TURMAS -->
                                                    <table class="table">


                                                        <tr>
                                                            <th></th>
                                                            <th>Aluno</th>
                                                            <th>Status</th>
                                                            <th>Data Inclusão</th>
                                                            <th>Data Alteração</th>

                                                        </tr>
                                                        <?php

                                                        $alunos = loadAlunos($conn, $turma['ed57_i_codigo']);
                                                        if ($alunos != 0) {

                                                            $num_aluno = 0;
                                                            foreach ($alunos as $aluno) {
                                                                $num_aluno++;
                                                                ?>
                                                                <tr>
                                                                    <th>
                                                                        <small><?php echo $num_aluno ?></small>
                                                                    </th>
                                                                    <td>
                                                                        <small><?php echo $aluno['aluno'] ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <small><?php echo ($aluno['ed60_c_situacao'] != '') ? $aluno['ed60_c_situacao'] : 'CANDIDATO'; ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <small><?php echo date('d-m-Y H:i:s', strtotime($aluno['dataregistro'])) ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <?php

                                                                        if ($aluno['dataatualizacao']) {
                                                                            $data_atualizado = explode('.', $aluno['dataatualizacao']);
                                                                            $data_atualizado = date('d-m-Y H:i:s', strtotime($data_atualizado[0]));
                                                                        }else{
                                                                            $data_atualizado = '';
                                                                        }

                                                                        ?>
                                                                        <small><?php echo $data_atualizado ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <small style="color :red"><?php echo ($aluno['escola'] == '') ? '' : 'Matriculado na ' . $aluno['escola'] ?></small>
                                                                    </td>

                                                                </tr>

                                                            <?php }
                                                        } ?>
                                                    </table>
                                                    <!--END TURMAS-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--fim card turmas-->
                                    <?php }
                                } ?>

                                <div>
                                </div>

                                <div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
        } ?>
    </div>
</body>
</html>


<?php

function loadTurmas($conn, $codigo_escola)
{

    $sql_turmas_escola = "
select  ed57_i_codigo, trim(ed57_c_descr)as turma ,trim(ed11_c_descr) as serie,
case
when ed15_c_nome='TARDE 1' then 'VESPERTINO'
when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
when ed15_c_nome='TARDE 2' then 'VESPERTINO'
when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
when ed15_c_nome='NOITE 1' then 'NOTURNO'
when ed15_c_nome='NOITE 2' then 'NOTURNO'
else ed15_c_nome
end as turno
from turma
 inner join matriculareserva on reserva_turma = ed57_i_codigo 
 inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = turma.ed57_i_codigo
 inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
 inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
 inner join turno tuo on turma.ed57_i_turno = tuo.ed15_i_codigo
 where ed57_i_escola = $codigo_escola
 group by ed57_i_codigo, ed57_c_descr, ed11_c_descr,ed15_c_nome
 order by ed11_c_descr
";

    $result = pg_query($conn, $sql_turmas_escola);
    $turmas = pg_fetch_all($result);

    if (pg_num_rows($result) == 0) {
        return 0;
    }
    return $turmas;
}

function loadAlunos($conn, $codigoTurma)
{
//    $sql="
//select
//
//(select mmr_dataregistro from monitoramentomatriculareserva
//where mmr_acao = 'Inseriu' and mmr_idaluno = ed47_i_codigo
//order by mmr_dataregistro desc
//limit 1
//) as dataregistro,
//
//(select mmr_dataregistro from monitoramentomatriculareserva
//where mmr_acao = 'Atualizou' and mmr_idaluno = ed47_i_codigo
//order by mmr_dataregistro desc
//limit 1
//) as dataatualizacao,
//
//ed60_c_situacao, trim(ed47_v_nome) as aluno
//from matriculareserva
//inner join aluno on ed47_i_codigo = reserva_aluno
//left join matricula on ed60_i_aluno = ed47_i_codigo
//where reserva_turma = $codigoTurma order by reserva_id
//";

    //die($sql);
//    $sql = "
//        select ed60_c_situacao, trim(ed47_v_nome) as aluno from matriculareserva
//    inner join aluno on ed47_i_codigo = reserva_aluno
//    left join matricula on ed60_i_aluno = ed47_i_codigo
//    where reserva_turma = $codigoTurma
//    order by reserva_id
//    ";
    $sql = "
SELECT 
CASE WHEN reserva_turma <> ed60_i_turma THEN (
SELECT 
'Etapa Pedagógica: '||ed11_c_descr||' Turno: '|| CASE WHEN ed15_c_nome='TARDE 1' THEN 'VESPERTINO' WHEN ed15_c_nome='MANHÃ 1' THEN 'MATUTINO' WHEN ed15_c_nome='TARDE 2' THEN 'VESPERTINO' WHEN ed15_c_nome='MANHÃ 2' THEN 'MATUTINO' WHEN ed15_c_nome='NOITE 1' THEN 'NOTURNO' WHEN ed15_c_nome='NOITE 2' THEN 'NOTURNO' ELSE ed15_c_nome END 
||' Turma: '||ed57_c_descr||' Unidade Escolar: '||ed18_c_nome AS matriculado
FROM matricula
INNER JOIN turma ON ed60_i_turma = ed57_i_codigo
LEFT JOIN calendario ON ed57_i_calendario = ed52_i_codigo
INNER JOIN escola ON ed18_i_codigo = ed57_i_escola
INNER JOIN turmaserieregimemat tsrm ON tsrm.ed220_i_turma = turma.ed57_i_codigo
INNER JOIN serieregimemat srm ON srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
INNER JOIN serie s ON srm.ed223_i_serie = s.ed11_i_codigo
INNER JOIN turno tuo ON turma.ed57_i_turno = tuo.ed15_i_codigo
WHERE 
ed52_i_ano = 2020 AND ed60_c_situacao = 'MATRICULADO' AND ed60_i_aluno = ed47_i_codigo

) ELSE '' END AS escola,(
SELECT mmr_dataregistro
FROM monitoramentomatriculareserva
WHERE mmr_acao = 'Inseriu' AND mmr_idaluno = ed47_i_codigo
ORDER BY mmr_dataregistro DESC
LIMIT 1) AS dataregistro, (
SELECT mmr_dataregistro
FROM monitoramentomatriculareserva
WHERE mmr_acao = 'Atualizou' AND mmr_idaluno = ed47_i_codigo
ORDER BY mmr_dataregistro DESC
LIMIT 1) AS dataatualizacao, ed60_c_situacao, TRIM(ed47_v_nome) AS aluno
FROM matriculareserva
INNER JOIN aluno ON ed47_i_codigo = reserva_aluno
LEFT JOIN matricula ON ed60_i_aluno = ed47_i_codigo
WHERE reserva_turma = $codigoTurma
ORDER BY reserva_id

";

    //die($sql);
    $result = pg_query($conn, $sql);
    $alunos = pg_fetch_all($result);

    if (pg_num_rows($result) == 0) {
        return 0;
    }
    return $alunos;
}

require_once('footer.php');
?>


