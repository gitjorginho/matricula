<?php
$banner_passo = 0;
require_once('header.php');

if (isset($_GET['id_alunoreserva'])) {
    $id_alunoreserva = $_GET['id_alunoreserva'];
}
?>

<div class="centr">
    <br>
    <h2 class="text-center">Lista de Espera</h2>
    <br>
    <form method="post" action="verificar_matricula_rematricula.php">
        <div class="card-body">
            <i>-Caro aluno, favor informar o c�digo de inscri��o presente no Comprovante de Lista de Espera.</i>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="exampleInputEmail1">C�digo do Aluno:</label>
                    <input class="form-control" type="text" name="cod_aluno" id="cod_aluno" />
                </div>
            </div>
        </div>
        <div class="card-body">
            <i>-Caso n�o tenha em m�os o c�digo de inscri��o, favor preencher os campos abaixo.</i>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-8">
                    <label for="exampleInputEmail1" id="labelNomeAluno">Nome do Aluno:</label>
                    <input onkeyup="this.value = this.value.toUpperCase();" class="form-control" type="text" name="vch_nome_aluno" id="vch_nome_aluno" onKeyPress="mudarCorCampo('labelNomeAluno', 'vch_nome_aluno')" />
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-8">
                    <label for="exampleInputEmail1">Nome do Respons�vel:</label>
                    <input onkeyup="this.value = this.value.toUpperCase();" class="form-control" type="text" name="vch_nome_resp" />
                </div>

            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="exampleInputEmail1" id="labelDataNascimento">Data Nascimento do aluno:</label>
                    <input class="form-control" type="text" name="vch_data_nasc" id="vch_datanasc_edit" onKeyPress="mudarCorCampo('labelDataNascimento', 'vch_datanasc_edit')" />
                </div>
            </div>
        </div>
        <br>
        <div class="form-group">
            <div class="row">
                <div class="col">
                    <input class="btn btn-success col-3" type="submit" onclick="return validar()" value="Enviar">
                    <input class="form-control" type="hidden" name="vch_cod_aluno" id="vch_cod_aluno" value="<?php echo $id_alunoreserva; ?>"/>  
                </div>
            </div>
        </div>
    </form>


    <!-- Bot�o para acionar modal -->
    <button id="msg" type="button" style="display: none" class="btn btn-primary" data-toggle="modal" data-target="#modalExemplo">
        Abrir modal de demonstra��o
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
                    <button type="button" class="btn btn-success" data-dismiss="modal" data-backdrop="static" >OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMessagem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Portal Lista de Espera</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    N�o foi poss�vel localizar aluno! Verifique se os dados est�o corretos.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary col-md-3" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#vch_datanasc_edit').mask('00/00/0000');

        $('#cp_enviar').click(function () {

            let cpf = $('#vch_cpf_edit').val();
            let data_nasc = $('#vch_datanasc_edit').val();
            let nome_aluno = $('#vch_nome_aluno').val();
            let cod_aluno = $('#cod_aluno').val();

            $.ajax({
                url: "verificar_matricula_rematricula.php",
                type: 'post',
                data: {
                    nome_aluno: nome_aluno,
                    data_nasc: data_nasc,
                    cpf: cpf,
                    cod_aluno: cod_aluno
                },
                beforeSend: function () {}
            })
                    .done(function (msg) {
                        let data = JSON.parse(msg);
                        if (data.response == 'error') {
                            $('#btn-msg').trigger('click');
                        }
                    })
                    .fail(function (jqXHR, textStatus, msg) {
                        alert('Busca de turmas falhou !');
                    });

        });

    </script>

    <!--VERIFICA SE A BUSCA DO ALUNO RETORNOU VAZIA E IMPRIME MENSAGEM RELACIONADA A ISTO -->
    <?php if (isset($_GET['not_found'])) { ?>
        <script>
            $('#modalMessagem').fadeIn().modal('show');
        </script>
    <?php } ?>


    <script>

        function mudarCorCampo(nomeDoLabel, nomeDoCampo) {
            //muda cor do campo vazio para cor padrao, outrora vermelho
            document.getElementById(nomeDoLabel).style.color = 'black';
            document.getElementById(nomeDoCampo).style.borderColor = '#ced4da';
        }

        function validar() {
            //valida de determinados dados do formulario estao vazios
            let codigoAluno = $('#cod_aluno').val();
            let nomeAluno = $('#vch_nome_aluno').val();
            let dataNascimento = $('#vch_datanasc_edit').val();
            let nome_completo = nome.split(' ');

            if (nomeAluno.trim() === '' && codigoAluno.trim() === '') {
                $("#msg").trigger("click");
                $("#msg_text").text("Preencha o nome do aluno!");
                document.getElementById('labelNomeAluno').style.color = 'red';
                document.getElementById('vch_nome_aluno').style.borderColor = 'red';
                return false;
            }

            if (nome_completo.length == 1) {
                $("#msg").trigger("click");
                $("#msg_text").text("Nome do aluno est� incompleto!");
                document.getElementById('labelDataNascimento').style.color = 'red';
                document.getElementById('sdt_nascimento').style.borderColor = 'red';
                return false;
            }

            if (dataNascimento.trim() === '' && codigoAluno.trim() === '') {

                $("#msg").trigger("click");
                $("#msg_text").text("Preencha a data de nascimento!");
                document.getElementById('labelDataNascimento').style.color = 'red';
                document.getElementById('vch_datanasc_edit').style.borderColor = 'red';
                return false;
            }
        }
    </script>



    <?php if (isset($_GET['alunocadastrado'])) { ?>
        <script>
            //alert('Aluno j� cadastrado, vc pode consultar os dados aqui.');
            $('#msg').trigger('click');
            $('#msg_text').text("Aluno j� cadastrado! N�mero da reserva: " + document.getElementById('vch_cod_aluno').value + "");
        </script>
    <?php } ?>
    <?php require_once('footer.php'); ?>


