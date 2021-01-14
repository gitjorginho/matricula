<?php
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 30/01/2020
 * Time: 13:47
 */
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo 'expirou';
    die();
}

header("Content-Type: text/html;  charset=ISO-8859-1", true);

require_once('../classe/Aluno.php');
require_once('../classe/Escola.php');
require_once('../classe/Serie.php');
require_once('../classe/Matricula.php');
require_once('../classe/Auditoria.php');
require_once('../classe/Agendamento.php');

// inicializando variável
$dataMatriculatemp = isset($dataMatriculatemp) ? $dataMatriculatemp : '';
//verifica se usuario ta logado

$obj_aluno = new Aluno();
$status = $obj_aluno->allStatus();

$obj_matricula = new Matricula();

$Agendamento = new Agendamento();
$agendamento = $Agendamento->getHorarioAgendamento();

if (isset($_GET['codigo'])) {
    $_SESSION['paginacao'] = $_GET['paginacao'];
    $aluno = $obj_aluno->getAluno($_GET['codigo']);
    $matricula = $obj_matricula->getMatricula(@$aluno['ed47_i_codigo']);
    if (isset($matricula['ed60_d_datamatricula'])) {
        $dataMatriculatemp = date('d/m/Y', strtotime(@$matricula['ed60_d_datamatricula']));
    }
    if (isset($aluno['ed47_d_agedamento'])){
      $dataAgendamento      = date('d/m/Y', strtotime($aluno['ed47_d_agedamento']));    
      $horarioAgendamento   = date('H:i', strtotime($aluno['ed47_d_agedamento']));    
    }
    else{
          $horarioAgendamento = '08:00';
    }

    $Escola = new Escola();
    $escolas = $Escola->loadEscolasSerie($aluno['ed221_i_serie']);
    
    $usuarioMatricula = $obj_matricula->getUsuarioMatricula(@$aluno['ed47_i_codigo']);
    // Retorna a auditoria sobre o cadastro do registro da lista de espera    
    $auditoriaCadastro = new Auditoria();
    $auditoriasCadastrados = $auditoriaCadastro->getAuditoriaCadastro(@$aluno['id_alunoreserva']);
    // Retorna a auditoria sobre a alteração do registro da lista de espera
    $auditoria = new Auditoria();
    $auditorias = $auditoria->getAuditoria(@$aluno['id_alunoreserva']);
}

//var_dump($usuarioMatricula);
//exit;
$serie = new Serie();
$series = $serie->all();
?>

<head>
    <link href="../../../../matricula/css/style.css" rel="stylesheet" type="text/css" />
    <!-- style Administração -->
    <link rel="stylesheet" href="css/dist/css/adminlte.min.css">    
    <!-- Daterange picker -->
    <link rel="stylesheet" href="css/plugins/daterangepicker/daterangepicker.css">
</head>
<script>
    title('Aluno');
    subTitle1('Aluno');
    subTitle2('Alterar');
</script>
<script>
$('.date').daterangepicker({
        "locale": {
    "format": "DD/MM/YYYY",
    "separator": " - ",
    "applyLabel": "Aplicar",
    "cancelLabel": "Cancelar",
    "daysOfWeek": [
      "Dom",
      "Seg",
      "Ter",
      "Qua",
      "Qui",
      "Sex",
      "Sab"
    ],
    "monthNames": [
      "Janeiro",	
      "Fevereiro",
      "Março",
      "Abril",
      "Maio",
      "Junho",
      "Julho",
      "Agosto",
      "Setembro",
      "Outubro",
      "Novembro",
      "Dezembro"
    ],
    "firstDay": 1
  },
    singleDatePicker: true,
    //showDropdowns: true,
    minYear: 2019,
    maxYear: parseInt(moment().format('YYYY'),10)
  });
</script>

<!-- daterangepicker -->
<script src="css/plugins/moment/moment.min.js"></script>
<!-- daterangepicker -->
<script src="css/plugins/daterangepicker/daterangepicker.js"></script>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" style="background:green">
                <h4 style="color: white">Alterar Aluno </h4>
            </div>
            <div class="card-body">
                <div class="tabbable tabbable-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1_1" data-toggle="tab"><b>Cadastro</b></a></li>&nbsp;&nbsp;&nbsp;&nbsp;
                        <li><a href="#tab_1_2" data-toggle="tab"><b>Histórico</b></a></li>&nbsp;&nbsp;&nbsp;&nbsp;
                        <li><a href="#tab_1_3" data-toggle="tab"><b>Matrícula no SGE</b></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1_1">
                            <br>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <label for="vch_codigo" id="labelCodigo">Código:</label>
                                        <input class="form-control" type="text" name="vch_codigo" id="vch_codigo" readonly value="<?php echo trim(@$aluno['id_alunoreserva']) ?>" />
                                    </div>
                                    <?php if (($aluno['ed47_d_agedamento'] != '') && ($aluno['alunostatusreserva_id']) == 7){ ?> 
                                    <div class="col-md-8">
                                        <label></label>
                                        <p style="text-align: center; color: red;">Matrícula Agendada <br> Dia <?php echo @date('d/m/Y H:i', strtotime($aluno['ed47_d_agedamento'])); ?></p>
                                    </div>
                                    <?php 
                                    } 
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label for="vch_nome" id="labelNome">Nome:</label>
                                        <input class="form-control" type="text" name="vch_nome" id="vch_nome" value="<?php echo @$aluno['ed47_v_nome'] ?>" onkeyup="this.value = this.value.toUpperCase();" onKeyPress=";return letras();" />
