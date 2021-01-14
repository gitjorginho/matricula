<?php
ini_set('display_errors', 0);
session_start();

$matriculado_sge = $_SESSION['matriculado'];
$escola_disabled = $_SESSION['escola'];
$banner_passo = 0;

require_once('header.php');
require_once('conexao.php');
$conexao = new Conexao();
$conn = $conexao->conn();

$codigo = $_SESSION['codigo'];


$sql_status = "select * from reserva.alunostatusreserva";
$result = pg_query($conn, $sql_status);
$status = pg_fetch_all($result);



$sql_aluno = " select * from reserva.alunoreserva 
join reserva.escolareserva on escolareserva.id_alunoreserva = alunoreserva.id_alunoreserva where  alunoreserva.id_alunoreserva = $codigo ";


$result = pg_query($conn, $sql_aluno);
$aluno = pg_fetch_assoc($result);

//var_dump($aluno);


$data = $aluno["ed47_d_nasc"];
$datando = date("d/m/Y", strtotime($data));
$_SESSION['sdt_nascimento'] = $datando;

// Separa em dia, mês e ano
list($dia, $mes, $ano) = explode('/', $datando);
$mescorte = 03;
$diacorte = 31;
$anocorte = 2020;

// Descobre que dia é hoje e retorna a unix timestamp
$hoje = mktime(0, 0, 0, $mescorte, $diacorte, $anocorte);
// Descobre a unix timestamp da data de nascimento do fulano
$nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

// Depois apenas fazemos o cálculo já citado :)
$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

