<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
session_start();
//verifica se usuario ta logado
if (!isset($_SESSION['id_usuario'])) {
    echo 'expirou';
    die();
}

$volarEdicao = isset($_SESSION['voltaredicao']) ? $_SESSION['voltaredicao'] : '';
$codAluno = isset($_GET['cod_aluno']) ? $_GET['cod_aluno'] : '';
            
require('../classe/Aluno.php');
$aluno = new Aluno();
$alunos = $aluno->findAluno($_GET['aluno'], $codAluno, $_GET['responsavel'], $_GET['data_nascimento'], $_GET['status_id'], $_GET['offset']);
clearstatcache();
//var_dump($_GET['status_id']);
?>
<table class="table">
    <thead style="background: lightgray">
        <tr>
            <th><small>Código</small></th>
            <th><small>Aluno</small></th>
            <th class="smart-phone"><small>Responsável</small></th>
            <th class="smart-phone"><small>Data Nasc</small></th>
            <th class="smart-phone"><small>Série</small></th>
            <th class="smart-phone"><small>Status</small></th>
            <th class="smart-phone"><small>Comp.</small></th>
            <th class="smart-phone"><small>Aut.</small></th>
            <th class="smart-phone"><small>Ação<small></th>
        </tr>
    </thead>
    <tbody id="table_alunos">
    <?php
        $qtd_aluno = 0;
        foreach ($alunos as $aluno) {
            $qtd_aluno += 1;
    ?>
        <tr>
            <td><small><?php echo trim($aluno['id_alunoreserva']) ?></small></td>
            <td><small><?php echo trim($aluno['ed47_v_nome']) ?></small></td>
            <td class="smart-phone"><small><?php echo trim($aluno['ed47_c_nomeresp']) ?></small></td>
            <td class="smart-phone"><small><?php echo date('d/m/Y', strtotime($aluno['ed47_d_nasc'])) ?></small></td>
            <td><small><?php echo trim($aluno['ed11_c_descr']) ?></small></td>
            <td><small><?php echo trim($aluno['status_descr']) ?></small></td>

    <?php 
            //if ($aluno['ed60_d_datamatricula']) {
    ?>
        <!--<td class="smart-phone"><i class="fas fa-check-circle" style="color:green" ></i><small><span class="smart-phone">Matr?culado</span></small></td>
            <td>
                <div class="row">
                   <div class="col-6">
                        <button class="btn btn-outline-secondary btn-sm" onclick="getForm('app/aluno/form_alterar_aluno.php?codigo=<?php //echo $aluno['ed47_i_codigo']  ?>')">Editar</button>
                    </div>
                     <!-- <div class="col-6">
                          <button class="btn btn-outline-info btn-sm" onclick="getForm('app/aluno/matricular_aluno.php?codigo=<?php //echo $aluno['ed47_i_codigo']  ?>&matriculado=')">Visualizar</button>
                      </div>                             
                </div>
            </td>-->
    <?php //}else{ ?>
            <td> <img style="cursor: pointer;" data-aluno-id="<?php echo trim($aluno['id_alunoreserva']) ?>" onclick="showComprovanteAluno(this)" src="img/pdf.png" title="Reimpressão do Comprovante" width="20"></td>
            <?php 
            if (($aluno['alunostatusreserva_id'] == 7) && ($aluno['ed47_v_codigoseguranca'] !='')){
                echo "<td> <img style='cursor: pointer;' data-aluno-id=".trim($aluno['id_alunoreserva'])." onclick='showAutorizaçãoMatricula(this)' src='img/pdf.png' title='Reimpressão da Autorização Matrícula' width='20'></td>";
            }else{
                echo "<td> <img  src='img/pdf_off.png' title='Sem Autorização Matrícula' width='20'></td>";   
            } 
            ?>
            <td>
                <div class="row">
                    <div class="col-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="getForm('app/aluno/form_alterar_aluno.php?codigo=<?php echo strval($aluno['id_alunoreserva']) . '&paginacao=' . strval($_GET['offset']) ?>')">Editar</button>
                    </div>
                </div>
            </td>
    <?php //} ?>
            </tr>
    <?php
            }
            // TESTA SE O USUÁRIO ESTÁ RETORNANDO À PÁGINA. 
            
            if ($volarEdicao != true) {
                // É PRÓXIMA PÁGINA
                if ($_GET['funcao'] == 'findAlunoProximo') {
                    $_SESSION['registros'] = $_SESSION['registros'] + $qtd_aluno;
                    if ($qtd_aluno <= 40) {
                        $_SESSION['qtd_alunofim'] = $qtd_aluno;
                    } else {
                        $_SESSION['qtd_alunofim'] = '';
                    }
                // É PRÓXIMA ANTERIOR
                } elseif ($_GET['funcao'] == 'findAlunoAnterior') {
                    if ($_SESSION['qtd_alunofim'] != '') {
                        $qtd_aluno = $_SESSION['qtd_alunofim'];
                        $_SESSION['qtd_alunofim'] = '';
                    }
                    $_SESSION['registros'] = $_SESSION['registros'] - $qtd_aluno;
                    $qtd_aluno =  $_SESSION['registros'];
                // É INÍCIO
                } elseif ($_GET['funcao'] == 'findAlunoInicio') {
                    if ($qtd_aluno <= 40) {
                        $_SESSION['registros'] = $qtd_aluno;
                    } else {
                        $_SESSION['registros'] = 40;
                    }
                // É FIM
                } elseif ($_GET['funcao'] == 'findAlunoFim') {
                    if ($qtd_aluno <= 40) {
                        $_SESSION['registros'] = $qtd_aluno + $_GET['offset'];
                        $_SESSION['qtd_alunofim'] = $qtd_aluno;
                    } else {
                        $_SESSION['qtd_alunofim'] = '';
                    }
                } else {
                    $_SESSION['registros'] = $qtd_aluno;
                }
            } else {
                $_SESSION['voltaredicao'] = false;
                if ($_GET['offset'] == 0){ 
                    $_SESSION['registros'] = $qtd_aluno;
                }else{
                    
                    $_SESSION['registros'] = 0; 
                    $_SESSION['registros'] = ($_GET['offset'])  + $qtd_aluno;
                }    
            }    
            ?>
   </tbody>
    <tfoot>
        <tr style="background: lightgray">
            <td></td>
            <td colspan="1" class="text-left" ><i><b> ( <i ><?php echo $_SESSION['registros'] . ' de ' . $_SESSION['total_registros']; ?></i> )Registros</b></i></td>
            <td></td>
            <td></td>
            <td colspan="5">
                <ul class="pagination pagination-sm  justify-content-md-end">
                    <button <?php echo $_GET['offset'] == 0 ? 'disabled' : ''; ?> class="page-link" data-page="0" id="cp_prev_page" onclick="findAlunoInicio()" href="#"><<</button> 
                    <button  <?php echo $_GET['offset'] == 0 ? 'disabled' : ''; ?>  class="page-link" data-page="<?php echo $_GET['offset'] ?>" id="cp_start_page" onclick="findAlunoAnterior()" href="#">Anterior</button>
    <?php
//                  echo ("Quantidade de Registros:");
//                  var_dump($_SESSION['registros']);
//                  echo ("Voltando:");
//                  var_dump($_SESSION['voltaredicao']);
    ?>
                    <button  <?php echo $qtd_aluno < 40 ? 'disabled' : ''; ?> class="page-link" data-page="<?php echo $_GET['offset'] ?>" id="cp_next_page" onclick="findAlunoProximo()" href="#">Próximo</button>
                    <button  <?php echo $qtd_aluno < 40 ? 'disabled' : ''; ?> class="page-link" data-page="<?php echo $_SESSION['total_registros']; ?>" id="cp_end_page" onclick="findAlunoFim()" href="#">>></button>
                </ul>
            </td>
        </tr>
    </tfoot>
</table>
