<?php
ini_set('display_errors', 0);
session_start();
require_once('config.php');




;//$_SESSION['matriculado'];
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



$sql_aluno = "select 
(select true from docaluno where ed49_i_aluno = ed47_i_codigo limit 1) as pendencia_doc_sge,
(select true from confirmacaorematricula where edu01_aluno = ed47_i_codigo) as confirmacao_rematricula,
(
	select (case when ed60_i_codigo = null then false else true end) matriculado
	from matricula 
	inner join turma on ed57_i_codigo = ed60_i_turma
	inner join calendario on ed57_i_calendario = ed52_i_codigo
	where ed60_i_aluno = reserva.alunoreserva.ed47_i_codigo and ed52_i_ano = 2020 and ed60_c_situacao in ('MATRICULADO', 'APROVADO')
) as matriculado,
alunoreserva.*
from reserva.alunoreserva 
join reserva.escolareserva on escolareserva.id_alunoreserva = alunoreserva.id_alunoreserva
where  alunoreserva.id_alunoreserva = $codigo ";
$result = pg_query($conn, $sql_aluno);
$aluno = pg_fetch_assoc($result);






$data = $aluno["ed47_d_nasc"];
$datando = date("d/m/Y", strtotime($data));
$_SESSION['sdt_nascimento'] = $datando;

// Separa em dia, m�s e ano
list($dia, $mes, $ano) = explode('/', $datando);
$mescorte = 03;
$diacorte = 31;
$anocorte = 2020;

// Descobre que dia � hoje e retorna a unix timestamp
$hoje = mktime(0, 0, 0, $mescorte, $diacorte, $anocorte);
// Descobre a unix timestamp da data de nascimento do fulano
$nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

// Depois apenas fazemos o c�lculo j� citado :)
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

//carregar tipos de documento do banco
// $sql_documento = "select *  from reserva.documentoreserva dr
// join documentacao as ds on dr.ed02_i_codigo = ds.ed02_i_codigo
// where dr.id_documentoreserva  not in(
//     select id_documentoreserva  from reserva.documentoalunoreserva where id_alunoreserva = $codigo
//     )";

//carregar tipos de documento do banco



$sql_documento = "
select ed02_c_descr, reserva.documentoreserva.* 
from docaluno
join documentacao on ed49_i_documentacao = documentacao.ed02_i_codigo
join reserva.documentoreserva on reserva.documentoreserva.ed02_i_codigo = documentacao.ed02_i_codigo
where ed49_i_aluno = {$aluno['ed47_i_codigo']} and id_documentoreserva not in (select id_documentoreserva from reserva.documentoalunoreserva where id_alunoreserva = {$aluno['id_alunoreserva']})
";

$result = pg_query($conn,$sql_documento);
$documentos =  pg_fetch_all($result);

//die(var_dump($documentos))

?>