if ($idade == 1) {
    $sql = ("select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
   inner join turma on ed57_i_escola = ed18_i_codigo
   where ed57_c_descr ilike '%G1%' or ed57_c_descr ilike '%GRUPO 01%'
           order by ed18_c_nome");
} elseif ($idade == 2) {
    $sql = ("select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
   inner join turma on ed57_i_escola = ed18_i_codigo
   where ed57_c_descr ilike '%G2%' or ed57_c_descr ilike '%GRUPO 02%'
           order by ed18_c_nome");
} elseif ($idade == 3) {
    $sql = ("select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
   inner join turma on ed57_i_escola = ed18_i_codigo
   where ed57_c_descr ilike '%G3%' or ed57_c_descr ilike '%GRUPO 03%'
           order by ed18_c_nome");
} elseif ($idade == 4) {
    $sql = ("select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
   inner join turma on ed57_i_escola = ed18_i_codigo
   where ed57_c_descr ilike '%G4%' or ed57_c_descr ilike '%GRUPO 04%'
           order by ed18_c_nome");
} elseif ($idade == 5) {
    $sql = ("select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
   inner join turma on ed57_i_escola = ed18_i_codigo
   where ed57_c_descr ilike '%G5%' or ed57_c_descr ilike '%GRUPO 05%'
           order by ed18_c_nome");
} else {
    $sql = ("select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
   inner join turma on ed57_i_escola = ed18_i_codigo
           order by ed18_c_nome");
}
$result = pg_query($conn, $sql);
$escolas = pg_fetch_all($result);

//Sql  localidade
$sql_localidade = "select * from territorio.localidade";

$result = pg_query($conn, $sql_localidade);
$localidades = pg_fetch_all($result);

//$sql_matricula_reserva = "select * from reserva.alunoreserva where reserva_aluno = {$codigo}";
//$result = pg_query($conn,$sql_matricula_reserva);
//
//if(pg_num_rows($result) == 1){
//    $matricula_reserva = pg_fetch_assoc($result);
//	$turminha = $matricula_reserva ['reserva_turma'];
//	$_SESSION['vch_serie'] = $turminha;
//}

?>

<div class="centr">


    <br>
    <div class="card-body">
        <font color="red"><b>ATENÇÃO:</b></font> <i>Para alteração das informações cadastradas, entre em contato através dos emails
            seduccmie@educa.camacari.ba.gov.br ou seduccmie@camacari.ba.gov.br.</i>
    </div>
    <br>
    <h3 class="text-center">Dados Cadastrais</h3>
    <br>
    <div class="card-body">
        <form method="post" action="registro_update.php" onsubmit="return validaForm()">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <label for="exampleInputEmail1">Código:</label>
                        <input class="form-control" type="text" name="vch_codigo" id="vch_codigo" readonly value="<?php echo $aluno['id_alunoreserva'] ?>" />
                    </div>
                    <div class="col-md-3">
                        <label for="exampleInputEmail1 ">Código SGE:</label>
                        <input class="form-control" type="text" name="vch_codigo_sge" id="vch_codigo" readonly value="<?php echo $aluno['ed47_i_codigo'] ?>" />
                    </div>
                </div>
            </div>
            <div class="form_group">
            <label for="exampleInputEmail1 ">Status:</label>
                        <select disabled class="custom-select" id="alunostatusreserva_id">
                                <?php
                                foreach ($status as $sta) {
                                    echo "<option value='{$sta['id']}'>{$sta['status_descr']}</option>";
                                }
                                ?>
                            </select>
                <script>
                    $('#alunostatusreserva_id').val('<?php echo $aluno['alunostatusreserva_id'] ?>');
                </script>

            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="exampleInputEmail1">Nome:</label>
                        <input required <?php echo ($matriculado_sge == 'true') ? 'readonly' : 'readonly' ?> class="form-control " type="text" name="vch_nome" id="vch_nome" value="<?php echo $aluno['ed47_v_nome'] ?>" onkeyup="this.value = this.value.toUpperCase();" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        <label for="sdt_nascimento">Data Nascimento:</label>
                        <input required <?php echo ($matriculado_sge == 'true') ? 'readonly' : 'readonly' ?> class="form-control" type="text" name="sdt_nascimento" id="sdt_nascimento" value="<?php echo $datando ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="cp_sexo">Sexo:</label>
                        <select required <?php echo ($matriculado_sge == 'true') ? 'readonly="true"' : 'readonly' ?> class="browser-default custom-select" name="vch_sexo" id="cp_sexo">
                            <option selected></option>
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                        <script>
                            $('#cp_sexo').val('<?php echo $aluno['ed47_v_sexo'] ?>');
                        </script>
                    </div>
                    <?php
                    $sql_serie = "select * from serie order by ed11_c_descr";
                    $result = pg_query($conn, $sql_serie);
                    $serie = pg_fetch_all($result);
                    ?>

                    <div class="col-md-3">
                        <label for="cp_serie">Série:</label>
                        <select required class="custom-select" id="cp_serie" name="vch_serie" readonly>
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

                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label>Nome da Mãe:</label>
                        <input required <?php echo ($matriculado_sge == 'true') ? 'readonly' : 'readonly' ?> class="form-control " type="text" name="vch_mae" id="vch_mae" value="<?php echo $aluno['ed47_v_mae'] ?>" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="exampleInputEmail1">Responsável:</label>
                        <input required <?php echo ($matriculado_sge == 'true') ? 'readonly' : 'readonly' ?> class="form-control " type="text" name="vch_responsavel" id="vch_responsavel" value="<?php echo $aluno['ed47_c_nomeresp'] ?>" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="exampleInputEmail1">Email do responsável:</label>
                        <input required <?php echo ($matriculado_sge == 'true') ? 'readonly' : 'readonly' ?> class="form-control " type="text" name="vch_responsavel" id="vch_responsavel" value="<?php echo $aluno['email_resp'] ?>" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputEmail">CPF do Responsável:</label>
                        <input <?php echo ($matriculado_sge == 'true') ? 'readonly' : 'readonly' ?> class="form-control" type="text" name="vch_cpf" id="vch_cpf" value="<?php echo $aluno['ed47_v_cpf'] ?>">

                    </div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="col-md-5">
                        <label for="examleInputEmail1" class="labelNome">Telefone:</label>
                        <input <?php echo ($matriculado_sge == 'true') ? 'readonly' : 'readonly' ?> value="<?php echo $aluno['ed47_v_telef'] ?>" name="vch_telefone" id="vch_telefone" class="form-control" type="text">
                    </div>

                </div>
            </div>

            <br>
            <br>
            <hr>
            <h3 class="text-center">Endereço</h3>
            <br>
            <br>

            Pesquisa de Endereço:
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <input <?php //echo ($matriculado_sge == 'true') ? 'readonly' : 'readonly' 
                                ?> class="form-control" type="text" id="cp_texto" autocomplete="off">
                        <select onclick="pegarValores()" id="resposta" style="width: 500px; margin-left: 0px;display: none" name="vch_endereco" multiple="multiple"></select>
                    </div>
                </div>
            </div>


            <br>
            <br />


            <div class="form-group">

            
                <div class="row">
                    <div class="col-md-9">
                        <label for="exampleInputEmail1">Endereço:</label>
                        <input required value="<?php echo $aluno['ed47_v_ender'] ?>" name="vch_endereco" class="form-control" id="ender" type="text" readonly>
                        <label for="exampleInputEmail1" class="labelNome">Complemento</label>
                        <input required value="<?php echo $aluno['ed47_v_compl'] ?>" vch_complemento class="form-control" type="text" readonly>

                    </div>
                    <div class="col-md-3">
                        <label for="exampleInputEmail1" class="labelNome">Número:</label>
                        <input readonly <?php // echo ($matriculado_sge == 'true') ? 'readonly="true"' : 'readonly' 
                                        ?> required value="<?php echo $aluno['ed47_c_numero'] ?>" name="vch_numero" class="form-control .form-control-nome" type="text">
                        <label for="" class="labelNome">Cep:</label>
                        <input name="vch_cep" value="<?php echo $aluno['ed47_v_cep'] ?>" class="form-control" id="vch_cep" type="text" readonly>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <label for="exampleInputEmail1">Bairro:</label>
                        <input required value="<?php echo $aluno['ed47_v_bairro'] ?>" name="vch_bairro" id="vch_bairro" class="form-control" type="text" readonly>
                        <label for="exampleInputEmail1">Localidade:</label>
                        <select disabled required <?php //echo ($matriculado_sge == 'true') ? 'readonly' : 'readonly' 
                                                    ?> class="custom-select" id="cp_localidades" name="vch_localidade">
                            <option></option>
                            <?php
                            foreach ($localidades as $localidade) {
                                echo '<option value=' . $localidade['loc_i_cod'] . '>' . $localidade['loc_v_nome'] . "</option>";
                            }
                            ?>
                        </select>
                        <script>
                            $('#cp_localidades').val(<?php echo $aluno['ed47_i_localidade'] ?>);
                        </script>

                    </div>

                    <div class="col-md-6">

                        <label for="exampleInputEmail1" class="labelNome">Cidade</label>
                        <input required value="<?php echo $aluno['municipio'] ?>" name="vch_cidade" id="vch_cidade" class="form-control" type="text" readonly>

                    </div>
                </div>

            </div>

            <br>
            <br>
            <hr>
            <h3>Opção de Cadastro de Lista de Espera</h3>
            <br>
            <br>


            <div class="form-group">
                <div class="row">
                    <div class="col-md-9">
                        <label for="">Escola Pretendida</label>
                        <select <?php echo ($matriculado_sge == 'true' && $escola_disabled == 'true') ? 'readonly="true"' : 'readonly' ?> required id="escola" name="escola" class="custom-select">
                            <option readonly value=""></option>
                            <?php foreach ($escolas as $escola) { ?>
                                <option value="<?php echo $escola['codigo'] ?>"><?php echo $escola['escola'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <script>
                    $('#escola').val('<?php echo $aluno['ed56_i_escola'] ?>');
                </script>
            </div>

            <!--
        <div class="form-group">
            <div class="row">
                <div class="col-9">
                    <label for="">Escolha uma turma para o ano letivo 2020:</label>
                    <select  required id="cp_turmas" name="turma" class="custom-select">
                        <option></option>
                        <?php //foreach ($turmas as $turma) {
                        //echo '<option value=' . $turma['ed57_i_codigo'] . '>' . 'Turma: ' . $turma['turma'] . "- Serie: " . $turma['serie'] . '- Turno: ' . $turma['turno'] . "</option>";
                        //  }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <script>
            $('#cp_turmas').val(<?php //echo $matricula_reserva['reserva_turma'] 
                                ?>);
        </script>
      <br>
        <br>-->


            <hr>
            <h3>Comprovante Lista de Espera</h3>
            <br>
            <br>
            <div class="form-group">
                <div class="row">
                    <div class="col-2"></div>
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-success col" href="">
                            Imprimir Comprovante Lista de Espera
                        </button>
                        <!--<button type="submit" class="btn btn-success col" href="">
                        Salvar e Imprimir Comprovante Lista de Espera
                    </button>-->
                    </div>
                    <div class="col-2"></div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="col-2"></div>
                    <div class="col-md-8">
                        <a class="btn btn-secondary col-md-12" href="ficha_cadastro.php">Voltar</a>
                        <!--<button type="submit" class="btn btn-success col" href="">
                        Salvar e Imprimir Comprovante Lista de Espera
                    </button>-->
                    </div>
                    <div class="col-2"></div>
                </div>
            </div>
        </form>
    </div>
    <br>
    <br>
    <br>
    <br>

    <script type="text/javascript">
        function validaForm() {

            //valida data de nascimento
            if (validaDat($('#sdt_nascimento').val())) {
                alert('Data de nascimento incorreta.');
                return false;
            }

            //valida data ver se atual
            if (compareDates($('#sdt_nascimento').val())) {
                alert('Data de nascimento não pode ser maior que data atual.');
                return false;
            }
        }



        function compareDates(date) {
            let parts = date.split('/') // separa a data pelo caracter '/'
            let today = new Date() // pega a data atual

            date = new Date(parts[2], parts[1] - 1, parts[0]) // formata 'date'

            // compara se a data informada é maior que a data atual
            // e retorna true ou false
            return date >= today ? true : false;
        }


        function validaDat(valor) {
            var date = valor;
            var ardt = new Array;
            var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
            ardt = date.split("/");
            erro = false;
            if (date.search(ExpReg) == -1) {
                erro = true;
            } else if (((ardt[1] == 4) || (ardt[1] == 6) || (ardt[1] == 9) || (ardt[1] == 11)) && (ardt[0] > 30))
                erro = true;
            else if (ardt[1] == 2) {
                if ((ardt[0] > 28) && ((ardt[2] % 4) != 0))
                    erro = true;
                if ((ardt[0] > 29) && ((ardt[2] % 4) == 0))
                    erro = true;
            }
            return erro;
        }



        jQuery(function($) {
            $("#vch_telefone").mask("(99) 9 9999-9999");
        });

        jQuery(function($) {
            $("#vch_cpf").mask("999.999.999-99");
        });

        $('#cp_texto').keydown(function() {
            ender = $('#cp_texto').val();
            $('#resposta').show();
            $.ajax({
                    url: "pesq.php",
                    type: 'get',
                    data: {
                        funcao: 'carregar_autocomplete',
                        endereco: ender
                    },
                    beforeSend: function() {}
                })
                .done(function(msg) {
                    $('#resposta').html(msg);
                })
                .fail(function(jqXHR, textStatus, msg) {
                    alert(msg);
                });
        });

        function pegarValores() {
            let valor = $('#resposta :selected').val();

            $.ajax({
                    url: "pesq.php",
                    type: 'get',
                    data: {
                        codigo: valor,
                        funcao: 'carregar_endereco'
                    },
                    beforeSend: function() {}
                })
                .done(function(msg) {
                    let endereco = JSON.parse(msg);
                    $('#vch_bairro').val(endereco.bairro);
                    $('#vch_cidade').val(endereco.cidade);
                    $('#vch_cep').val(endereco.cep);
                    $('#ender').val(endereco.endereco);
                    let codigo_bairro = endereco.codigo_bairro;
                    carregar_localidade(codigo_bairro)

                })
                .fail(function(jqXHR, textStatus, msg) {
                    alert('Requisição Falhou !');
                });

        }

        //carregar localidade

        function carregar_localidade(codigo_bairro) {
            $.ajax({
                    url: "pesq.php",
                    type: 'get',
                    data: {
                        codigo: codigo_bairro,
                        funcao: 'carregar_localidade'
                    },

                    beforeSend: function() {}
                })
                .done(function(msg) {
                    $('#cp_localidades').html(msg);

                })
                .fail(function(jqXHR, textStatus, msg) {
                    alert('Requisição Falhou !');
                });
        }

        $('#escola').change(function() {
            pesq_turmas();
        });

        function pesq_turmas() {
            let escola = $('#escola :selected').val();

            $.ajax({
                    url: "pesq.php",
                    type: 'get',
                    data: {
                        codigo: escola,
                        funcao: 'select_turma'
                    },
                    beforeSend: function() {}
                })
                .done(function(msg) {
                    $('#cp_turmas').html(msg);


                    //let turmas = JSON.parse(msg);

                    // $('#vch_bairro').val(endereco.bairro);
                    // $('#vch_cidade').val(endereco.cidade);
                    // $('#vch_cep').val(endereco.cep);

                })
                .fail(function(jqXHR, textStatus, msg) {
                    alert('Busca de turmas falhou !');
                });

        }
    </script>

    <?php require_once('footer.php'); ?>