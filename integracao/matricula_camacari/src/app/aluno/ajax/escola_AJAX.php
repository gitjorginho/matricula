<?php
session_start();
//die (var_dump($_SESSION));
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 29/01/2020
 * Time: 15:52
 */
require_once('../../classe/Escola.php');

switch ($_GET['funcao']) {

    case'carregar_escolas':
        $obj_escola = new Escola;
        $escolas = $obj_escola->loadEscolas($_GET['data_nascimento'], $_SESSION['cp_serie']);
        echo "<option value=''>Selecione uma Escola</option>";
        foreach ($escolas as $escola) {
            echo "<option value='{$escola['codigo']}'>".utf8_encode($escola['escola'])."</option>";
        }
        break;

    case'carregar_turmas':
        $obj_escola = new Escola;
        $turmas = $obj_escola->loadTurmas($_GET['data_nascimento'],$_GET['codigo_escola'], $_SESSION['cp_serie']);

        echo '<option selected value="">Selecione uma turma</option>';

        foreach ($turmas as $turma) {
            echo '<option value=' . utf8_encode($turma['ed57_i_codigo']) . '>' . 'Turma: ' . utf8_encode($turma['turma']) . "- Serie: " . utf8_encode($turma['serie']) . '- Turno: ' . utf8_encode($turma['turno']) . "</option>";
        }

        break;
    case'carregar_dados_turmas':

        $obj_escola = new Escola;
        $dados_turma = $obj_escola->carregarDadosTurma($_GET['codigo_turma'],$_GET['codigo_escola']);
        $vagas_turma = $obj_escola->getVagasTurma($_GET['codigo_turma']);
        $alunos_matriculado = $obj_escola->getAMatriculadoTurma($_GET['codigo_turma']);
        $dados = new stdClass();
        $dados->curso = utf8_encode(trim($dados_turma['ed29_c_descr']));
        $dados->base = utf8_encode(trim($dados_turma['ed31_c_descr']));
        $dados->calendario = utf8_encode(trim($dados_turma['ed52_c_descr']));
        $dados->etapa = utf8_encode(trim($dados_turma['ed11_c_descr']));
        $dados->turno = utf8_encode(trim($dados_turma['ed15_c_nome']));
        $dados->vagas_turma = utf8_encode(trim($vagas_turma));
        $dados->alunos_matriculado = utf8_encode(trim($alunos_matriculado));
        $dados->vagas_disp = utf8_encode(trim($vagas_turma - $alunos_matriculado));

        echo json_encode($dados);

        break;
}