<div class="">


    <br>
    <div class="card-body">
        <font color="red"><b>ATEN��O:</b></font> <i>Para altera��o das informa��es cadastradas, entre em contato atrav�s dos emails
            seduccmie@educa.camacari.ba.gov.br ou seduccmie@camacari.ba.gov.br.</i>
    </div>
    <br>
    <h3 class="text-center">Dados Cadastrais</h3>
    <br>
    <div class="card-body">
        <form method="post"  enctype="multipart/form-data" action="registro_update.php">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <label for="exampleInputEmail1">C�digo:</label>
                        <input class="form-control" type="text" name="vch_codigo" id="vch_codigo" readonly value="<?php echo $aluno['id_alunoreserva'] ?>" />
                    </div>
                    <div class="col-md-3">
                        <label for="exampleInputEmail1 ">C�digo SGE:</label>
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
                        <label for="" id="labelNome" readonly>Nome do aluno:</label>
                         <!-- line old -->
                         <!-- <input required  class="form-control " type="text" name="vch_nome" id="vch_nome" value="<?php echo $aluno['ed47_v_nome'] ?>" onkeyup="this.value = this.value.toUpperCase();" /> -->
                        <input   class="form-control " onchange="salvaNomeDoCampoModificado(this)" type="text" name="vch_nome" id="vch_nome" readonly value="<?php echo $aluno['ed47_v_nome'] ?>" onkeyup="this.value = this.value.toUpperCase();" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        <label for="sdt_nascimento" id="labelDataNascimento" >Data Nascimento:</label>
                        <input required  readonly class="form-control" onchange="salvaNomeDoCampoModificado(this);testaIdade(this.value);" type="text" name="sdt_nascimento" id="sdt_nascimento" value="<?php echo $datando ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="cp_sexo" id="labelSexo" readonly>Sexo:</label>
                        <select required disabled  class="browser-default custom-select" onchange="salvaNomeDoCampoModificado(this)" name="vch_sexo" id="cp_sexo">
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

                    

                </div>
            </div>
            <!-- <div class="row">
                <div class="col-md-12">
                    <label id="labelQuestionamento" for="vch_email" title="Aluno acolhido pelo poder p�blico."><b>O cadastro � realizado por �rg�o p�blico?</b></label>
                </div>
            </div> -->
            <!-- <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <input type="radio" class="form-check" id="radioNao"  name="radio" value="0" <?php
                            if ($radio == 0) {
                                echo'checked';
                            } else {
                                echo'';
                            }
                            ?> onchange="habilitarRadio(this)">
                        <label for="male">N�o</label><br>
                    </div>
                    <div class="col-md-10">
                        <input type="radio" class="form-check" id="radioSim" name="radio" value="1" <?php
                        if ($radio == 1) {
                            echo'checked';
                        } else {
                            echo'';
                        }
                        ?> onchange="habilitarRadio(this)">
                        <label for="female">Sim</label><br>
                    </div>    
                </div>
            </div>     -->
            <!--<br>-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label id="labelOrgaoPublico" for="vch_orgaopublico">Descri��o do org�o p�blico:</label><span id="spanAsteristicoOrgao"></span>
                        <!--<input autocomplete="off" class="form-control" type="text" id="vch_orgaopublico" name="vch_orgaopublico" id="vch_orgaopublico" value="<?php echo $vch_orgaopublico ?>" onkeyup="this.value = this.value.toUpperCase();" disabled onKeyPress="mudarCorCampo('labelOrgaoPublico', 'vch_orgaopublico')">-->
                        <input autocomplete="off" readonly class="form-control" value="<?php echo $aluno['vch_orgaopublico'] ?>" type="text" id="vch_orgaopublico" name="vch_orgaopublico" id="vch_orgaopublico" value="<?php echo $vch_orgaopublico ?>"  onKeyPress="mudarCorCampo('labelOrgaoPublico', 'vch_orgaopublico');return letras();">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label>Nome da M�e:</label>
                        <!-- <span id="spanAsteristicoMae">*</span> -->
                        <input   class="form-control " onchange="salvaNomeDoCampoModificado(this)" type="text" name="vch_mae" id="vch_mae" readonly value="<?php echo $aluno['ed47_v_mae'] ?>" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label id="labelNomeResponsavel" for="">Nome do respons�vel:</label>
                        <!-- <span id="spanAsteristicoResp">*</span> -->
                        <input  class="form-control "  onchange="salvaNomeDoCampoModificado(this)"  type="text" name="vch_responsavel" id="vch_responsavel" readonly value="<?php echo $aluno['ed47_c_nomeresp'] ?>" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="exampleInputEmail1" id="labelEmail">Email do Responsavel:</label>
                        <input   class="form-control "  onchange="salvaNomeDoCampoModificado(this)"  type="text" name="vch_email_responsavel" id="vch_responsavel" readonly value="<?php echo $aluno['email_resp'] ?>" >
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-5">
                        <label id ="labelCpf" for="">CPF do Responsavel:</label>
                        <input  class="form-control" type="text"  onchange="salvaNomeDoCampoModificado(this)"  name="vch_cpf" id="vch_cpf" readonly value="<?php echo $aluno['ed47_v_cpf'] ?>">

                    </div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="col-md-5">
                        <label for="examleInputEmail1" class="labelNome">Telefone:</label>
                        <input  value="<?php echo $aluno['ed47_v_telef'] ?>"  onchange="salvaNomeDoCampoModificado(this)"  name="vch_telefone" id="vch_telefone" readonly class="form-control" type="text">
                    </div>

                </div>
            </div>

            <br>
            <br>
            <hr>
            <h3 class="text-center">Endere�o</h3>
            <br>
            <br>

            Pesquisa de Endere�o:
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <input  class="form-control" type="text" id="cp_texto" readonly autocomplete="off">
                        <select onclick="pegarValores()" id="resposta" style="width: 640px; margin-left: 0px;display: none;font-size: 10px" name="vch_endereco" multiple="multiple"></select>
                    </div>
                </div>
            </div>


            <br>
            <br />


            <div class="form-group">

            
                <div class="row">
                    <div class="col-md-9">
                        <label for="exampleInputEmail1">Endere�o:</label>
                        <input required value="<?php echo $aluno['ed47_v_ender'] ?>"   name="vch_endereco" class="form-control" id="ender" readonly type="text" readonly>
                        <label for="exampleInputEmail1" class="labelNome">Complemento</label>
                        <input  value="<?php echo $aluno['ed47_v_compl'] ?>" onchange="salvaNomeDoCampoModificado(this)" name="vch_complemento" readonly class="form-control" type="text" >
                    </div>
                    <div class="col-md-3">
                        <label for="exampleInputEmail1" class="labelNome">N�mero:</label>
                        <input required value="<?php echo $aluno['ed47_c_numero'] ?>"  onchange="salvaNomeDoCampoModificado(this)" name="vch_numero" readonly class="form-control .form-control-nome" type="text">
                        <label for="" class="labelNome">Cep:</label>
                        <input name="vch_cep" value="<?php echo $aluno['ed47_v_cep'] ?>" class="form-control" id="vch_cep" readonly type="text" readonly>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <label for="exampleInputEmail1">Bairro:</label>
                        <input required value="<?php echo $aluno['ed47_v_bairro'] ?>" name="vch_bairro" id="vch_bairro" readonly class="form-control" type="text" readonly>
                        <label for="exampleInputEmail1">Localidade:</label>
                        <select disabled class="custom-select" id="cp_localidades" name="vch_localidade">
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
            <?php if ($aluno['pendencia_doc_sge'] == true){ ?> 
            <br>
            <br>
            <hr>
            <h3 class="text-center">Documentos</h3>
            <br>
            <br>

            
          <?php 
            
            if ($documentos == false){
                echo "<h4 class='text-center'>Todos os documentos j� foram enviados. </h4>";
                echo "<h5 class='text-center' style='color:#28A745;'>Aguarde analise e contato para comparecimento.</h5>";
            }
            //die(var_dump($documentos));
            foreach($documentos as $documento){?>
          
            <?php  if($documento['frenteverso'] == 'S'){?>
                <br>
                <div class="card card-body">
                <div class="form-row">
                        <div class="col-md-6">
                            <label for=""><?php echo $documento['ed02_c_descr'].' (FRETE)'  ?></label>
                            <input type="file" <?php  echo $documento['obrigatorio'] == 'S'?'required':'' ?> onchange='validaImagem(this);' name="<?php echo $documento['id_documentoreserva'].'-'.$documento['ed02_c_descr'].'-FRENTE-'?>" class="form-control">            
                        </div>  
                        <div class="col-md-6">
                            <label for=""><?php echo $documento['ed02_c_descr'].' (VERSO)'  ?></label>
                            <input type="file" <?php  echo $documento['obrigatorio'] == 'S'?'required':'' ?> onchange='validaImagem(this);' name="<?php echo $documento['id_documentoreserva'].'-'.$documento['ed02_c_descr'].'-VERSO-'?>" class="form-control">            
                        </div>  
                        
                </div> 
                </div>
                

            <?php }else{?>
                <br>
                <div class="card card-body">
                    <div class="form-row">
                            <div class="col-md-12">
                                <label for=""><?php echo $documento['ed02_c_descr']  ?></label>
                                <input type="file" <?php  echo $documento['obrigatorio'] == 'S'?'required':'' ?> onchange='validaImagem(this);' name="<?php echo $documento['id_documentoreserva'].'-'.$documento['ed02_c_descr'].'-UNICO-'?>" class="form-control">            
                            </div>  
                            
                    </div> 
                </div>
            <?php } ?>

           

           <?php } }?>


           
      <!-- <div class="form-group">
            <div class="card-body">
                <a class="btn btn-secondary col-md-2" href="ficha_cadastro_endereco.php">Voltar</a>
                <div class="d-md-none" style="margin:10px;"></div>
                <button type="button" id="ProsseguirEndereco" class="btn btn-success col-md-2" onClick="Javascript:GravarForm(document.Form);"> Prosseguir</button>
            </div>
      </div> -->
    




            <!-- <br>
            <br>
            <hr> -->
            <!-- <h3>Op��o de Cadastro de Lista de Espera</h3>
            <br>
            <br>

            <div class="form-group col-md-4">
            
                        <label for="cp_serie">S�rie:</label>
                        <select  class="custom-select " id="cp_serie" name="vch_serie" >
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
            
            </div> -->
            
            <!-- <div class="form-group col-md-8">
                            
                        <label for="">Escola Pretendida</label>
                        <select  id="escola" name="escola" class="custom-select">
                            <option readonly value=""></option>
                            <?php foreach ($escolas as $escola) { ?>
                                <option value="<?php echo $escola['codigo'] ?>"><?php echo $escola['escola'] ?></option>
                            <?php } ?>
                        </select>
                <script>
                    $('#escola').val('<?php echo $aluno['ed56_i_escola'] ?>');
                </script>
            </div> -->

            <br>
            <br>
            <br>
            <br>


            <hr>
            <h3>Comprovante rematr�cula</h3>
            <br>
            <br>
            <div class="form-group">
                <div class="row">
                    <div class="col-2"></div>
                    <div class="col-md-8">
                        
                        <!-- <button type="submit" class="btn btn-success col btn-block" href="">
                            Imprimir Comprovante Lista de Espera
                        </button> -->
                        
                        <?php
                            
                            if( ($aluno['pendencia_doc_sge'] == true &&  $documentos == false) || $aluno['confirmacao_rematricula'] == true){
                             $desabilita_botao_rematricula = 'disabled';
                            }else
                             $desabilita_botao_rematricula = '';
                            
                             if($aluno['confirmacao_rematricula'] == true){
                                echo "<h6 class='text-center' style='color:#28A745' > Solicita��o de rematricula j� confirmada !</h6>";
                             }    

                        ?>

                        <button  <?php  echo $desabilita_botao_rematricula; ?> type="submit" class="btn btn-success col btn-block" onclick="return valida()" href="">Confirma��o de rematr�cula</button>
                    </div>
                    <div class="col-2"></div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="col-2"></div>
                    <div class="col-md-8">
                        <a class="btn btn-secondary col-md-12" href="ficha_cadastro.php">Voltar</a>
                    </div>
                    <div class="col-2"></div>
                </div>
            </div>

            <input type="hidden"  name="vch_acoes" id="vch_acoes" value='acoes'>
        </form>
    </div>
    <br>
    <br>
    <br>
    <br>



     <!-- Bot�o para acionar modal -->
     <button id="msg" type="button" style="display: none" class="btn btn-primary" data-toggle="modal" data-target="#modalExemplo">
        Abrir modal de demonstra��o
    </button>
    <!-- Modal -->
    <div class="modal fade" id="modal_msg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Portal rematr�cula</h5>
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

    <script type="text/javascript">
      

    testaIdade($('#sdt_nascimento').val());
    
    function testaIdade(data){
           let idade = calculaIdade(data);
                console.log(idade);
                
            if (idade < 18){
                document.getElementById('vch_responsavel').removeAttribute("disabled");
                document.getElementById('labelEmail').innerText = 'Email do Respons�vel';
                document.getElementById('labelCpf').innerText = 'CPF do Respons�vel';
            }else{
                document.getElementById('vch_responsavel').setAttribute("disabled", "disabled");
                document.getElementById('labelEmail').innerText = 'Email do Aluno';
                document.getElementById('labelCpf').innerText = 'CPF do Aluno';
            }
    }

    function validaImagem(ficheiro){
         
        var extensoes = [".pdf", ".jpeg", ".jpg", ".png", ".tif", ".gif"];
        var fnome = ficheiro.value;
        var extficheiro = fnome.substr(fnome.lastIndexOf('.'));
        if(extensoes.indexOf(extficheiro) >= 0){
            if(!(ficheiro.files[0].size > <?php echo $tamanhoImagemUploadDocumentoAluno ?>)){
                $(ficheiro).removeClass('is-invalid').addClass('is-valid');
                return true;
            } else {
               //mostra menssagem 
               $("#msg_text").text("Arquivo demasiado grande !");
               $('#modal_msg').modal('show');
               $(ficheiro).addClass('is-invalid');
               // remover ficheiro
                ficheiro.value = "";
            }
        } else {
            
            $("#msg_text").text("Extensao inv�lida: "+ extficheiro);
            $('#modal_msg').modal('show');
            $(ficheiro).addClass('is-invalid');
            // remover ficheiro
            ficheiro.value = "";
        }
        return false;
}


        function salvaNomeDoCampoModificado(element){
          if (typeof(window.acoes) =="undefined"){
                window.acoes = [];
          }
          let acao = $(element).attr('name');
       
          if(acoes.indexOf(acao)== -1){
            acoes.push(acao) 
          } 
          
          let sAcoes='';
          if(acoes.length >= 1 ){
              for(let i = 0 ; i <= acoes.length;i++ ){
                 sAcoes += acoes[i]+',';
              }
          }
          
            $('#vch_acoes').val(sAcoes);      
     
              
                  
        } 


        function valida() {

            return true;
            //######################################################################            
            // 1� Valida o preenchimento do nome do Aluno
            //######################################################################
            let nome = $('#vch_nome').val().trim();
            let nome_completo = nome.split(' ');
            // Retorna a idade do aluno 
            let idade = calculaIdade(document.getElementById('sdt_nascimento').value);
            
            if (nome === '') {
                $("#msg").trigger("click");
                $("#msg_text").text("Nome do aluno precisa ser preenchido!");
                document.getElementById('labelNome').style.color = 'red';
                document.getElementById('vch_nome').style.borderColor = 'red';
                return false;
            }
        
            if (nome_completo.length == 1) {
                $("#msg").trigger("click");
                $("#msg_text").text("Nome do aluno est� incompleto!");
                document.getElementById('labelNome').style.color = 'red';
                document.getElementById('vch_nome').style.borderColor = 'red';
                return false;
            }
        
            //######################################################################        
            // 2� Valida o preenchimento da data de nascimento
            //######################################################################            
            if ($('#sdt_nascimento').val().trim() === '') {
                $("#msg").trigger("click");
                $("#msg_text").text("Data de nascimento precisa ser preenchida!");
                document.getElementById('labelDataNascimento').style.color = 'red';
                document.getElementById('sdt_nascimento').style.borderColor = 'red';
                return false;
            } 
        
            if (compareDates($('#sdt_nascimento').val())) {
                 $("#msg").trigger("click");
                 $("#msg_text").text("Data de nascimento n�o pode ser maior que a data atual!");
                 document.getElementById('labelDataNascimento').style.color = 'red';
                 document.getElementById('sdt_nascimento').style.borderColor = 'red';
                 return false;
             }

        
            if (validaDat($('#sdt_nascimento').val())) {
                $("#msg").trigger("click");
                $("#msg_text").text("Data de nascimento est� incorreta!");
                document.getElementById('labelDataNascimento').style.color = 'red';
                document.getElementById('sdt_nascimento').style.borderColor = 'red';
                ;
                return false;
            }

       
              //DATA DE NASCIMENTO MAIOR QUE 100
            datanasc = $('#sdt_nascimento').val();
            dianasc = datanasc.substr(0, 2);
            mesnasc = datanasc.substr(3, 2);
            anonasc = datanasc.substr(6, 4);

            if (anonasc < 1921) {
                $("#msg").trigger("click");
                $("#msg_text").text("Ano da data de nascimento deve ser maior que 1920!");
                document.getElementById('labelDataNascimento').style.color = 'red';
                document.getElementById('sdt_nascimento').style.borderColor = 'red';
                return false;
            }    
        

        //     //######################################################################        
        //     // 3� Valida a sele��o do Sexo
        //     //######################################################################            

            if ($('#cp_sexo').val().trim() === '') {
                $("#msg").trigger("click");
                $("#msg_text").text("Informe o sexo do aluno!");
                document.getElementById('labelSexo').style.color = 'red';
                document.getElementById('cp_sexo').style.borderColor = 'red';
                return false;
            }


            //######################################################################        
            // 4� Valida a sele��o da s�rie
            //######################################################################            

            if ($('#cp_serie').val().trim() === '') {
                $("#msg").trigger("click");
                $("#msg_text").text("Informe a s�rie desejada a cursar!");
                document.getElementById('labelSerie').style.color = 'red';
                document.getElementById('cp_serie').style.borderColor = 'red';
                return false;
            }
        
             //######################################################################        
            // 5� Responde se � org�o p�blico
            //######################################################################            

            if ((document.getElementById('radioSim').checked === false) && (document.getElementById('radioNao').checked === false)) {
                $("#msg").trigger("click");
                $("#msg_text").text("� necess�rio responder se o cadastro � realizado por �rg�o p�blico que acolhe o aluno!");
                return false;
            }
            if (document.getElementById('radioSim').checked) {
                if ($('#vch_orgaopublico').val().trim() === '') {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Informe a descri��o do �rg�o p�blico!");
                    document.getElementById('labelOrgaoPublico').style.color = 'red';
                    document.getElementById('vch_orgaopublico').style.borderColor = 'red';
                    return false;
                }
            } else {

                // 6� N�o � �rg�o p�blico. Necess�rio informar o nome da M�e 
                //######################################################################

                nome = $('#vch_mae').val().trim();
                nome_completo = nome.split(' ');

                if (nome === '') {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Nome da m�e deve ser preenchido!");
                    document.getElementById('labelNomeMae').style.color = 'red';
                    document.getElementById('vch_mae').style.borderColor = 'red';
                    return false;
                }

                if (nome_completo.length == 1) {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Nome da m�e est� incompleto!");
                    document.getElementById('labelNomeMae').style.color = 'red';
                    document.getElementById('vch_mae').style.borderColor = 'red';
                    return false;
                }

                // 7� N�o � �rg�o p�blico. Necess�rio informar o nome do Respons�vel
                //######################################################################            

                nome = $('#vch_responsavel').val().trim();
                nome_completo = nome.split(' ');

                if ((nome === '') && (idade <18) ) {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Nome do Respons�vel deve ser preenchido!");
                    document.getElementById('labelNomeResponsavel').style.color = 'red';
                    document.getElementById('vch_responsavel').style.borderColor = 'red';
                    return false;
                }

                if ((nome_completo.length == 1) && (idade <18)) {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Nome do Respons�vel est� incompleto!");
                    document.getElementById('labelNomeResponsavel').style.color = 'red';
                    document.getElementById('vch_responsavel').style.borderColor = 'red';
                    return false;
                }
            }
        
        
        //     //######################################################################    
        //     // 8� Valida e-mail 
        //     //###################################################################### 

            let Email = document.getElementById('vch_email').value;
            if (Email !== '') {
                result = validEmail(Email);
                if (result == false) {
                    $("#msg").trigger("click");
                    $("#msg_text").text("E-mail incorreto!");
                    document.getElementById('labelEmail').style.color = 'red';
                    document.getElementById('vch_email').style.borderColor = 'red';
                    return false;
                }
            }

        
        
        }
       

        
       
        
      


      
        //     //######################################################################    
        //     // 8� Valida e-mail 
        //     //###################################################################### 

        //     let Email = document.getElementById('vch_email').value;
        //     if (Email !== '') {
        //         result = validEmail(Email);
        //         if (result == false) {
        //             $("#msg").trigger("click");
        //             $("#msg_text").text("E-mail incorreto!");
        //             document.getElementById('labelEmail').style.color = 'red';
        //             document.getElementById('vch_email').style.borderColor = 'red';
        //             return false;
        //         }
        //     }

        //     //######################################################################    
        //     // 9� Valida o CPF 
        //     //###################################################################### 

        //     let cpf_value = $('#vch_cpf').val();

        //     if (cpf_value != '') {
        //         if (!validarCPF(cpf_value)) {
        //             $("#msg").trigger("click");
        //             $("#msg_text").text("CPF inv�lido!");
        //             document.getElementById('labelCpf').style.color = 'red';
        //             document.getElementById('vch_cpf').style.borderColor = 'red';
        //             return false;
        //         }
        //     }

        // }


        // function validaForm() {
        //     //valida data de nascimento
        //     if (validaDat($('#sdt_nascimento').val())) {
        //         alert('Data de nascimento incorreta.');
        //         return false;
        //     }
        //     //valida data ver se atual
        //     if (compareDates($('#sdt_nascimento').val())) {
        //         alert('Data de nascimento n�o pode ser maior que data atual.');
        //         return false;
        //     }
        // }
        function calculaIdade(dataNasc){
            var dataAtual = new Date();
            var anoAtual = dataAtual.getFullYear();
            var anoNascParts = dataNasc.split('/');
            var diaNasc =anoNascParts[0];
            var mesNasc =anoNascParts[1];
            var anoNasc =anoNascParts[2];
            var idade = anoAtual - anoNasc;
            var mesAtual = dataAtual.getMonth() + 1;
            //se m�s atual for menor que o nascimento, nao fez aniversario ainda; (26/10/2009)
        if(mesAtual < mesNasc){
            idade--;
        }else {
            //se estiver no mes do nasc, verificar o dia
            if(mesAtual == mesNasc){
                if(dataAtual.getDate() < diaNasc ){
                //se a data atual for menor que o dia de nascimento ele ainda nao fez aniversario
                idade--;
                }
            }
        }
       return idade;
       }

     

        function compareDates(date) {
            let parts = date.split('/') // separa a data pelo caracter '/'
            let today = new Date() // pega a data atual

            date = new Date(parts[2], parts[1] - 1, parts[0]) // formata 'date'

            // compara se a data informada � maior que a data atual
            // e retorna true ou false
            return date >= today ? true : false;
        }

        function habilitarRadio(radio) {
            if (radio.value == 0) {
                document.getElementById('vch_orgaopublico').setAttribute("disabled", "disabled");
                document.getElementById('vch_orgaopublico').value = '';
                // Controla os campos obrigat�rios.                 
                document.getElementById('spanAsteristicoMae').innerText = '*';
                document.getElementById('spanAsteristicoResp').innerText = '*';
                document.getElementById('spanAsteristicoOrgao').innerText = '';


            } else {
                document.getElementById('vch_orgaopublico').removeAttribute("disabled");
                document.getElementById('vch_orgaopublico').value = '';
                // Controla os campos obrigat�rios. 
                document.getElementById('spanAsteristicoMae').innerText = '';
                document.getElementById('spanAsteristicoResp').innerText = '';
                document.getElementById('spanAsteristicoOrgao').innerText = '*';
            }
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

            let localidade = $('#resposta :selected').attr('data-localidade');
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
                    carregar_localidade(codigo_bairro, localidade);

                })
                .fail(function(jqXHR, textStatus, msg) {
                    alert('Requisi��o Falhou !');
                });

        }

        //carregar localidade

        function carregar_localidade(codigo_bairro, set_localidade) {
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
                    $('#cp_localidades').val(set_localidade);

                })
                .fail(function(jqXHR, textStatus, msg) {
                    alert('Requisi��o Falhou !');
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