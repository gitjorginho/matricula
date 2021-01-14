<?php
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 29/01/2020
 * Time: 11:14
 */
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../../classe/Localidade.php');
switch ($_GET['funcao']) {
    case'localizar_endereco':

        $localidade = new Localidade();
        $enderecos = $localidade->findEndereco($_GET['endereco']);

        foreach ($enderecos as $endereco) {
            echo "<option data-endereco='{$endereco['j13_codi']}' value='{$endereco['j14_codigo']}'>" . $endereco['j14_nome'] . " - " . $endereco['j13_descr'] . "</option>";
        }
        break;

    case'carregar_endereco':

        $localidade = new Localidade();
        $endereco = $localidade->loadEndereco($_GET['endereco'],$_GET['endereco_2']);

        $enderOBJ = new stdClass();
        $enderOBJ->cep = "{$endereco['cep']}";
        $enderOBJ->cidade = "{$endereco['cidade']}";
        $enderOBJ->bairro = trim(utf8_encode($endereco['bairro']));
        $enderOBJ->codigo_bairro = trim($endereco['codigo_bairro']);
        $enderOBJ->endereco = "{$endereco['endereco']}";

        echo json_encode($enderOBJ);
        break;

    case'carregar_localidade':

        $localidade = new Localidade();
        $localidades = $localidade->loadLocalidade($_GET['codigo_localidade']);

        echo '<option></option>';
        foreach ($localidades as $localidade) {
            echo '<option value=' . $localidade['loc_i_cod'] . '>' . $localidade['loc_v_nome'] . "</option>";
        }

        break;

    case'carregar_escolas':
        $localidade = new Escola();
        $localidades = $localidade->loadLocalidade($_GET['codigo_localidade']);
        break;


}