<!--                                        mudarCorCampo('labelNome', 'vch_nome')-->
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="sdt_nascimento" id="labelDataNascimento">Data Nascimento:</label>
                                        <input required class="form-control" type="text" name="sdt_nascimento" id="sdt_nascimento" value="  <?php echo @date('d/m/Y', strtotime($aluno['ed47_d_nasc'])) ?>" data-nasc-database="<?php echo $aluno['ed47_d_nasc'] ?>" onKeyPress="mudarCorCampo('labelDataNascimento', 'sdt_nascimento')">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="cp_sexo" id="labelSexo">Sexo:</label>
                                        <select class="browser-default custom-select" name="vch_sexo" id="cp_sexo" onchange="mudarCorCampo('labelSexo', 'cp_sexo');">
                                            <option selected></option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Feminino</option>
                                        </select>
                                        <script>
                                            $('#cp_sexo').val('<?php echo @strtoupper($aluno['ed47_v_sexo']) ?>');
                                        </script>
                                    </div>
                                    <?php
                                    $series = new Serie();
                                    $serie = $serie->all();
                                    ?>

                                    <div class="col-md-2">
                                        <label for="cp_serie" id="labelSerie">Série:</label>
                                        <select required class="custom-select" id="cp_serie" name="vch_serie" onchange="MandaID('serie','AjaxRetornaEscola',$('#cp_serie').val())">
                                            <option selected></option>
                                            <?php
                                            foreach ($serie as $serie_selec) {
                                                echo '<option value=' . $serie_selec['ed11_i_codigo'] . '>' . $serie_selec['ed11_c_descr'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <script>
                                            $('#cp_serie').val('<?php echo $aluno['ed221_i_serie'] ?>');
                                        </script>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="">Status do Aluno</label>
                                        <select class="custom-select" id="alunostatusreserva_id">
                                            <?php
                                            foreach ($status as $sta) {
                                                echo "<option value='{$sta['id']}'>{$sta['status_descr']}</option>";
                                            }
                                            ?>
                                        </select>
                                        <script>
                                            $('#alunostatusreserva_id').val(<?php echo $aluno['alunostatusreserva_id'] ?>);
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label for="vch_orgaopublico" id="labelOrgaoPublico">Orgão Público:</label>
                                        <input class="form-control" type="text" name="vch_orgaopublico" id="vch_orgaopublico" value="<?php echo trim(@strtoupper($aluno['vch_orgaopublico'])) ?>" onkeyup="this.value = this.value.toUpperCase();" onKeyPress="mudarCorCampo('labelOrgaoPublico', 'vch_orgaopublico')">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label for="vch_mae" id="labelNomeMae">Nome da Mãe:</label>
                                        <input class="form-control" type="text" name="vch_mae" id="vch_mae" value="<?php echo trim(@strtoupper($aluno['ed47_v_mae'])) ?>" onkeyup="this.value = this.value.toUpperCase();" onKeyPress="mudarCorCampo('labelNomeMae', 'vch_mae');return letras();">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-9">
                                        <label for="vch_responsavel" id="labelNomeResponsavel">Responsável:</label>
                                        <input class="form-control" type="text" name="vch_responsavel" id="vch_responsavel" value="<?php echo trim(@strtoupper($aluno['ed47_c_nomeresp'])) ?>" onkeyup="this.value = this.value.toUpperCase();" onKeyPress="mudarCorCampo('labelNomeResponsavel', 'vch_responsavel');return letras();">


                                        <label for="vch_responsavel" id="labelEmailResponsavel">Email do Responsável:</label>
                                        <input class="form-control" type="text" name="emailResponsavel" id="emailResponsavel" value="<?php echo trim($aluno['email_resp']) ?>">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="vch_cpf" id="labelCpf">CPF do Responsável:</label>
                                        <input class="form-control" type="text" name="vch_cpf" id="vch_cpf" value="<?php echo @$aluno['ed47_v_cpf'] ?>" onKeyPress="mudarCorCampo('labelCpf', 'vch_cpf')">
                                        <label for="vch_telefone" id="labelTelefone">Telefone:</label>
                                        <input name="vch_telefone" id="vch_telefone" class="form-control" type="text" value="<?php echo @$aluno['ed47_v_telef'] ?>" onKeyPress="mudarCorCampo('labelTelefone', 'vch_telefone')">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                    </div>
                                </div>
                            </div>
                            <b>UNIDADE ESCOLAR:</b></label>
                            <div id="RetornaEscola" >
                                <label  id="labelEscola" style="color: red"></label>
                                <select required id="cp_escola" name="escola" class="custom-select">
                                    <option value="">Selecione uma escola</option>
                                    <?php foreach ($escolas as $escola) { ?>
                                        <option value="<?php echo $escola['codigo'] ?>"><?php echo $escola['escola'] ?></option>
                                    <?php } ?>
                                </select>
                                <script>
                                    $('#cp_escola').val(<?php echo $aluno['ed56_i_escola'] ?>);
                                </script>
                            </div>    
                            <hr>
                            <h4>Endereço</h4>
<!--  <div class="form-group"> -->
<!--                                <div class="row">-->
<!--                                    <div class="col">-->
<!--                                        <label for="">Pesquisa de Localidade</label>-->
<!--                                        <input class="form-control" type="text" onkeyup="find_ender()" id="cp_localidade" name="cp_localidade" autocomplete="off">-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->

                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label for="cp_texto" id="labelEndereco">Pesquisa de Endereço:</label>
                                        <input class="form-control" type="text"  onkeyup="find_ender()" id="cp_texto" name="cp_texto" autocomplete="off">
                                        <select  style="font-size: 11px"  class="custom-select" onclick="pegarValores(this)" id="resposta" style=" margin-left: 0px;display: none" name="vch_endereco" multiple="multiple" onchange="mudarCorCampo('labelEndereco', 'cp_texto')"></select>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <br />
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-9">
                                        <label for="exampleInputEmail1">Endereço:</label>
                                        <input required value="<?php echo @$aluno['ed47_v_ender'] ?>" name="vch_endereco" class="form-control" id="ender" type="text" readonly>
                                        <label for="complemento">Complemento:</label>
                                        <input required value="<?php echo @$aluno['ed47_v_compl'] ?>" name="vch_compl" class="form-control" id="compl" type="text">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">Numero:</label>
                                        <input name="vch_numero" id="vch_numero" value="<?php echo @$aluno['ed47_c_numero'] ?>" class="form-control " type="text" onkeypress="//return onlynumber();">
                                        <label for="">Cep:</label>
                                        <input name="vch_cep" id="vch_cep" value="<?php echo @$aluno['ed47_v_cep'] ?>" class="form-control" type="text" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="exampleInputEmail1">Bairro:</label>
                                        <input required value="<?php echo @$aluno['ed47_v_bairro'] ?>" name="vch_bairro" id="vch_bairro" class="form-control" type="text" readonly>
                                        <label for="exampleInputEmail1">Localidade:</label>
                                        <select required class="custom-select" id="cp_localidades" name="vch_localidade">
                                            <option selected value="<?php echo @$aluno['ed47_i_localidade'] ?>"><?php echo @$aluno['loc_v_nome'] ?></option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="exampleInputEmail1">Cidade</label>
                                        <input required data-codigo-cidade="<?php echo @$aluno['ed47_i_censomunicend'] ?>" value="<?php echo @$aluno['cidade'] ?>" name="vch_cidade" id="vch_cidade" class="form-control" type="text" readonly>
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="">Observação:</label>
                                <textarea class="form-control" name="" id="observacao" cols="1" rows="5"><?php echo trim($aluno['observacao']); ?></textarea>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12 text-center">
                                    <button onclick="lastList(<?php echo $_GET['paginacao'] ?>)" class="btn btn-outline-secondary col-md-2">Voltar</button>
                                    <button data-aluno-id="<?php echo trim($aluno['id_alunoreserva']) ?>" onclick="showComprovanteAluno(this)" class="btn btn-outline-info col-md-2">Comprovante</button>
                                    <button onclick="showModalUpdateConfirmation()" id="btn_salvar" class="btn btn-outline-success col-md-2">Salvar</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_1_2">
                            <br>
                            <div class="form-group">
                                <?php
                                require('../aluno/ajax/historico_AJAX.php');
                                ?>    
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12 text-center">
                                    <button onclick="lastList(<?php echo $_GET['paginacao'] ?>)" class="btn btn-outline-secondary col-md-2">Voltar</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_1_3">
                            <br>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <label for="vch_codigo_aba3" id="labelCodigo_aba3">Código:</label>
                                        <input class="form-control" type="text" name="vch_codigo_aba3" readonly value="<?php echo trim(@$aluno['id_alunoreserva']) ?>" />
                                    </div>
                                    <div class="col">
                                        <label for="vch_nome_aba3" id="labelNome_aba3">Nome:</label>
                                        <input class="form-control" type="text" name="vch_nome_aba3" readonly value="<?php echo @$aluno['ed47_v_nome'] ?>" />
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label for="vch_cod_matricula" id="labelCodigo">Matrícula Número:</label>
                                        <input class="form-control" type="text" name="vch_cod_matricula" id="vch_cod_matricula" readonly value="<?php echo @$matricula['ed60_i_codigo'] ?>"/>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="vch_dt_matricula" id="labelNome">Data da Matrícula:</label>
                                        <input class="form-control" type="text" name="vch_dt_matricula" id="vch_dt_matricula" readonly value="<?php echo $dataMatriculatemp ?>"/>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="vch_usu_matricula" id="labelNome">Matrícula Efetivada Por:</label>
                                        <input class="form-control" type="text" name="vch_usu_matricula" id="vch_usu_matricula" readonly value="<?php echo @$usuarioMatricula['nome'] ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="vch_escola" id="labelNome">Unidade Escolar:</label>
                                        <input class="form-control" type="text" name="vch_escola" id="vch_escola" readonly value="<?php echo @$matricula['escola'] ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="vch_etapa_pedagogica" id="labelNome">Etapa pedagógica:</label>
                                        <input class="form-control" type="text" name="vch_etapa_pedagogica" id="vch_etapa_pedagogica" readonly value="<?php echo @$matricula['serie'] ?>"/>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="vch_turma" id="labelNome">Turma:</label>
                                        <input class="form-control" type="text" name="vch_turma" id="vch_tp_turma" readonly value="<?php echo @$matricula['ed57_c_descr'] ?>"/>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="vch_turno" id="labelNome">Turno:</label>
                                        <input class="form-control" type="text" name="vch_turno" id="vch_turno" readonly value="<?php echo @$matricula['turno'] ?>"/>
                                    </div> 
                                </div>
                            </div> 
                            <div class="form-group row">
                                <div class="col-md-12 text-center">
                                    <button onclick="lastList(<?php echo $_GET['paginacao'] ?>)" class="btn btn-outline-secondary col-md-2">Voltar</button>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>    
            </div>

        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="msg_secesso_modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Portal de Matricula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="form-group" id="DataAgendamento">
                <div class="row">
                    <div class="col" style="text-align: center">
                        <label for="Agendamento">Agendamento</label><br>        
                    </div>
                </div>
                <div class="row">
                    <div class="col" style="text-align: center">
                        <label for="Agendamento">Data:</label><br>        
                    </div>
                </div>
                <div class="row" style="text-align: center">
                    <div class="col-md-4 offset-md-4">
                        <input class="form-control date " style="text-align:center;" type="text" id="date_agendamento" value="<?php echo $dataAgendamento; ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col" style="text-align: center">
                        <label for="HorarioAgendamento">Horário:</label><br>        
                    </div>
                </div>
                <div class="row" style="text-align: center">
                    <div class="col-md-4 offset-md-4">
                        <select class="browser-default custom-select"  name="vch_horario_agenda" id="vch_horario_agenda">
                            <?php    
                            foreach ($agendamento as $agendamento_selec) {
                                echo '<option value=' . $agendamento_selec['re006_v_horarioagendamento'] . '>' . $agendamento_selec['re006_v_horarioagendamento'] . "</option>";
                            }
                            ?>

                        </select>
                        <script>
                            $('#vch_horario_agenda').val('<?php echo $horarioAgendamento; ?>');
                        </script>
                    </div>
                </div>

            </div>
            <div id="NotificacaoEscola">
            </div>
        </div>
    </div>
</div>



<button id="msg" type="button" style="display: none" class="btn btn-primary" data-toggle="modal" data-target="#modalExemplo">
    Abrir modal de demonstração
</button>
<!-- Modal -->
<div class="modal fade" id="modalExemplo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Portal Lista de Espera </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="msg_text"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" data-backdrop="static">OK</button>
            </div>
        </div>
    </div>
</div>

<!--Modal que pergunta se o usuario quer alterar-->
<button id="msg" type="button" style="display: none" class="btn btn-primary" data-toggle="modal" data-target="#modalExemplo">
    Abrir modal de demonstração
</button>
<!-- Modal -->
<div class="modal" id="modalConfirmaAlteracao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Portal Lista de Espera </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="msg_text">Tem certeza que deseja alterar estes dados?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="updateAluno()" id="modal-btn-sim">Sim</button>
                <button type="button" class="btn btn-success" id="modal-btn-nao" onclick="lastList(<?php echo $_GET['paginacao'] ?>)">Não</button>
                <!--  <button type="button" class="btn btn-success" data-dismiss="modal" data-backdrop="static" >OK</button> -->
            </div>
        </div>
    </div>
</div>


