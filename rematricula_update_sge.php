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

$codigo = $_SESSION['codigo_sge'];


/*REMOVER $sql_status = "select * from reserva.alunostatusreserva";
$result = pg_query($conn, $sql_status);
$status = pg_fetch_all($result);FIMREMOVER*/



$sql_aluno = "select (select true 
                        from docaluno 
                       where ed49_i_aluno = $codigo 
                       limit 1) as pendencia_doc_sge,
                     (select true 
                        from confirmacaorematricula 
                       where edu01_aluno = $codigo) as confirmacao_rematricula,
                     (select (case when ed60_i_codigo = null 
                              then false 
                              else true 
                               end) matriculado
                        from matricula 
                       inner join turma on ed57_i_codigo = ed60_i_turma 
                       inner join calendario on ed57_i_calendario = ed52_i_codigo
                       where ed60_i_aluno = $codigo
                         and ed52_i_ano = 2020 and ed60_c_situacao in ('MATRICULADO', 'APROVADO')) as matriculado,
                     a.ed47_d_nasc,
                     a.ed47_i_codigo,
                     null as id_alunoreserva,
                     a.ed47_v_nome,
                     a.ed47_v_mae
                from escola.aluno a
               where a.ed47_i_codigo = $codigo
               limit 1;";

$result = pg_query($conn, $sql_aluno);
$aluno = pg_fetch_assoc($result);

//Lógica para preencher data de nascimento do aluno
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

//Fim da lógica para preencher data de nascimento do aluno


//Verificar documentação pendente para a rematrícula. Por não ser mais obrigatório, não é necessário checar se já foi enviado. A rematrícula já é sinal de que não precisa carregar essas informações.
$sql_documento = "select d2.ed02_i_codigo,
                         d2.ed02_c_descr,
                         d3.id_documentoreserva,
                         d3.obrigatorio,
                         d3.frenteverso
                    from escola.docaluno d,
                         escola.documentacao d2,
                         reserva.documentoreserva d3
                   where d.ed49_i_documentacao = d2.ed02_i_codigo
                     and d3.ed02_i_codigo = d2.ed02_i_codigo 
                     and d.ed49_i_aluno = {$aluno['ed47_i_codigo']}";

$result = pg_query($conn,$sql_documento);
$documentos =  pg_fetch_all($result);

//die(var_dump($documentos))

?>

