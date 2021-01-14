<?php
session_start();
$banner_passo = 1;
$img_banner_passo = 'img/guia_01.jpg';
require_once('header.php');
require_once('conexao.php');

$conexao = new Conexao();
$conn = $conexao->conn();

$passo = $_SESSION['passo'];
if (!in_array(0, $passo)) {
    header('Location:index.php');
}

//só entra se foi enviado um post
if (isset($_POST['vch_nome'])) {

    //verifica se aluno ja foi cadastrado----------------------------------
    $nome_aluno     = f_Anti_Injection(trim($_POST['vch_nome']));
    $vch_mae        = f_Anti_Injection(trim($_POST['vch_mae']));
    $data_nasc      = dateToDatabase(trim($_POST['sdt_nascimento']));

    $sql_verifica_cadastro = "
              select id_alunoreserva from reserva.alunoreserva 
              where sem_acentos(ed47_v_nome) ilike sem_acentos('$nome_aluno') and  sem_acentos(ed47_v_mae ) ilike sem_acentos('$vch_mae') and ed47_d_nasc  = '$data_nasc' ";

    $result = pg_query($conn, $sql_verifica_cadastro);

    $verifica_cadastro = pg_fetch_assoc($result);

    if (pg_numrows($result) > 0) {
        // se aluno ja cadastrado
        header("Location: editar_matricula.php?alunocadastrado=1&id_alunoreserva=" . $verifica_cadastro['id_alunoreserva'] . "");
        die('Aluno ja cadastrado.');
    }
    //-----------------------------------------------------------------------
    // pega o codigo do aluno caso exista na tabela aluno do sge
    //    $sql_verifica_aluno_sge = "
    //             select * from aluno
    //             where ed47_v_nome ilike '%$nome_aluno%' and ed47_c_nomeresp ilike '%$vch_nome_resp%' and ed47_d_nasc  = '$data_nasc'
    //    ";
    //    //  die($sql_verifica_aluno_sge);
    //    $result = pg_query($conn, $sql_verifica_aluno_sge);
    //    $cadastro_aluno_sge = pg_fetch_assoc($result);
    //    $_SESSION['codigo_aluno_sge'] = $cadastro_aluno_sge['ed47_i_codigo'];
    //-----------------------------------------------------------

    $num_cpf = trim(str_replace(['.', '-'], '', $num_cpf));


    $nome_responsavel = trim($_POST['vch_responsavel']);

    //if ($num_cpf == ''){
    $sql_verifica_cpf_responsavel = "
            
             select reserva_aluno, trim(ed47_c_nomeresp) as responsavel 
             from escola.matriculareserva
             inner join escola.aluno on reserva_aluno = ed47_i_codigo 
             where reserva_cpfresponsavel = '$num_cpf' and reserva_cpfresponsavel <> '' limit 1
       
       ";
    // echo $sql_verifica_cpf_responsavel;

    $result = pg_query($conn, $sql_verifica_cpf_responsavel);
    $responsavel = pg_fetch_assoc($result);

    $msg_responsavel_cpf = true;

    if (pg_num_rows($result) == 1) {
        if ($responsavel['responsavel'] == $nome_responsavel) {
            if (!in_array(1, $passo)) {
                array_push($passo, 1);
                $_SESSION['passo'] = $passo;
            }
            $_SESSION = array_merge($_SESSION, $_POST);
            header('Location: ficha_cadastro_endereco.php');
        } else {
            $msg_responsavel_cpf = true;
        }
    } else {
        if (!in_array(1, $passo)) {
            array_push($passo, 1);
            $_SESSION['passo'] = $passo;
        }
        $_SESSION = array_merge($_SESSION, $_POST);
        header('Location: ficha_cadastro_endereco.php');
    }
    //}
}

$nome = isset($_SESSION['vch_nome']) ? $_SESSION['vch_nome'] : '';
$data_nascimento = isset($_SESSION['sdt_nascimento']) ? $_SESSION['sdt_nascimento'] : '';
$sexo = isset($_SESSION['vch_sexo']) ? $_SESSION['vch_sexo'] : '';
$nome_mae = isset($_SESSION['vch_mae']) ? $_SESSION['vch_mae'] : '';
$nome_responsavel = isset($_SESSION['vch_responsavel']) ? $_SESSION['vch_responsavel'] : '';
$cpf = isset($_SESSION['vch_cpf']) ? $_SESSION['vch_cpf'] : '';
$serie = isset($_SESSION['vch_serie']) ? $_SESSION['vch_serie'] : '';
$teste = $serie;
$vch_orgaopublico = isset($_SESSION['vch_orgaopublico']) ? $_SESSION['vch_orgaopublico'] : '';
$vch_email = isset($_SESSION['vch_email']) ? $_SESSION['vch_email'] : '';
$radio = isset($_SESSION['radio']) ? $_SESSION['radio'] : '';

