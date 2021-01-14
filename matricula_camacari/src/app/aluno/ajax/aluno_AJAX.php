<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 01/02/2020
 * Time: 09:09
 */
require_once(__DIR__ . '/../../../../../config.php');
require_once('../../classe/Aluno.php');
switch ($_GET['funcao']) {
    case 'findAluno':

        $aluno = new Aluno();
        $alunos = $aluno->findAluno($_GET['aluno'], $_GET['responsavel'], $_GET['data_nascimento'], $_GET['status_id']);
        $html = '';
        $qtd_aluno = 0;

        foreach ($alunos as $al) {
            $qtd_aluno++;
            $get_form = "getForm('app/aluno/form_alterar_aluno.php?codigo={$al['id_alunoreserva']}')";

            $html .= '<tr>
                        <td>' . $al['id_alunoreserva'] . '</td>
                        <td>' . $al['ed47_v_nome'] . '</td>
                        <td>
                            <button class="btn btn-outline-info" onclick="' . $get_form . '">Editar</button>
                        </td>
                    </tr>';
        }

        $_SESSION['registros'] = $qtd_aluno;

        echo $html;
        break;

    case 'atualizar_label_registro':
        $registros = $_SESSION['registros'];
        echo $registros;
        break;

    case 'update_aluno':
        $aluno = new Aluno();
        $resposta = $aluno->updateAluno();
        echo $resposta;
        break;


    case 'storeAluno':
        $aluno = new Aluno();
        $resposta = $aluno->storeAluno();
        echo $resposta;
        break;

    case 'showComprovanteAluno':
        if (!isset($_SESSION['id_usuario'])) {
            echo 'expirou';
            die();
        }
        $_SESSION['codigo'] = $_GET['codigo'];

        if (isProduction()) {
            //producao
            echo 'https://' . $_SERVER['HTTP_HOST'] . '/comprovante_pdf.php';
        } else {
            //homologacao
            //echo 'https://' . $_SERVER['HTTP_HOST'] . '/matricula/comprovante_pdf.php';
            echo 'http://' . $_SERVER['HTTP_HOST'] . '/matricula/comprovante_pdf.php';
        }
        break;
        
        case 'showAutorizacaoMatricula':
        if (!isset($_SESSION['id_usuario'])) {
            echo 'expirou';
            die();
        }
        $_SESSION['codigo'] = $_GET['codigo'];

        if (isProduction()) {
            //producao
            echo 'https://' . $_SERVER['HTTP_HOST'] . '/matricula_camacari/src/app/aluno/autorizacao_matricula_pdf.php?reimpressao=2';
        } else {
            //homologacao
            //echo 'https://' . $_SERVER['HTTP_HOST'] . '/matricula/comprovante_pdf.php';
            echo 'http://' . $_SERVER['HTTP_HOST'] . '/matricula/matricula_camacari/src/app/aluno/autorizacao_matricula_pdf.php?reimpressao=2';
        }
        break;
}
