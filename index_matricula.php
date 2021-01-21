<?php

    session_start();
    session_destroy();
    session_start();
    $banner_passo = 0;

    require_once('header.php');

    if (isset($_GET['passo'])) {
        $_SESSION['passo'] = array(0);
        header('Location:ficha_cadastro.php');
    }

?>

<style>
    @import url(tiny.css) (min-width:300px);
    @import url(small.css) (min-width:600px);
    @import url(big.css) (min-width:900px);
</style>
<div class="centr">
    <div class="card-body" style="text-align:center">
        <div class="form-group">
           <div class="row" >
               <div class="col-md-12" style="text-align: center">
                   <br>
                   <h2><b>Seja bem vindo!</b></h2>
                   <h6>Sistema de realização de lista de espera</h6>
                   <br>
               </div>
           </div>
        </div>        
        <div class="form-group cadastrarEmLista">
        <div class="row">
            <div class="col-md-12">
                <button class="text-center btn btn-success btn-lg col-10 btnTelaLogin" type="button" data-toggle="modal"
                        data-target="#modalExemplo">
                        <span class="d-none d-md-block">Quero me cadastrar em uma lista de espera da SEDUC</span>
                        <small class="d-block d-md-none">Me cadastrar na <br class="ajusta"> lista de espera</small>
                </button>
                <br/>
            </div>
        </div>
    </div>
    <div class="form-group cadastrarEmLista">
        <div class="row">
            <div class="col-md-12 ">
                <a href="editar_matricula.php" class=" text-center btn btn-success btn-lg col-10 btnTelaLogin ">
                    <small class="d-block d-md-none">Já possuo cadastro e <br class="ajusta"> desejo consultar a situação</small>
                    <div class="d-none d-md-block">Já possuo cadastro e  desejo consultar a situação</div>
                </a>
            </div>
        </div>
    </div> 
</div>
</div> 
</div>
    <div style="min-height: 10px;"></div>

    <!-- Modal -->

    <div class="modal fade" id="modalExemplo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Para iniciar, tenha em mãos os seguintes dados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    1 - NOME COMPLETO;<br>
                    2 - DATA DE NASCIMENTO;<br>
                    3 - NOME COMPLETO E CPF DA MÃE E/OU RESPONSÁVEL;<br>
                    4 - ENDEREÇO COMPLETO DA RESIDÊNCIA (COM O CEP);<br>
                    <!--5 - UNIDADE ESCOLAR;<br>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary col-md-3" data-dismiss="modal">Fechar</button>
                    <a class="btn btn-success col-md-3" href="index.php?passo=0">OK</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão para acionar modal -->
    <button id="cadastrado" type="button" style="display: none" class="btn btn-primary" data-toggle="modal"
            data-target="#msg_ja_existe">
        Abrir modal de demonstração
    </button>

    <!-- Modal -->
    <div class="modal fade" id="msg_ja_existe" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Aluno Já cadastrado.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ALUNO JÁ CONSTA NA BASE DE DADOS, FAVOR SELECIONAR A OPÇÃO DE CONSULTA
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary col-md-3" data-dismiss="modal">Fechar</button>

                </div>
            </div>
        </div>
    </div>


    <!-- esta div é provisoria e somente habilitada no caso de uma notificacao ao entrar na pagina -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div id="botaoCloseModal"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div>
                    <br>
                    <h5 style="padding-right: 27px">Srs.(a),
                    <br><br>O Portal da Lista de Espera da SEDUC entrará em manutenção no dia 11/08/2020 no horário de 08:00 com previsão de retorno dia 13/08/2020 no horário de 08:00. Em caso de dúvidas neste período, por gentileza, entrar em contato com matrícula atende (71) 98796-8484 ou seduccmie@educa.camacari.ba.gov.br.
                    <br><br>
                    Atenciosamente
                    <br><br> Coordenação de Matrículas e Informações Educacionais.</h5>
                </div>                  
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
           // $('#myModal').modal('show');
        });
    </script>

    <?php
        if (isset($_GET['cadastro'])) {
            echo "<script>
            $('#cadastrado').trigger('click');
            </script>";
        }
        require_once('footer.php');
    ?>
