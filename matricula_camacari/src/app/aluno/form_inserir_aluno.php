<?php
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 30/01/2020
 * Time: 13:47
 */
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../classe/Serie.php');
$serie = new Serie();
$series = $serie->all();

?>
<script>
    title('Aluno');
    subTitle1('Aluno');
    subTitle2('Inserir');
</script>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" style="background:green">
                <h4 style="color: white">Inserir Aluno </h4>
            </div>
            <div class="card-body">
                <!--                <div class="form-group">-->
                <!--                    <div class="row">-->
                <!--                        <div class="col">-->
                <!--                            <label for="exampleInputEmail1">Código:</label>-->
                <!--                            <input class="form-control col-sm-3" type="text" name="vch_codigo" id="vch_codigo" readonly-->
                <!--                                   value=""/>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="exampleInputEmail1">Nome:</label>
                            <input required class="form-control" type="text" name="vch_nome" id="vch_nome"
                                   value="" onblur="TestaNome(this)"
                                   onkeyup="this.value = this.value.toUpperCase();"/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="sdt_nascimento">Data Nascimento:</label>
                            <input required class="form-control" type="text" name="sdt_nascimento" id="sdt_nascimento"
                                   value=""  onblur="TestaData(this)" data-nasc-database="<?php echo $aluno['ed47_d_nasc'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="cp_sexo">Sexo:</label>
                            <select required class="browser-default custom-select" name="vch_sexo" id="cp_sexo">
                                <option selected></option>
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                            </select>

                        </div>
                        <div class="col-md-3">
                            <label for="cp_serie">Série:</label>
                            <select required class="custom-select" id="cp_serie" name="vch_serie">
                                <option value=""></option>
                                <?php foreach ($series as $ser){
                                    $seri = trim($ser['ed11_c_descr']);
                                    ?>
                                    <option value="<?php echo $ser['ed11_i_codigo'] ?>"><?php echo $seri ?></option>
                                <?php }?>
                             </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="exampleInputEmail1">Nome da Mãe:</label>
                            <input required class="form-control" type="text" name="vch_mae" id="vch_mae"
                                   value="" onblur="TestaMae(this)"
                                   onkeyup="this.value = this.value.toUpperCase();">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-9">
                            <label for="exampleInputEmail1">Responsável:</label>
                            <input required class="form-control" type="text" name="vch_responsavel" id="vch_responsavel"
                                   value="" onblur="TestaResponsavel(this)" 
                                   onkeyup="this.value = this.value.toUpperCase();">
                        </div>

                        <div class="col-md-3">
                            <label for="exampleInputEmail1">CPF do Responsável:</label>
                            <input class="form-control" type="text" name="vch_cpf" id="vch_cpf"
                                   value="" onblur="TestaCPF(this)">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="examleInputEmail1">Telefone:</label>
                            <input name="vch_telefone" id="vch_telefone" class="form-control col-2" type="text"
                                   value="">
                        </div>

                    </div>
                </div>


                <hr>
                <h4>Endereço</h4>


                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="">Pesquisa de Endereço</label>
                            <input class="form-control" type="text" id="cp_texto" autocomplete="off">
                            <select class="custom-select" onclick="pegarValores(this)" id="resposta"
                                    style=" margin-left: 0px;display: none"
                                    name="vch_endereco" multiple="multiple"></select>
                        </div>
                    </div>
                </div>
                <br>
                <br/>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="exampleInputEmail1">Endereço:</label>
                            <input required value="" name="vch_endereco" class="form-control" id="ender" type="text" readonly>
                            <label for="exampleInputEmail1">Bairro:</label>
                            <input required value="" name="vch_bairro" id="vch_bairro" class="form-control" type="text" readonly>
                            <label for="exampleInputEmail1">Localidade:</label>
                            <select required class="custom-select" id="cp_localidades" name="vch_localidade">
                                <option selected value=""></option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="exampleInputEmail1">Numero:</label>
                            <input name="vch_numero" value="" id="numero" class="form-control col-md-5" type="text">
                            <label for="">Cep:</label>
                            <input name="vch_cep" value="" class="form-control" id="vch_cep" type="text" readonly>
                            <label for="exampleInputEmail1">Cidade</label>
                            <input required name="vch_cidade" id="vch_cidade" class="form-control" type="text" readonly>
                        </div>
                    </div>

                </div>
<!--                <hr>-->
<!--                <h4>Turma</h4>-->
<!--                <div class="form-group">-->
<!--                    <label for="">Escola</label>-->
<!--                    <select onclick="loadTurmas()" class="custom-select" name="" id="cp_escolas">-->
<!---->
<!--                    </select>-->
<!--                </div>-->
<!--                <div class="form-group">-->
<!--                    <label for="">Turma</label>-->
<!--                    <select class="custom-select" name="" id="cp_turmas">-->
<!---->
<!--                    </select>-->
<!--                </div>-->

                <div class="form-group row">
                    <div class="col-4"></div>
                    <div class="col-4">
                        <button onclick="storeAluno()" class="btn btn-outline-primary col">Salvar</button>
                    </div>
                    <div class="col-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="msg_secesso_modal">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Portal de Matricula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="status_modal"></p>
                <p id="msg_modal">Dados do aluno salvo com sucesso !</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script src="js/aluno/form_inserir_aluno.js"></script>

<script>

    // loadEscolas()
</script>

