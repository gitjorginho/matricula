<?php
session_start();
$banner_passo = 1;
$img_banner_passo = 'img/guia_02.jpg';
require_once('header.php');

$passo = $_SESSION['passo'];
if (!in_array(1, $passo)) {
    header('Location:index.php');
}

//session_start();
//var_dump($_SESSION);
$nome_aluno     = strtoupper($_SESSION['vch_nome']);
$data_nascimento= $_SESSION['sdt_nascimento'];
$nome_mae       = $_SESSION['vch_mae'];

include_once("conexao.php");
$conexao = new Conexao();
$conn = $conexao->conn();



if (isset($_POST['vch_endereco'])) {
    if (!in_array(2, $passo)) {
        array_push($passo, 2);
        $_SESSION['passo'] = $passo;
    }
    $_SESSION = array_merge($_SESSION, $_POST);
    header('Location: ficha_cadastro_opcao.php');
    //header('Location: ficha_proc.php');
}


$endereco = isset($_SESSION['vch_endereco']) ? $_SESSION['vch_endereco'] : '';
$bairro = isset($_SESSION['vch_bairro']) ? $_SESSION['vch_bairro'] : '';
$telefone = isset($_SESSION['vch_telefone']) ? $_SESSION['vch_telefone'] : '';
$numero = isset($_SESSION['vch_numero']) ? $_SESSION['vch_numero'] : '';
$cidade = isset($_SESSION['vch_cidade']) ? $_SESSION['vch_cidade'] : '';
$cep = isset($_SESSION['vch_cep']) ? $_SESSION['vch_cep'] : '';
$complemento = isset($_SESSION['vch_complemento']) ? $_SESSION['vch_complemento'] : '';

//vch_pesq_endereco
$localidade = isset($_SESSION['vch_localidade']) ? $_SESSION['vch_localidade'] : '';
$codigo_bairro = isset($_SESSION['vch_pesq_endereco']) ? $_SESSION['vch_pesq_endereco'] : '';
//echo ($codigo);
// Pesquisa a Localidade no Voltar

if ($codigo_bairro ===  ''){
   
} else {
$array = explode("-", $codigo_bairro);
$bair = $array[1];
$sql_localidade = "
            select *
            from territorio.localidade
            where loc_i_bairro = " . $bair;

$result = pg_query($conn, $sql_localidade);
$localidades = pg_fetch_all($result);
}

//if($codigo_bairro != '') {
//
//    $sql = " select j14_codigo,j14_nome as endereco, j13_codi as codigo_bairro,j13_descr as bairro ,j29_cep as cep ,trim(ed261_c_nome) as cidade
//            from cadastro.ruas r
//            inner join ruasbairro rb on r.j14_codigo = rb.j16_lograd
//            inner join ruasbairrocep rbc on rbc.j32_ruasbairro = rb.j16_codigo
//            inner join bairro b on b.j13_codi = rb.j16_bairro
//            inner join ruascep on j29_codigo = j32_ruascep
//	        inner join censomunic on j13_i_censomunic = ed261_i_codigo
//            where j14_codigo = {$codigo_bairro}";
//
//    $result = pg_query($conn, $sql);
//    $endereco_sele = pg_fetch_assoc($result);
//
//
//}
?>
<div class="centr">
    <br>
    <h2 class="text-center">Dados Pessoais</h2>
    <form id="ficha" method="post" action="#">
        <br>
        <div class="form-group">
            <div class="row">
                <div class="col">
                    <div class="card-body">
                        <p><i>Primeiro preencha o endereço em que o estudante reside no campo abaixo.</i></p>