<div class="">


    <br>
    <!--<div class="card-body">
        <font color="red"><b>ATENÇÃO:</b></font> <i>Para alunos que estudam na ultima etapa, Só seram cadastrado para rematricula quem possuir repetência. Caso contrário séra destinado ao calendário de transferência.</i>
    </div>-->
    <br>
    <h3 class="text-center">Rematrícula 2021</h3>
    <br>
    <div class="card-body">
        <form method="post"  enctype="multipart/form-data" action="registro_update_sge.php">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <label for="exampleInputEmail1">Código:</label>
                        <input class="form-control" type="text" name="vch_codigo" id="vch_codigo" readonly value="<?php echo $aluno['id_alunoreserva'] ?>" />
                    </div>
                    <div class="col-md-2">
                        <label for="exampleInputEmail1 ">Código SGE:</label>
                        <input class="form-control" type="text" name="vch_codigo_sge" id="vch_codigo" readonly value="<?php echo $aluno['ed47_i_codigo'] ?>" />
                    </div>
                    <div class="col-md-8">
                        <label for="" id="labelNome" readonly>Nome do aluno:</label>
                         <!-- line old -->
                         <!-- <input required  class="form-control " type="text" name="vch_nome" id="vch_nome" value="<?php echo $aluno['ed47_v_nome'] ?>" onkeyup="this.value = this.value.toUpperCase();" /> -->
                        <input   class="form-control " onchange="salvaNomeDoCampoModificado(this)" type="text" name="vch_nome" id="vch_nome" readonly value="<?php echo $aluno['ed47_v_nome'] ?>" onkeyup="this.value = this.value.toUpperCase();" />
                    </div>
                </div>
            </div>
                                   
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="sdt_nascimento" id="labelDataNascimento" >Data de nascimento:</label>
                        <input required  readonly class="form-control" onchange="salvaNomeDoCampoModificado(this);testaIdade(this.value);" type="text" name="sdt_nascimento" id="sdt_nascimento" value="<?php echo $datando ?>">
                    </div>

                    <div class="col-md-8">
                        <label>Nome da Mãe:</label>
                        <!-- <span id="spanAsteristicoMae">*</span> -->
                        <input   class="form-control " onchange="salvaNomeDoCampoModificado(this)" type="text" name="vch_mae" id="vch_mae" readonly value="<?php echo $aluno['ed47_v_mae'] ?>" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                </div>
            </div>            
                              
            <?php if ($aluno['pendencia_doc_sge'] = true){ ?> 
            <!-- <br> -->
            <!-- <br> -->
            <hr>
            <h3 class="text-center">Documentos pendentes</h3>
            <h4>Anexar documentos pendentes abaixo, caso não tenha o documento em mãos, verificar com a unidade escolar uma data para entrega</h4>
            <br>
            <br>

            
          <?php 
            
            if ($documentos == false){
                echo "<h4 class='text-center'>Todos os documentos já foram enviados. </h4>";
                echo "<h5 class='text-center' style='color:#28A745;'>Aguarde analise e contato para comparecimento.</h5>";
            }
            //die(var_dump($documentos));
            foreach($documentos as $documento){?>
          
            <?php  if($documento['frenteverso'] == 'S'){?>
                <br>
                <div class="card card-body">
                    <div class="form-row">
                            <div class="col-md-6">
                                <label for=""><?php echo $documento['ed02_c_descr'].' (FRENTE)'  ?></label>
                                <input type="file"  accept=".pdf,.jpeg,.jpg,.JPG,.png,.PNG,.tif,.gif"  onchange='validaImagem(this);' name="<?php echo $documento['id_documentoreserva'].'-'.$documento['ed02_c_descr'].'-FRENTE-'?>" class="form-control">            
                            </div>  
                            <div class="col-md-6">
                                <label for=""><?php echo $documento['ed02_c_descr'].' (VERSO)'  ?></label>
                                <input type="file" accept=".pdf,.jpeg,.jpg,.JPG,.png,.PNG,.tif,.gif"  onchange='validaImagem(this);' name="<?php echo $documento['id_documentoreserva'].'-'.$documento['ed02_c_descr'].'-VERSO-'?>" class="form-control">            
                            </div>  
                            
                    </div> 
                </div>
                

            <?php }else{?>
                <br>
                <div class="card card-body">
                    <div class="form-row">
                            <div class="col-md-12">
                                <label for=""><?php echo $documento['ed02_c_descr']  ?></label>
                                <input type="file" accept=".pdf,.jpeg,.jpg,.JPG,.png,.PNG,.tif,.gif"  onchange='validaImagem(this);' name="<?php echo $documento['id_documentoreserva'].'-'.$documento['ed02_c_descr'].'-UNICO-'?>" class="form-control">            
                            </div>  
                    </div> 
                </div>
            <?php } ?>

           

           <?php } }?>

            <br>
            <br>
            <br>
            <br>


            <hr>
            <h3>Comprovante rematrícula</h3>
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
                            //($aluno['pendencia_doc_sge'] == true &&  $documentos == false) 
                            if($aluno['confirmacao_rematricula'] == true){
                            $desabilita_botao_rematricula = 'disabled';
                            }else
                             $desabilita_botao_rematricula = '';
                            
                             if($aluno['confirmacao_rematricula'] == true){
                                echo "<h6 class='text-center' style='color:#28A745' > Solicitação de rematricula já confirmada !</h6>";
                             }    

                        ?>

                        <button  <?php  echo $desabilita_botao_rematricula; ?> type="submit" class="btn btn-success col btn-block" onclick="return valida()" href="">Confirmação de rematrícula</button>
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

     <!-- Botão para acionar modal -->
     <button id="msg" type="button" style="display: none" class="btn btn-primary" data-toggle="modal" data-target="#modalExemplo">
        Abrir modal de demonstração
    </button>
    <!-- Modal -->
    <div class="modal fade" id="modal_msg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Portal rematrícula</h5>
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
    

    <?php if(isset($_GET['not_matricula'])){ ?> 
               $("#msg_text").text("Aluno não tem nenhum registro de matricula do ano anterior.");
               $('#modal_msg').modal('show');
    <?php }?>

    function testaIdade(data){
           let idade = calculaIdade(data);
                console.log(idade);
                
            if (idade < 18){
                document.getElementById('vch_responsavel').removeAttribute("disabled");
                document.getElementById('labelEmail').innerText = 'Email do Responsável';
                document.getElementById('labelCpf').innerText = 'CPF do Responsável';
            }else{
                document.getElementById('vch_responsavel').setAttribute("disabled", "disabled");
                document.getElementById('labelEmail').innerText = 'Email do Aluno';
                document.getElementById('labelCpf').innerText = 'CPF do Aluno';
            }
    }

    function validaImagem(ficheiro){
         
        var extensoes = [".pdf", ".jpeg", ".jpg",".JPG", ".png",".PNG", ".tif", ".gif"];
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
            
            $("#msg_text").text("Extensao inválida: "+ extficheiro);
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
            // 1º Valida o preenchimento do nome do Aluno
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
                $("#msg_text").text("Nome do aluno está incompleto!");
                document.getElementById('labelNome').style.color = 'red';
                document.getElementById('vch_nome').style.borderColor = 'red';
                return false;
            }
        
            //######################################################################        
            // 2º Valida o preenchimento da data de nascimento
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
                 $("#msg_text").text("Data de nascimento não pode ser maior que a data atual!");
                 document.getElementById('labelDataNascimento').style.color = 'red';
                 document.getElementById('sdt_nascimento').style.borderColor = 'red';
                 return false;
             }

        
            if (validaDat($('#sdt_nascimento').val())) {
                $("#msg").trigger("click");
                $("#msg_text").text("Data de nascimento está incorreta!");
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
        //     // 3º Valida a seleção do Sexo
        //     //######################################################################            

            if ($('#cp_sexo').val().trim() === '') {
                $("#msg").trigger("click");
                $("#msg_text").text("Informe o sexo do aluno!");
                document.getElementById('labelSexo').style.color = 'red';
                document.getElementById('cp_sexo').style.borderColor = 'red';
                return false;
            }


            //######################################################################        
            // 4º Valida a seleção da série
            //######################################################################            

            if ($('#cp_serie').val().trim() === '') {
                $("#msg").trigger("click");
                $("#msg_text").text("Informe a série desejada a cursar!");
                document.getElementById('labelSerie').style.color = 'red';
                document.getElementById('cp_serie').style.borderColor = 'red';
                return false;
            }
        
             //######################################################################        
            // 5º Responde se é orgão público
            //######################################################################            

            if ((document.getElementById('radioSim').checked === false) && (document.getElementById('radioNao').checked === false)) {
                $("#msg").trigger("click");
                $("#msg_text").text("É necessário responder se o cadastro é realizado por órgão público que acolhe o aluno!");
                return false;
            }
            if (document.getElementById('radioSim').checked) {
                if ($('#vch_orgaopublico').val().trim() === '') {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Informe a descrição do órgão público!");
                    document.getElementById('labelOrgaoPublico').style.color = 'red';
                    document.getElementById('vch_orgaopublico').style.borderColor = 'red';
                    return false;
                }
            } else {

                // 6º Não é órgão público. Necessário informar o nome da Mãe 
                //######################################################################

                nome = $('#vch_mae').val().trim();
                nome_completo = nome.split(' ');

                if (nome === '') {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Nome da mãe deve ser preenchido!");
                    document.getElementById('labelNomeMae').style.color = 'red';
                    document.getElementById('vch_mae').style.borderColor = 'red';
                    return false;
                }

                if (nome_completo.length == 1) {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Nome da mãe está incompleto!");
                    document.getElementById('labelNomeMae').style.color = 'red';
                    document.getElementById('vch_mae').style.borderColor = 'red';
                    return false;
                }

                // 7º Não é órgão público. Necessário informar o nome do Responsável
                //######################################################################            

                nome = $('#vch_responsavel').val().trim();
                nome_completo = nome.split(' ');

                if ((nome === '') && (idade <18) ) {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Nome do Responsável deve ser preenchido!");
                    document.getElementById('labelNomeResponsavel').style.color = 'red';
                    document.getElementById('vch_responsavel').style.borderColor = 'red';
                    return false;
                }

                if ((nome_completo.length == 1) && (idade <18)) {
                    $("#msg").trigger("click");
                    $("#msg_text").text("Nome do Responsável está incompleto!");
                    document.getElementById('labelNomeResponsavel').style.color = 'red';
                    document.getElementById('vch_responsavel').style.borderColor = 'red';
                    return false;
                }
            }
        
        
        //     //######################################################################    
        //     // 8º Valida e-mail 
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
        //     // 8º Valida e-mail 
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
        //     // 9º Valida o CPF 
        //     //###################################################################### 

        //     let cpf_value = $('#vch_cpf').val();

        //     if (cpf_value != '') {
        //         if (!validarCPF(cpf_value)) {
        //             $("#msg").trigger("click");
        //             $("#msg_text").text("CPF inválido!");
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
        //         alert('Data de nascimento não pode ser maior que data atual.');
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
            //se mês atual for menor que o nascimento, nao fez aniversario ainda; (26/10/2009)
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

            // compara se a data informada é maior que a data atual
            // e retorna true ou false
            return date >= today ? true : false;
        }

        function habilitarRadio(radio) {
            if (radio.value == 0) {
                document.getElementById('vch_orgaopublico').setAttribute("disabled", "disabled");
                document.getElementById('vch_orgaopublico').value = '';
                // Controla os campos obrigatórios.                 
                document.getElementById('spanAsteristicoMae').innerText = '*';
                document.getElementById('spanAsteristicoResp').innerText = '*';
                document.getElementById('spanAsteristicoOrgao').innerText = '';


            } else {
                document.getElementById('vch_orgaopublico').removeAttribute("disabled");
                document.getElementById('vch_orgaopublico').value = '';
                // Controla os campos obrigatórios. 
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
                    alert('Requisição Falhou !');
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