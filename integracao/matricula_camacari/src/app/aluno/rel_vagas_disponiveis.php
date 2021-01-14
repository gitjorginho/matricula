<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
include("../../../../conexao.php");

//verifica se usuario ta logado
if (!isset($_SESSION['id_usuario'])) {
    echo 'expirou';
    die();
}

$conexao = new Conexao();
$conn = $conexao->conn();

/* Antigo código.. 
  $sql_escola = ("SELECT ed18_i_codigo,ed18_c_nome FROM escola
  --where ed18_i_codigo NOT IN (101,45,20,92,87,47,19,70,73,84,40,32,17,63,18,60)
  order by ed18_c_nome ");
 */
$sql_escola = "select 
              ed18_i_codigo, 
              ed18_c_nome
              from escola E
              inner join configuracoes.db_depart DD on 
              E.ed18_i_codigo = DD.coddepto 
              where limite >= now() or limite is null 
              order by ed18_c_nome";

$result = pg_query($conn, $sql_escola);
$escolas = pg_fetch_all($result);
?>

<script>
    title('Relatorio Vagas Disponíveis');
    subTitle1('Aluno');
    subTitle2('Relatório');
</script>

<div class="row">
    <div class="col">
        <div id="accordion">
            <!--INICIO DO CARD  -->
            <div class="card">
                <div class="card-header" style="background:green">
                    <h4 style="color: white">Relatório Vagas Disponíveis </h4>
                </div>
                <div class="card-body">
                    <?php
                    $cont_name = 0;
                    foreach ($escolas as $escola) {
                        $cont_name++;
                        $sql_etapa = "
                            select
                                T.ed57_i_turno, 
                                T.ed57_i_codigo,
                                trim(ed31_c_descr) as base,
                                trim(ed11_c_descr) as etapa,
                                ed336_vagas as vagas, 
                                (
                                select
                                    count(*) matriculados
                                from escola
                                join turma on ed57_i_escola = ed18_i_codigo
                                join matricula on ed60_i_turma = ed57_i_codigo
                                join turmaserieregimemat on ed220_i_turma = ed57_i_codigo
                                join serieregimemat on ed220_i_serieregimemat = ed223_i_codigo
                                join serie as S2 on ed223_i_serie = ed11_i_codigo
                                join calendario on ed52_i_codigo = ed57_i_calendario
                                where 
                                        ed57_i_escola = {$escola['ed18_i_codigo']} and 
                                        S2.ed11_i_codigo = S.ed11_i_codigo and  
                                        turma.ed57_i_codigo = T.ed57_i_codigo
                                        and ed52_i_ano = '2020'  
                                and ed57_i_tipoturma = 1 and ed60_c_situacao in ('MATRICULADO')
                                )as matriculados,
                                (
                                select 
                                        count(*)reservados 
                                from reserva.alunoreserva
                                join reserva.escolareserva on escolareserva.id_alunoreserva = alunoreserva.id_alunoreserva 
                                where ed221_i_serie = S.ed11_i_codigo and ed56_i_escola = E.ed18_i_codigo
                                )as reservados
                            from escola.escola E  
                            join escola.turma T on T.ed57_i_escola = E.ed18_i_codigo
                            join escola.turno TU on TU.ed15_i_codigo = T.ed57_i_turno 
                            join escola.base B  on B.ed31_i_codigo = T.ed57_i_base 
                            join escola.turmaturnoreferente on ed336_turma = ed57_i_codigo
                            join escola.turmaserieregimemat TSR on TSR.ed220_i_turma = T.ed57_i_codigo
                            join escola.serieregimemat SR on SR.ed223_i_codigo = TSR.ed220_i_serieregimemat 
                            join escola.serie S on S.ed11_i_codigo = ed223_i_serie 
                            join escola.calendario on ed52_i_codigo = ed57_i_calendario
                            where 
                                ed57_i_escola = {$escola['ed18_i_codigo']} and  
                                ed52_i_ano = '2020' and 
                                ed57_i_tipoturma = 1 
                            group by 
                                trim(ed31_c_descr), trim(ed11_c_descr), 
                                T.ed57_i_codigo,
                                S.ed11_i_codigo,
                                E.ed18_i_codigo,T.ed57_i_turno,
                                ed336_vagas
                            order by base, etapa"; 
                        //die($sql_etapas);
                        
                        $result = pg_query($conn, $sql_etapa);
                        $sql_etapa = pg_fetch_all($result);
                        if (pg_num_rows($result) != 0) {
                            ?>
                            <div class="card-header" id="<?php echo 'heading' . $cont_name ?> ">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="<?php echo '#collapse' . $cont_name ?>"
                                            aria-expanded="false" aria-controls="<?php echo 'collapse' . $cont_name ?>">
                                                <?php
                                                echo trim($escola['ed18_c_nome']);
                                                ?>
                                    </button>
                                </h5>
                            </div>
                            <div id="<?php echo 'collapse' . $cont_name ?>" class="collapse hide" aria-labelledby="<?php echo 'heading' . $cont_name ?>" data-parent="#accordion">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <td colspan="5" scope="col" style="text-align: center"><b>Turmas Normais</b></td>
                                        </tr>
                                        <tr>
                                            <th scope="col">Serviços</th>
                                            <th scope="col">Etapa</th>
                                            <th scope="col" class="text-center">Vagas</th>
                                            <th scope="col" class="text-center">Alunos Matriculados</th>
                                            <th scope="col" class="text-center">Vagas Disponíveis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_vagas_disponiveis = 0;
                                        $etapaInicio = '';
                                        $tipo_etapa = '';
                                        $arrBase = array();
                                        $arrEtapa = array();
                                        $arrEstapaVaga = array();
                                        $arrEstapaMatriculados = array();
                                        $arrEstapaReservador = array();
                                        $cont=0;
                                        foreach ($sql_etapa as $etapa) {
                                            // Alex pediu para retirar do calculo das reservas. 
                                            //$total_vagas_disponiveis += $serie['vagas'] - ($serie['matriculados'] + $serie['reservados']);
                                            $total_vagas_disponiveis += $etapa['vagas'] - $etapa['matriculados'];
                                            
                                            if ($etapaInicio != $etapa['etapa']){
                                                $etapaInicio = $etapa['etapa'];
                                                $arrBase[$cont]                    = trim($etapa['base']);
                                                $arrEtapa[$cont]                    = trim($etapa['etapa']);
                                                $arrEstapaVaga[$cont]               = ($etapa['vagas']);
                                                $arrEstapaMatriculados[$cont]       = ($etapa['matriculados']);
                                                $arrEstapaReservador[$cont]         = ($etapa['reservados']);
                                                $cont = $cont + 1;
                                            }else
                                            {
                                                $arrEstapaVaga[$cont - 1]           = ($arrEstapaVaga[$cont - 1] + ($etapa['vagas']));
                                                $arrEstapaMatriculados[$cont - 1]   = ($arrEstapaMatriculados[$cont - 1] + ($etapa['matriculados']));                                                
                                                $arrEstapaReservador[$cont - 1]     = ($arrEstapaReservador[$cont - 1] + ($etapa['reservados']));
                                            } 
                                        }    
                                        for ($i = 0; $i < $cont; $i++) {
                                            echo "<tr>";
                                            echo "<td style='font-size: 13px'>" . $arrBase[$i] . "</td>";
                                            echo "<td class='text-center'>" . $arrEtapa[$i] . "</td>";
                                            echo "<td class='text-center'>" . $arrEstapaVaga[$i] . "</td>";
                                            echo "<td class='text-center'>" . $arrEstapaMatriculados[$i] . "</td>";
                                            //echo "<td class='text-center'>" . $arrEstapaReservador[$i] . "</td>";
                                            echo "<td class='text-center'>" . ($arrEstapaVaga[$i] - $arrEstapaMatriculados[$i]) . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4"><b>Total de Vagas Disponiveis:</b></td>
                                            <td colspan="4" class="text-center"><b><?php echo $total_vagas_disponiveis ?></b></td>
                                        </tr>
                                    </tfoot>
                                    </tbody>
                                </table>
                                <?php
                                $sql_multietapa = "select 
                                                          B.ed31_c_descr as base, 
                                                          ed57_i_codigo as codturma, 
                                                          trim(ed57_c_descr) turma, 
                                                          sum(ed336_vagas) vagas,
                                                              (
                                                               select count(*) from matricula
                                                               where ed60_i_turma = T.ed57_i_codigo and ed60_c_situacao = 'MATRICULADO' and ed60_d_datamatricula >= '2020-01-01'
                                                               ) as matriculados
                                                          from escola.turma T
                                                          join escola.base B on B.ed31_i_codigo  = T.ed57_i_base 
                                                          join escola.turmaturnoreferente on ed336_turma = ed57_i_codigo
                                                          join escola.calendario on ed52_i_codigo = ed57_i_calendario
                                                          where ed57_i_escola = {$escola['ed18_i_codigo']} and 
                                                          ed57_i_tipoturma  not in (1) and 
                                                          ed52_i_ano = 2020
                                                           group by ed31_c_descr,ed57_c_descr,ed57_i_codigo";
                                $result = pg_query($conn, $sql_multietapa);
                                $turmas_multietapa = pg_fetch_all($result);
                                if ($turmas_multietapa != false) {
                                ?>    
                                <table class="table" >
                                    <thead>
                                        <tr>
                                            <th colspan="5" scope="col" style="text-align: center">Turmas Especiais</th>
                                        </tr>
                                        <tr>
                                            <th scope="col">Serviços</th>
                                            <th scope="col">Etapa/Etapas</th>
                                            <th scope="col" class="text-center">Vagas</th>
                                            <th scope="col" class="text-center">Alunos Matriculados</th>
                                            <th scope="col" class="text-center">Vagas Disponíveis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $tipo_base = '';
                                    $total_vagas_disponiveis = 0;
                                    $etapaEspecialInicio = '';
                                    $arrBaseTurmaEspecial = array();
                                    $arrVagasTurmaEspecial = array();
                                    $arrEtapaTurmaEspecial = array();
                                    $arrMatriculadosTurmaEspecial = array();
                                    $cont = 0;
                                    foreach ($turmas_multietapa as $turmas) {
                                        $total_vagas_disponiveis += $turmas['vagas'] - $turmas['matriculados'];
                                        $sqlConsulta = "select 
                                        trim(ed11_c_descr) as etapa 
                                        from escola.turma T
                                        join escola.turmaserieregimemat TSRM on TSRM.ed220_i_turma = T.ed57_i_codigo
                                        join escola.serieregimemat SRN on SRN.ed223_i_codigo = TSRM.ed220_i_serieregimemat
                                        join escola.serie S on S.ed11_i_codigo = SRN.ed223_i_serie
                                        join escola.calendario C on C.ed52_i_codigo = T.ed57_i_calendario 
                                        join escola.base B on B.ed31_i_codigo = T.ed57_i_base
                                        where 
                                        T.ed57_i_codigo = {$turmas['codturma']}  and 
                                        T.ed57_i_escola = {$escola['ed18_i_codigo']} and  
                                        C.ed52_i_ano = '2020'";

                                        //die($sqlConsulta);
                                        //echo $sqlConsulta; 
                                        $rsConsulta = pg_query($conn, $sqlConsulta);
                                        $linharsConsulta = pg_fetch_all($rsConsulta);
                                        $etapaEspecial = '';
                                        if ($linharsConsulta != false){
                                            foreach ($linharsConsulta as $linha) {
                                                $etapaEspecial = $etapaEspecial . $linha['etapa'] . "/";
                                            }
                                            $etapaEspecial = substr($etapaEspecial, 0, -1);

                                            if ($etapaEspecialInicio != $etapaEspecial) {
                                                $etapaEspecialInicio = $etapaEspecial;
                                                $arrBaseTurmaEspecial[$cont] = trim($turmas['base']);
                                                $arrEtapaTurmaEspecial[$cont] = ($etapaEspecial);
                                                $arrVagasTurmaEspecial[$cont] = ($turmas['vagas']);
                                                $arrMatriculadosTurmaEspecial[$cont] = ($turmas['matriculados']);
                                                $cont = $cont + 1;
                                            } else {
                                                $arrVagasTurmaEspecial[$cont - 1] = ($arrVagasTurmaEspecial[$cont - 1] + ($turmas['vagas']));
                                                $arrMatriculadosTurmaEspecial[$cont - 1] = ($arrMatriculadosTurmaEspecial[$cont - 1] + ($turmas['matriculados']));
                                            }
                                        }    
                                    }
                                    for ($i = 0; $i < $cont; $i++) {
                                    if ($arrBaseTurmaEspecial[$i] != $tipo_base) {

                                        echo "<td style='font-size: 13px'>" . $arrBaseTurmaEspecial[$i] . "</td>";
                                        $tipo_base = $arrBaseTurmaEspecial[$i];
                                    } else {
                                        echo "<td style='font-size: 13px'></td>";
                                    }
                                    echo "<td style='font-size: 13px'>" . $arrEtapaTurmaEspecial[$i] . "</td>";
                                    echo "<td class='text-center'>" . $arrVagasTurmaEspecial[$i] . "</td>";
                                    echo "<td class='text-center'>" . $arrMatriculadosTurmaEspecial[$i] . "</td>";
                                    echo "<td class='text-center'>" . ($arrVagasTurmaEspecial[$i] - $arrMatriculadosTurmaEspecial[$i]) . "</td>";
                                    echo "</tr>";
                                    }
                                    
                                    ?>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4"><b>Total de Vagas Disponiveis:</b></td>
                                            <td colspan="4" class="text-center"><b><?php echo $total_vagas_disponiveis ?></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" ><b>Consulta Gerada em  <?php echo date("d-m-Y H:i:s"); ?></b></td>                                 </tr>
                                        </tr>
                                    </tfoot>
                                    </tbody>
                                </table>
                                <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <!--FIM DO CARD  -->
        </div>
    </div>
</div>