<!--                        <div class="form-group">-->
<!--                            <label for="">Pesquisa de Localidade</label>-->
<!--                            <input class="form-control" type="text" id="cp_localidade" onkeyup="pesquisa_endereco();" >-->
<!--                        </div>-->
                        <label id="labelPesquisaEndereco"for="">Pesquisa de Endereço:</label>
                        <input autocomplete="off" class="form-control col-md-12" type="text" id="cp_texto" onkeyup="pesquisa_endereco();">

                        <select  style="font-size: 10px" class="form-control" onclick="pegarValores()" onchange="mudarCorParaNormal('labelPesquisaEndereco', 'cp_texto')" id="resposta" class="resp_endereco" name="vch_pesq_endereco" multiple="multiple">
                        <!--<select  class="form-control" onclick="pegarValores()" onchange="mudarCorParaNormal('labelPesquisaEndereco','cp_texto')" id="resposta" class="resp_endereco" name="vch_pesq_endereco" multiple="multiple">-->
                        </select>
                        <div style="width: 5px; height:5px">
                            <img id="loading" style="display: none" src="img/loading.gif" width="20" alt="">
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="exampleInputEmail1">Endereço:</label><span>*</span>
                    <input autocomplete="off" required value="<?php echo $endereco ?>" name="vch_endereco" class="form-control" id="ender" type="text" readonly>
                </div>
                <div class="col-md-6">
                    <label id="labelNumero" for="exampleInputEmail1">Número:</label>
                    <input autocomplete="off" required value="<?php echo $numero ?>" name="vch_numero" id ="vch_numero" onchange="mudarCorParaNormal('labelNumero', 'vch_numero')" onkeypress="return onlynumber();" class="form-control col-md-5" type="text">
                </div>
            </div>    
            <div class="row">
                <div class="col-md-6">
                    <label for="exampleInputEmail1">Bairro:</label><span>*</span>
                    <input autocomplete="off" required value="<?php echo $bairro ?>" name="vch_bairro" id="vch_bairro" class="form-control" type="text" readonly>
                </div>
                <div class="col-md-6">
                    <label for="">CEP:</label><span>*</span>
                    <input autocomplete="off" name="vch_cep" value="<?php echo $cep ?>" class="form-control" id="vch_cep" type="text" readonly>
                </div>
            </div> 
            <div class="row">
                <div class="col-md-6">  
                    <label id="labellocalidade"  for="exampleInputEmail1">Localidade:</label><span>*</span>
                    <select disabled required class="custom-select" id="cp_localidades" onchange="mudarCorParaNormal('labellocalidade', 'cp_localidades')" name="vch_localidade">
<?php
if ($codigo_bairro != '') {
    foreach ($localidades as $localidade_selec) {
        echo '<option value=' . $localidade_selec['loc_i_cod'] . '>' . $localidade_selec['loc_v_nome'] . "</option>";
    }
}
?>
                    </select>
                    <script>
                        $('#cp_localidades').val(<?php echo "'$localidade'" ?>);
                    </script>
                </div>  
                <div class="col-md-6"> 
                    <label for="exampleInputEmail1">Cidade</label><span>*</span>
                    <input autocomplete="off" required name="vch_cidade" value="<?php echo $cidade ?>" id="vch_cidade" class="form-control" type="text" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6"> 
                    <label for="exampleInputEmail1">Telefone (Celular):</label>
                    <input autocomplete="off" value="<?php echo $telefone ?>" name="vch_telefone" id="vch_telefone" class="form-control" type="text">
                </div>
                <div class="col-md-6">
                    <label for="exampleInputcomplemento">Complemento:</label>
                    <input autocomplete="off"  value="<?php echo $complemento ?>" name="vch_complemento" class="form-control " type="text">

                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="card-body">
                <a class="btn btn-secondary col-md-2" href="ficha_cadastro.php">Voltar</a>
                <div class="d-md-none" style="margin:10px;"></div>
                <!--<input id="btn_submit" class="btn btn-success col-md-2" type="submit" value="Prosseguir">-->
                <button type="button" id="ProsseguirEndereco" class="btn btn-success col-md-2" onClick="Javascript:GravarForm(document.Form);"> Prosseguir</button>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label label="labelAsterisco" style="font-size: 11px">(*)Campos obrigatórios</label>
                </div>
            </div>
        </div>

</div>

</form>
<!-- Botão para acionar modal -->
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

