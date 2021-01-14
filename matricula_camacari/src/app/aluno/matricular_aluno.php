<?php
session_start();
require_once('../classe/Aluno.php');
$obj_aluno = new Aluno();
if (isset($_GET['matriculado'])) {
    $aluno = $obj_aluno->getAlunoMatriculado($_GET['codigo']);
}else{
    $aluno = $obj_aluno->getAluno($_GET['codigo']);
}


$_SESSION['cp_serie'] = $aluno['reserva_turma'];
//die (var_dump($_POST));
clearstatcache();
?>
<script>
    title('Aluno')
    subTitle1('Aluno');
    subTitle2('Matricular');
</script>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" style="background:green">
                <h4 style="color: white">Matricular Aluno </h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="exampleInputEmail1">Código:</label>
                            <input class="form-control" type="text" name="vch_codigo" id="vch_codigo" readonly
                                   value="<?php echo $aluno['ed47_i_codigo'] ?>"/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="exampleInputEmail1">Nome:</label>
                            <input readonly required class="form-control" type="text" name="vch_nome" id="vch_nome"
                                   value="<?php echo $aluno['ed47_v_nome'] ?>"
                                   onkeyup=""/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="sdt_nascimento">Data Nascimento:</label>
                            <input readonly required class="form-control" type="text" name="sdt_nascimento" id="sdt_nascimento"
                                   value="  <?php echo date('d/m/Y', strtotime($aluno['ed47_d_nasc'])) ?>" data-nasc-database="<?php echo $aluno['ed47_d_nasc'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="cp_sexo">Sexo:</label>
                            <input readonly type="text" class="form-control" value="<?php echo (strtoupper($aluno['ed47_v_sexo']) == 'M') ? 'Masculino' : 'Feminino' ?>">
                        </div>

                        <div class="col-md-2">
                            <label for="cp_serie">Série:</label>
                            <input readonly required class="form-control" type="text" name="vch_serie" id="cp_serie"
                                   value="<?php echo strtoupper(utf8_encode($aluno['ed11_c_descr'])) ?>" onkeyup="">
                        </div>


                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="exampleInputEmail1">Nome da Mãe:</label>
                            <input readonly required class="form-control" type="text" name="vch_mae" id="vch_mae"
                                   value="<?php echo strtoupper($aluno['ed47_v_mae']) ?>"
                                   onkeyup="this.value = this.value.toUpperCase();">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-9">
                            <label for="exampleInputEmail1">Responsável:</label>
                            <input readonly required class="form-control" type="text" name="vch_responsavel" id="vch_responsavel"
                                   value="<?php echo strtoupper($aluno['ed47_c_nomeresp']) ?>" onkeyup="">
                        </div>

                        <div class="col-md-3">
                            <label for="exampleInputEmail1">CPF do Responsável:</label>
                            <input readonly class="form-control" type="text" name="vch_cpf" id="vch_cpf"
                                   value="<?php echo $aluno['reserva_cpfresponsavel'] ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="examleInputEmail1">Telefone:</label>
                            <input readonly name="vch_telefone" id="vch_telefone" class="form-control col-md-2" type="text"
                                   value="<?php echo $aluno['ed47_v_telef'] ?>">
                        </div>

                    </div>
                </div>


                <hr>
                <h4>Endereço</h4>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="exampleInputEmail1">Endereço:</label>
                            <input readonly required value="<?php echo $aluno['ed47_v_ender'] ?>" name="vch_endereco" class="form-control" id="ender" type="text" readonly>
                            <label for="exampleInputEmail1">Bairro:</label>
                            <input readonly required value="<?php echo $aluno['ed47_v_bairro'] ?>" name="vch_bairro" id="vch_bairro" class="form-control" type="text" readonly>
                            <label for="exampleInputEmail1">Localidade:</label>
                            <input readonly type="text" class="form-control" value="<?php echo $aluno['loc_v_nome'] ?>">

                        </div>
                        <div class="col">
                            <label for="exampleInputEmail1">Numero:</label>
                            <input readonly name="vch_numero" class="form-control col-md-5" type="text">
                            <label for="">Cep:</label>
                            <input readonly name="vch_cep" value="<?php echo $aluno['ed47_v_cep'] ?>" class="form-control" id="vch_cep" type="text" readonly>
                            <label for="exampleInputEmail1">Cidade</label>
                            <input readonly required value="<?php echo $aluno['cidade'] ?>" name="vch_cidade" id="vch_cidade" class="form-control" type="text" readonly>
                        </div>
                    </div>

                </div>
                <hr>
                <h4>Turma</h4>
                <div class="form-group">
                    <label for="">Escola</label>
                    <?php if (!isset($_GET['matriculado'])){ ?>

                    <select onclick="loadTurmas()" onchange="validacao()" class="custom-select" name="" id="cp_escolas"> </select>
                    <?php }else{ ?>
                        <input readonly class="form-control" type="text" value="<?php echo trim(utf8_encode($aluno['ed18_c_nome'])) ?>">
                    <?php }?>
                </div>
                <div class="form-group">
                    <label for="">Turma <img id="loading_turma" style="display: none" src="img/loading.gif" width="20"> </label>
                    <?php if (!isset($_GET['matriculado'])){ ?>

                    <select disabled onchange="loadDadosTurma();" class="custom-select" name="" id="cp_turmas"> </select>
                    <?php }else{ ?>
                        <input readonly class="form-control" type="text" value="<?php echo 'Turma: '.trim(utf8_encode($aluno['ed11_c_descr'])).' - Serie:'.trim(utf8_encode($aluno['ed57_c_descr'])).' - Turno: '.trim(utf8_encode($aluno['ed15_c_nome'])); ?>">
                    <?php }?>
                </div>
                <div class="form-group row">
                    <div class="col">
                        <label for="">Curso<img class="loading_dados_turma" style="display: none" src="img/loading.gif" width="20"></label>
                        <input readonly type="text" id="cp_curso" class="form-control">
                    </div>
                    <div class="col">
                        <label for="">Base Curricular <img class="loading_dados_turma" style="display: none" src="img/loading.gif" width="20"></label>
                        <input readonly type="text" id="cp_base" class="form-control">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col">
                        <label for="">Calendário<img class="loading_dados_turma" style="display: none" src="img/loading.gif" width="20"></label>
                        <input readonly type="text" id="cp_calendario" class="form-control">
                    </div>
                    <div class="col">
                        <label for="">Etapa<img class="loading_dados_turma" style="display: none" src="img/loading.gif" width="20"></label>
                        <input readonly type="text" id="cp_etapa" class="form-control">
                    </div>
                    <div class="col">
                        <label for="">Turno<img class="loading_dados_turma" style="display: none" src="img/loading.gif" width="20"></label>
                        <input readonly type="text" id="cp_turno" class="form-control">
                    </div>

                </div>

                <div class="form-group row">
                    <div class="col">
                        <label for="">Vagas Turma<img class="loading_dados_turma" style="display: none" src="img/loading.gif" width="20"></label>
                        <input readonly type="text" id="cp_vagas_turma" class="form-control">

                    </div>
                    <div class="col col-sm-12">
                        <label for="">Alunos Matriculados<img class="loading_dados_turma" style="display: none" src="img/loading.gif" width="20"></label>
                        <input readonly type="text" id="cp_matriculado" class="form-control">

                    </div>
                    <div class="col">
                        <label for="">Vagas Disponíveis<img class="loading_dados_turma" style="display: none" src="img/loading.gif" width="20"></label>
                        <input readonly type="text" id="cp_vagas_disp" class="form-control">
                    </div>

                    <?php
                    if (isset($_GET['matriculado'])){ ?>
                        <input  type="hidden" id="cp_visu_cod_turma" value="<?php echo $aluno['ed57_i_codigo']?>">
                        <input type="hidden" id="cp_visu_cod_escola" value="<?php echo $aluno['ed18_i_codigo']?>">
                    <?php }?>

                </div>
                <?php if (!isset($_GET['matriculado'])) { ?>
                    <div class="form-group" id="">
                        <button disabled class="btn btn-success offset-md-4 col-md-4 " id="cp_btn_matricular" onclick="matricular()"><span id="cp_text_btn">Matricular</span><img id="loading_btn_matricula" style="display: none" src="img/loading.gif" width="20"></button>
                    </div>
                    <div class="form-group">
                        <button style="display: none" class="btn btn-outline-info offset-md-4  col-md-4 " id="cp_btn_comprovante" onclick="viewComprovante()">Imprimir Comprovante</button>
                    </div>

                <?php } else { ?>
                    <div class="form-group">
                        <button class="btn btn-outline-info offset-md-4  col-md-4 " onclick="viewComprovante()">Imprimir Comprovante</button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal_msg_matricula">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Portal de Matrícula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="msg_modal_matricula"></p>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-dismiss="modal">ok</button>

            </div>
        </div>
    </div>
</div>





<script type="text/javascript" src="js/aluno/form_matricula_aluno.js"></script>

<script>
    loadEscolas();


    //$('#cp_turmas).val();
</script>


<?php
if (isset($_GET['matriculado'])){
    echo '<script>
        loadDadosTurmaVisualizar();    
    </script>';
}
?>