<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../../classe/Conn.php');
Conn::conect();

$codigo_escola = $_GET['escola'];
if ($codigo_escola == 0) {
    $where_escola = 'where limite >= now() or limite is null ';
} else {
    $where_escola = 'where ed18_i_codigo = ' . $codigo_escola;
}

$sql_escolas = " 
    select 
        E.ed18_i_codigo, 
        trim(E.ed18_c_nome) as escola 
    from escola E
    inner join configuracoes.db_depart DD on 
        E.ed18_i_codigo = DD.coddepto 
    $where_escola  
    order by ed18_c_nome";
//echo $sql_escolas;
$stmt = Conn::$conexao->prepare($sql_escolas);
$stmt->execute();
$escolas = $stmt->fetchALL();
?>

<div id="accordion">
    <?php
    $acordion = 0;
    $total_geral_vagas_disponiveis = 0;
    $total_geral_vagas_ofertadas = 0;
    $total_geral_matriculas = 0;
    $total_geral_alunos_reservados = 0;

    foreach ($escolas as $escola) {
        ?>

        <div class="card">
            <div class="card-header" id="headingTwo<?php echo $acordion ?>">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo<?php echo $acordion ?>" aria-expanded="false" aria-controls="collapseTwo">
                        <?php echo trim($escola['escola']); ?>
                    </button>
                </h5>
            </div>
            <div id="collapseTwo<?php echo $acordion ?>" class="collapse" aria-labelledby="headingTwo<?php echo $acordion ?>" data-parent="#accordion">
                <div class="card-body">
                    <?php
/*
$sql_segmentos = "
select 
ed57_i_escola,
ed31_c_descr as segmento, 
sum(ed336_vagas) as vagas, 
(SELECT count(*) FROM turma
join matricula on ed60_i_turma = ed57_i_codigo
join base as b  on ed57_i_base = b.ed31_i_codigo
join calendario on ed52_i_codigo = ed57_i_calendario
WHERE b.ed31_c_descr = base.ed31_c_descr  and ed57_i_tipoturma = 1 and  ed57_i_escola = {$escola['ed18_i_codigo']} and  ed52_i_ano = 2020 and ed60_c_situacao in ('MATRICULADO','TRANSFERIDO REDE') and ed60_d_datamatricula >= '2020-01-01'
) as matriculados
from turma 
join turmaturnoreferente on ed336_turma  = ed57_i_codigo
join base on ed57_i_base = ed31_i_codigo
join calendario on ed52_i_codigo = ed57_i_calendario 
where ed57_i_escola = {$escola['ed18_i_codigo']}  and ed52_i_ano = 2020 and ed57_i_tipoturma =1
group by ed31_c_descr ,ed57_i_escola
order by ed31_c_descr     
";
*/
        $sql_segmentos = "select
T.ed57_i_escola,
ed31_c_descr as segmento,
sum(ed336_vagas) as vagas,
(
SELECT count(*) FROM turma as T1
join matricula as MA on MA.ed60_i_turma = T1.ed57_i_codigo
join base as B2 on B2.ed31_i_codigo = T1.ed57_i_base
join cursoedu as CE2 on CE2.ed29_i_codigo = B2.ed31_i_curso
join calendario C2 on C2.ed52_i_codigo = T1.ed57_i_calendario
WHERE B2.ed31_c_descr = B.ed31_c_descr and
ed60_c_situacao in ('MATRICULADO') and
T1.ed57_i_escola = {$escola['ed18_i_codigo']} and C2.ed52_i_ano = 2020
and B2.ed31_c_ativo = 'S'
) as matriculados
from turma as T
join turmaturnoreferente TTR on TTR.ed336_turma = T.ed57_i_codigo
join base as B on B.ed31_i_codigo = T.ed57_i_base
join cursoedu CE on CE.ed29_i_codigo = B.ed31_i_curso
join calendario as C on C.ed52_i_codigo = T.ed57_i_calendario
where ed57_i_escola = {$escola['ed18_i_codigo']} and ed52_i_ano = 2020
group by  ed31_c_descr ,ed57_i_escola
order by ed31_c_descr";
        
        //die($sql_segmentos);
                    $stmt = Conn::$conexao->prepare($sql_segmentos);
                    $stmt->execute();
                    $segmentos = $stmt->fetchALL();
                    if (count($segmentos) == 0) {
                        echo '<h4 class="text-center">Nenhum registro encontrado.</h4>';
                    } else {
                        ?>

                        <table class="table text-center">
                            <thead style="background: lightgrey">
                            <tr >
                                <td><small>Seg</small></td>
                                <td><small>Vagas Ofert.</small></td>
                                <td><small>Matr.</small></td>
                                <td><small>Alunos Reser.</small></td>
                                <td><small>Vagas Dispon.</small></td>
                            </tr>

                            </thead>
                            <tbody>
                            <?php
                            $total_vagas_ofertadas = 0;
                            $total_matriculas = 0;
                            $total_alunos_reservados = 0;
                            $total_vagas_disponiveis = 0;
                            foreach ($segmentos as $segmento) {
                                //conta quantos aluno estao reservados
                                $aluno_reservado = alunosReserva($segmento['segmento'], $escola['ed18_i_codigo']);

                                // soma todas as vagas disponiveis
                                if (($segmento['vagas'] - $segmento['matriculados']) >= 0) {
                                    $total_vagas_disponiveis += ($segmento['vagas'] - $segmento['matriculados']);
                                    $total_geral_vagas_disponiveis += ($segmento['vagas'] - ($segmento['matriculados']));
                                }
                                //soma todas as vagas ofertdas
                                $total_vagas_ofertadas += $segmento['vagas'];
                                $total_matriculas += $segmento['matriculados'];
                                $total_alunos_reservados += $aluno_reservado;
                                //soma total geral
                                $total_geral_vagas_ofertadas += $segmento['vagas'];
                                $total_geral_matriculas += $segmento['matriculados'];
                                $total_geral_alunos_reservados  += $aluno_reservado;



                                ?>
                                <tr>
                                    <td><?php echo $segmento['segmento'] ?></td>
                                    <td><?php echo $segmento['vagas'] ?></td>
                                    <td><?php echo $segmento['matriculados'] ?></td>
                                    <td><?php echo $aluno_reservado ?> </td>
                                    <!--<td><?php 
                                    // Pedido de Alex, retirar a reserva do calculo
                                    //            echo $segmento['vagas'] - ($segmento['matriculados'] + $aluno_reservado); 
                                            ?> 
                                    </td>-->
                                    <td><?php echo $segmento['vagas'] - $segmento['matriculados']; ?> </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr style="background: lightgrey">
                                <th></th>
                                <th><?php echo $total_vagas_ofertadas ?></th>
                                <th><?php echo $total_matriculas ?></th>
                                <th><?php echo $total_alunos_reservados ?></th>
                                <th><?php echo $total_vagas_disponiveis ?></th>
                            </tr>
                            </tfoot>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php $acordion++;
    } ?>

</div>
<div class="card">
    <div class="card-body">
        <table class="table text-center">
            <thead style="background: lightgrey">
            <tr>

                <td><b><small>Total Vagas Ofertadas</small></b></td>
                <td><b><small>Total Matri.</small></b></td>
                <td><b><small>Total Alunos Reser.</small></b></td>
                <td><b><small>Total Vagas Dispon.</small></b></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $total_geral_vagas_ofertadas?></td>
                <td><?php echo $total_geral_matriculas?></td>
                <td><?php echo $total_geral_alunos_reservados?></td>
                <td><?php echo $total_geral_vagas_disponiveis?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
function alunosReserva($segmento, $codigo_escola)
{
    $aluno_reservado = 0;
    switch (trim($segmento)) {
        case'ANOS INICIAIS':
            $sql_reservado = "
                 select count(*) as reservado from reserva.alunoreserva
                    join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                    where alunostatusreserva_id not in (8,9,10,11,12) and ed221_i_serie in (24,23,25,29,30) and ed56_i_escola = $codigo_escola
                 ";
            $stmt = Conn::$conexao->prepare($sql_reservado);
            $stmt->execute();
            $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
            $aluno_reservado = $aluno_reservado['reservado'];
            break;
        case'ANOS FINAIS':
            $sql_reservado = "
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where alunostatusreserva_id not in (8,9,10,11,12) and ed221_i_serie in (33,34,35,36) and ed56_i_escola = $codigo_escola
                 ";
            $stmt = Conn::$conexao->prepare($sql_reservado);
            $stmt->execute();
            $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
            $aluno_reservado = $aluno_reservado['reservado'];
            break;

        case'PRE-ESCOLA':
//                 $sql_reservado ="
//                 select count(*) as reservado from reserva.alunoreserva
//                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva
//                 where ed221_i_serie in (31,32)
//                 ";
//                 $stmt = Conn::$conexao->prepare($sql_reservado);
//                 $stmt->execute();
//                 $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
//                 $aluno_reservado = $aluno_reservado['reservado'];
            $aluno_reservado = 0;
            break;

        case'PRE-ESCOLA':
            $sql_reservado = "
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where alunostatusreserva_id not in (8,9,10,11,12) and ed221_i_serie in (31,32) and ed56_i_escola = $codigo_escola
                 ";
            $stmt = Conn::$conexao->prepare($sql_reservado);
            $stmt->execute();
            $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
            $aluno_reservado = $aluno_reservado['reservado'];
            break;
        case'CRECHE':
            $sql_reservado = "
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where alunostatusreserva_id not in (8,9,10,11,12) and ed221_i_serie in (26,27,28) and ed56_i_escola = $codigo_escola
                 ";
            $stmt = Conn::$conexao->prepare($sql_reservado);
            $stmt->execute();
            $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
            $aluno_reservado = $aluno_reservado['reservado'];
            break;

        case'EJA - ANOS INICIAIS':
            $sql_reservado = "
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where alunostatusreserva_id not in (8,9,10,11,12) and ed221_i_serie in (37,38,39) and ed56_i_escola = $codigo_escola
                 ";
            $stmt = Conn::$conexao->prepare($sql_reservado);
            $stmt->execute();
            $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
            $aluno_reservado = $aluno_reservado['reservado'];
            break;
        case'EJA - ANOS FINAIS':
            $sql_reservado = "
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where alunostatusreserva_id not in (8,9,10,11,12) and ed221_i_serie in (40,41) and ed56_i_escola = $codigo_escola
                
                 ";
            $stmt = Conn::$conexao->prepare($sql_reservado);
            $stmt->execute();
            $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
            $aluno_reservado = $aluno_reservado['reservado'];
            break;
    }

    return $aluno_reservado;
}

?>
