<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
session_start();
require_once('../../classe/Conn.php');
Conn::conect();
if (!isset($_SESSION['id_usuario'])) {
    echo 'expirou';
    die();
}

$sql_consula = "
    select 
        rtrim(ed18_c_email) as email
    from reserva.alunoreserva AR
    join reserva.escolareserva ER 
        on ER.id_alunoreserva = AR.id_alunoreserva
    left join escola.escola E
        on ER.ed56_i_escola = E.ed18_i_codigo
    where 
        AR.id_alunoreserva = {$_SESSION['codigo']}";

$stmt = Conn::$conexao->prepare($sql_consula);
$stmt->execute();
$resultconsulta = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SESSION['statusAluno'] == 7){

        echo              "<p style='text-align: center'>Imprimir o atestado de vaga?</p>";
        echo              "<div class='form-group'>";
        echo                   "<div class='row'>";
        echo                       "<div class='col' style='text-align: right'>"; 
        echo                            "<input type='radio' id='ConfirmacaoImpressaoSim' checked='true' name='confirmacaoImpressao' value='1'>";
        echo                            "<label for='ConfirmacaoImpressaoSim'>Sim</label><br>";
        echo                        "</div>";
        echo                        "<div class='col'>"; 
        echo                            "<input type='radio' id='ConfirmacaoImpressaoNao' name='confirmacaoImpressao' value='0'>";
        echo                            "<label for='ConfirmacaoImpressaoNao'>Não</label><br>";
        echo                        "</div>";    
        echo                   "</div>"; 
        echo              "</div>";

        if ($resultconsulta['email']!=''){
            echo              "<div class='form-group'>";
            echo                  "<div class='row'>";
            echo                       "<div class='col' style='text-align: center'>"; 
            echo                            "<input type='checkbox' class='checkbox' id='NotificarEscola' name='NotificarEscola' value='1'>"; 
            echo                            "<label for='NotificarEscola'>&nbsp;Notificar a escola da autorização de matrícula.</label><br>";
            echo                       "</div>";        
            echo                  "</div>";
            echo              "</div>";    
            echo              "<div class='modal-footer'>";
            echo                  "<button type='button' onclick='NotificaEscola(".$_SESSION['paginacao'].")' class='btn btn-outline-success'>Salvar</button>";
            echo              "</div>";
        }else{
            echo              "<div class='form-group'>";
            echo                  "<div class='row'>";
            echo                       "<div class='col' style='text-align: center'>"; 
            echo                            "<input type='checkbox' class='checkbox' id='NotificarEscola' disabled name='NotificarEscola' value='1'>"; 
            echo                            "<label for='NotificarEscola'>&nbsp;Notificar a escola da autorização de matrícula.</label><br>";
            echo                       "</div>";        
            echo                  "</div>";
            echo              "</div>";    
            echo              "<p style='text-align: center; color: red;'>Escola sem E-mail!</p>";
            echo              "<div class='modal-footer'>";
            echo                  "<button type='button' onclick='NotificaEscola(".$_SESSION['paginacao'].")' class='btn btn-outline-success'>Salvar</button>";
            echo              "</div>";

        }

    }else{
        echo              "<div class='modal-body'>";
        echo                  "<p style='text-align: center'>Dados do aluno alterados com sucesso!</p>"; 
        echo              "</div>"; 

        echo              "<div class='modal-footer'>";
        echo                  "<button type='button' onclick='lastList(".$_SESSION['paginacao'].")' class='btn btn-outline-success'>OK</button>";
        echo              "</div>";
    }