<script type="text/javascript">
    $(document).ready(function () {
        $("#myInput").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myList li").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function GravarForm(frm)
    {
        $result = valida();
        if ($result != false) {
            document.forms["ficha"].submit();
        }
    }

    jQuery(function ($) {
        $("#vch_telefone").mask("(99) 9 9999-9999");
    });

    function valida()
    {
        if ($('#ender').val() === '') {
            $("#msg").trigger("click");
            $("#msg_text").text("Informe o endereço em que o estudo reside. Primeiro pesquise o endereço e depois selecione o endereço desejado.");
            document.getElementById('labelPesquisaEndereco').style.color = 'red';
            document.getElementById('cp_texto').style.borderColor = 'red';
            ;
            return false;
        }

       // if ($('#vch_numero').val() == '') {
       //     $("#msg").trigger("click");
       //     $("#msg_text").text("Informe o número do endereço!");
       //     document.getElementById('labelNumero').style.color = 'red';
       //     document.getElementById('vch_numero').style.borderColor = 'red';
       //     ;
       //     return false;
       // }

        let $padrao_numero = /^[0-9]*$/;
        let  numero = $('#vch_numero').val();
        if (!$padrao_numero.test(numero)) {
            $("#msg").trigger("click");
            $("#msg_text").text("Número de endereço invalido!");
            document.getElementById('labelNumero').style.color = 'red';
            document.getElementById('vch_numero').style.borderColor = 'red';
            return false;
        }






        if ($('#cp_localidades').val() == '') {
            $("#msg").trigger("click");
            $("#msg_text").text("Informe a localidade!");
            document.getElementById('labellocalidade').style.color = 'red';
            document.getElementById('cp_localidades').style.borderColor = 'red';
            return false;
        }
    }

    //evento de pesquisar endereco
   // $('#cp_texto').keyup(function () {


    function pesquisa_endereco() {
        ender = $('#cp_texto').val();
        let loc = $('#cp_localidade').val();
        $('#resposta').show();
        $('#loading').show();
        $('#cp_localidades').attr('disabled', 'disabled');
        $('#btn_submit').prop('disabled', true);
        $.ajax({
            url: "pesq.php",
            type: 'get',
            data: {
                funcao: 'carregar_autocomplete',
                endereco: ender,
                localidade: loc
            },
            beforeSend: function () {
            }
        })
            .done(function (msg) {
                //alert(msg);
                $('#loading').hide();
                $('#resposta').html(msg);
            })
            .fail(function (jqXHR, textStatus, msg) {
                $('#loading').show();
                alert(msg);
            });
    }
        //});




    function pegarValores() {

        let valor = $('#resposta :selected').val();

        let localidade = $('#resposta :selected').attr('data-localidade');
        console.log(localidade);

        $('#loading').show();
        $('#cp_localidades').attr('disabled', 'disabled');
        $('#btn_submit').prop('disabled', true);

        $.ajax({
            url: "pesq.php",
            type: 'get',
            data: {
                codigo: valor,
                funcao: 'carregar_endereco'
            },
            beforeSend: function () {}
        })
                .done(function (msg) {
                    //alert(msg);
                    $('#btn_submit').prop('disabled', false);
                    $('#cp_localidades').removeAttr('disabled');
                    $('#loading').hide();
                    let endereco = JSON.parse(msg);
                    $('#vch_bairro').val(endereco.bairro);
                    $('#vch_cidade').val(endereco.cidade);
                    $('#vch_cep').val(endereco.cep);
                    $('#ender').val(endereco.endereco);
//                    // Copia o texto selecionado para o imput. 
//                    $('#cp_texto').val(endereco.endereco + " - " + endereco.bairro);
//                    // Desabilita a consulta apos o click.
//                    $('#resposta').hide();
                    let codigo_bairro = endereco.codigo_bairro;
                    carregar_localidade(codigo_bairro, localidade);

                })
                .fail(function (jqXHR, textStatus, msg) {
                    $('#loading').hide();
                    alert('Requisição Falhou!');
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

            beforeSend: function () {}
        })
                .done(function (msg) {
                    $('#cp_localidades').html(msg);
                    $('#cp_localidades').val(set_localidade);

                })
                .fail(function (jqXHR, textStatus, msg) {
                    alert('Requisição Falhou!');
                });
    }

    function mudarCorParaNormal(nomeDoLabel, nomeDoCampo) {
        document.getElementById(nomeDoLabel).style.color = 'black';
        document.getElementById(nomeDoCampo).style.borderColor = '#ced4da';
    }
    function onlynumber(evt) {
        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode( key );
        //var regex = /^[0-9.,]+$/;
        var regex = /^[0-9.]+$/;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
   }
}

</script>


<?php
//onclick="window.location.href='ficha_cadastro_opcao1.php'"
require_once('footer.php');
?>