//$Check_orgaopublico = isset($_SESSION['Check_orgaopublico']) ? $_SESSION['Check_orgaopublico'] : '';
?>
<div class="centr">
    <br>
    <h2 class="text-center">Informações Iniciais</h2>
    <form id="ficha" name="ficha" method="post" action="#">
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="vch_nome" id="labelNome">Nome do aluno:</label><span>*</span>
                        <!--<input required class="form-control" type="text" name="vch_nome" id="vch_nome" value="<?php echo $nome ?>" onkeyup="this.value = this.value.toUpperCase();" onKeyPress="mudarCorCampo('labelNome', 'vch_nome')" />-->
                        <input required class="form-control" type="text" name="vch_nome" id="vch_nome" value="<?php echo $nome ?>"  onKeyPress="mudarCorCampo('labelNome', 'vch_nome');return letras();" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label id="labelDataNascimento" for="sdt_nascimento">Data de nascimento:</label><span>*</span>
                        <div class='input-group date' id='datetimepicker3'>
                            <input required value="<?php echo $data_nascimento ?>" class="form-control" type="text" name="sdt_nascimento" id="sdt_nascimento" onchange="mudarCorCampo('labelDataNascimento', 'sdt_nascimento');testaIdade(this.value);">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label id="labelSexo" for="cp_sexo">Sexo:</label><span>*</span>
                        <select required class="browser-default custom-select" name="vch_sexo" id="cp_sexo" onchange="mudarCorCampo('labelSexo', 'cp_sexo')">
                            <option selected></option>
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                        <script>
                            $('#cp_sexo').val(<?php echo "'$sexo'" ?>);
                        </script>
                    </div>
                    <?php
                    $sql_serie = "select ed11_i_codigo, trim(ed11_c_descr) as ed11_c_descr from escola.serie where ed11_i_codigo not in(50,51,52,53,54) order by ed11_c_descr";
                    $result = pg_query($conn, $sql_serie);
                    $serie = pg_fetch_all($result);
                    ?>
                    <div class="col-md-4">
                        <label id="labelSerie" for="cp_serie">Série:</label><span>*</span>
                        <select required class="custom-select" id="cp_serie" name="vch_serie" onchange="mudarCorCampo('labelSerie', 'cp_serie')">
                            <option selected></option>
                            <?php
                            foreach ($serie as $serie_selec) {
                                echo '<option value=' . $serie_selec['ed11_i_codigo'] . '>' . $serie_selec['ed11_c_descr'] . "</option>";
                            }
                            ?>
                        </select>
                        <script>
                            $('#cp_serie').val('<?php echo $teste ?>');
                        </script>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label id="labelQuestionamento" for="vch_email" title="Aluno acolhido pelo poder público."><b>O cadastro é realizado por órgão público?</b></label>
                </div>
            </div>
            <!--                <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="Check_orgaopublico" onchange="habilitar()">
                                        <label class="form-check-label" for="Check_orgaopublico">Sim</label>
                                    </div>
                                </div>
                            </div>
                            <br>-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <input type="radio" class="form-check" id="radioNao"  name="radio" value="0" <?php
                            if ($radio == 0) {
                                echo'checked';
                            } else {
                                echo'';
                            }
                            ?> onchange="habilitarRadio(this)">
                        <label for="male">Não</label><br>
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
            </div>    
            <!--<br>-->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label id="labelOrgaoPublico" for="vch_orgaopublico">Descrição do orgão público:</label><span id="spanAsteristicoOrgao"></span>
                        <!--<input autocomplete="off" class="form-control" type="text" id="vch_orgaopublico" name="vch_orgaopublico" id="vch_orgaopublico" value="<?php echo $vch_orgaopublico ?>" onkeyup="this.value = this.value.toUpperCase();" disabled onKeyPress="mudarCorCampo('labelOrgaoPublico', 'vch_orgaopublico')">-->
                        <input autocomplete="off" class="form-control" type="text" id="vch_orgaopublico" name="vch_orgaopublico" id="vch_orgaopublico" value="<?php echo $vch_orgaopublico ?>" disabled onKeyPress="mudarCorCampo('labelOrgaoPublico', 'vch_orgaopublico');return letras();">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label id="labelNomeMae" for="vch_mae">Nome da mãe:</label><span id="spanAsteristicoMae">*</span>
                        <!--<input value="<?php echo "$nome_mae" ?>" class="form-control" type="text" name="vch_mae" id="vch_mae" onkeyup="this.value = this.value.toUpperCase();" onKeyPress="mudarCorCampo('labelNomeMae', 'vch_mae')">-->
                        <input value="<?php echo "$nome_mae" ?>" class="form-control" type="text" name="vch_mae" id="vch_mae" onKeyPress="mudarCorCampo('labelNomeMae', 'vch_mae');return letras();">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label id="labelNomeResponsavel" for="vch_responsavel">Nome do responsável:</label><span id="spanAsteristicoResp">*</span>
                        <!--<input autocomplete="off" class="form-control" type="text" name="vch_responsavel" id="vch_responsavel" value="<?php echo $nome_responsavel ?>" onkeyup="this.value = this.value.toUpperCase();" onKeyPress="mudarCorCampo('labelNomeResponsavel', 'vch_responsavel')">-->
                        <input autocomplete="off" class="form-control" type="text" name="vch_responsavel" id="vch_responsavel" value="<?php echo $nome_responsavel ?>" onKeyPress="mudarCorCampo('labelNomeResponsavel', 'vch_responsavel');return letras();">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label id="labelEmail" for="vch_email">Email do responsável:</label>
                        <input autocomplete="off" class="form-control" type="email" name="vch_email" id="vch_email" value="<?php echo $vch_email ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-5">
                        <label label="labelCpf" for="vch_cpf" id ="labelCpf">CPF do responsável:</label>
                        <input value="<?php echo $cpf ?>" class="form-control" type="text" name="vch_cpf" id="vch_cpf" onKeyPress="mudarCorCampo('labelCpf', 'vch_cpf')">
                    </div>
                </div>
            </div>
            <!--            <br>
                        <h4><b>Caso seja Órgão Público</b></h4>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label id="labelOrgaoPublico" for="vch_orgaopublico">Descrição do orgão público:</label>
                                    <input autocomplete="off" class="form-control" type="text" id="vch_orgaopublico" name="vch_orgaopublico" id="vch_orgaopublico" value="<?php echo $vch_orgaopublico ?>" onkeyup="this.value = this.value.toUpperCase();" disabled onKeyPress="mudarCorCampo('labelOrgaoPublico', 'vch_orgaopublico')">
                                </div>
                            </div>
                        </div>
                        <br>-->
            <div class="row">
                <div class="col-md-12">
                    <a href="index.php" class="btn btn-secondary col-md-2" type="button">Voltar</a>
                    <div class="d-md-none" style="margin:10px;"></div>
                    <!--<input class="btn btn-success col-md-2" type="submit" onclick="return valida()" value="Prosseguir">-->
                    <button type="button" id="ProsseguirCadastro" class="btn btn-success col-md-2" onClick="Javascript:GravarForm(document.Form);"> Prosseguir</button>
                </div>
            </div> 
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label label="labelAsterisco" style="font-size: 11px">(*)Campos obrigatórios</label>
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

    <script>
        function letras(){
            tecla = event.keyCode;
            if (tecla >= 48 && tecla <= 57){
                return false;
            }else{
               return true;
            }
        }
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
        function testaIdade(data){
           var idade = calculaIdade(data);
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

        function habilitar() {
            if (document.getElementById('Check_orgaopublico').checked) {
                document.getElementById('vch_orgaopublico').removeAttribute("disabled");
            } else {
                document.getElementById('Check_orgaopublico').value = ''; //Evita que o usuário defina um texto e desabilite o campo após realiza-lo
                document.getElementById('vch_orgaopublico').setAttribute("disabled", "disabled");
                document.getElementById('vch_orgaopublico').value = '';
            }
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

        function GravarForm(frm)
        {
            $result = valida();

            if ($result != false) {

                document.forms["ficha"].submit();
            }
        }
        $('#vch_cpf').mask('000.000.000-00', {
            reverse: true
        });
        $('#sdt_nascimento').mask('00/00/0000');

        function focarNoCampo() {
            document.getElementById('vch_nome').focus();
            //#ced4da - cinza
            //#c6d1de - azul
        }

        function mudarCorCampo(nomeDoLabel, nomeDoCampo) {
            document.getElementById(nomeDoLabel).style.color = 'black';
            document.getElementById(nomeDoCampo).style.borderColor = '#ced4da';
        }

        function valida() {
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
                ;
                return false;
            }

            if (nome_completo.length == 1) {
                $("#msg").trigger("click");
                $("#msg_text").text("Nome do aluno está incompleto!");
                document.getElementById('labelNome').style.color = 'red';
                document.getElementById('vch_nome').style.borderColor = 'red';
                ;
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
                ;
                return false;
            }

            if (compareDates($('#sdt_nascimento').val())) {
                $("#msg").trigger("click");
                $("#msg_text").text("Data de nascimento não pode ser maior que a data atual!");
                document.getElementById('labelDataNascimento').style.color = 'red';
                document.getElementById('sdt_nascimento').style.borderColor = 'red';
                ;
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

            //######################################################################        
            // 3º Valida a seleção do Sexo
            //######################################################################            

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

            //######################################################################    
            // 8º Valida e-mail 
            //###################################################################### 

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

            //######################################################################    
            // 9º Valida o CPF 
            //###################################################################### 

            let cpf_value = $('#vch_cpf').val();

            if (cpf_value != '') {
                if (!validarCPF(cpf_value)) {
                    $("#msg").trigger("click");
                    $("#msg_text").text("CPF inválido!");
                    document.getElementById('labelCpf').style.color = 'red';
                    document.getElementById('vch_cpf').style.borderColor = 'red';
                    return false;
                }
            }

        }

        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, '');
            if (cpf == '')
                return false;
            // Elimina CPFs invalidos conhecidos
            if (cpf.length != 11 ||
                    cpf == "00000000000" ||
                    cpf == "11111111111" ||
                    cpf == "22222222222" ||
                    cpf == "33333333333" ||
                    cpf == "44444444444" ||
                    cpf == "55555555555" ||
                    cpf == "66666666666" ||
                    cpf == "77777777777" ||
                    cpf == "88888888888" ||
                    cpf == "99999999999")
                return false;
            // Valida 1o digito
            add = 0;
            for (i = 0; i < 9; i++)
                add += parseInt(cpf.charAt(i)) * (10 - i);
            rev = 11 - (add % 11);
            if (rev == 10 || rev == 11)
                rev = 0;
            if (rev != parseInt(cpf.charAt(9)))
                return false;
            // Valida 2o digito
            add = 0;
            for (i = 0; i < 10; i++)
                add += parseInt(cpf.charAt(i)) * (11 - i);
            rev = 11 - (add % 11);
            if (rev == 10 || rev == 11)
                rev = 0;
            if (rev != parseInt(cpf.charAt(10)))
                return false;
            return true;
        }

        function compareDates(date) {
            let parts = date.split('/') // separa a data pelo caracter '/'
            let today = new Date() // pega a data atual

            date = new Date(parts[2], parts[1] - 1, parts[0]) // formata 'date'

            // compara se a data informada é maior que a data atual
            // e retorna true ou false
            return date >= today ? true : false;
        }

        function lengthDate(date) {
            if (date.length != 10) {
                return true;
            }
            return false;
        }
        function validaDat(valor) {
            var date = valor;
            var ardt = new Array;
            var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
            ardt = date.split("/");
            erro = false;
            if (date.search(ExpReg) == -1) {
                erro = true;
            }
            if (((ardt[1] == 4) || (ardt[1] == 6) || (ardt[1] == 9) || (ardt[1] == 11)) && (ardt[0] > 30))
                erro = true;
            if (ardt[1] == 2) {
                if ((ardt[0] > 28) && ((ardt[2] % 4) != 0))
                    erro = true;
                if ((ardt[0] > 29) && ((ardt[2] % 4) == 0))
                    erro = true;
            }
            return erro;
        }

        function validEmail(value) {
            var valid = true;
            var emails = value.replace(';', ',').split(",");

            jQuery.each(emails, function () {
                if (jQuery.trim(this) != '')
                {
                    if (!jQuery.trim(this).match(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i))
                        valid = false;
                }
            });
            return valid;
        }
        ;
        function validaEmail(value) {
            var valid = true;
            var emails = value.replace(';', ',').split(",");

            jQuery.each(emails, function () {
                if (jQuery.trim(this) != '')
                {
                    if (!jQuery.trim(this).match(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i))
                        valid = false;
                }
            });
            return true;
        }
        testaIdade(document.getElementById('sdt_nascimento').value);
        // Desabilita o imput do orgão quando voltar no cadastramento.
        if (document.getElementById('radioNao').checked) {
            document.getElementById('vch_orgaopublico').setAttribute("disabled", "disabled");
            document.getElementById('vch_orgaopublico').value = '';
            document.getElementById('spanAsteristicoMae').innerText = '*';
            document.getElementById('spanAsteristicoResp').innerText = '*';
            document.getElementById('spanAsteristicoOrgao').innerText = '';

        } else {
            document.getElementById('vch_orgaopublico').removeAttribute("disabled");
            document.getElementById('spanAsteristicoMae').innerText = '';
            document.getElementById('spanAsteristicoResp').innerText = '';
            document.getElementById('spanAsteristicoOrgao').innerText = '*';
        }

    </script>
    <?php
    //var_dump($_SESSION);
    if (@$msg_responsavel_cpf == true) {
        echo "    
           <script>
               $('#msg').trigger('click');
               $('#msg_text').text('CPF já cadastrado !');      
           </script>
           ";
    }

    require_once('footer.php');

    function dateToDatabase($date) {
        $date = explode('/', $date);
        $date_to_database = "$date[0]-$date[1]-$date[2]";
        return $date_to_database;
    }
    ?>