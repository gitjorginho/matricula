<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
session_start();
require_once('../../classe/Conn.php');
require_once('../../classe/Escola.php');
Conn::conect();
if (!isset($_SESSION['id_usuario'])) {
    echo 'expirou';
    die();
}

$serie = $_GET['serie'];

$Escola = new Escola();
$escolas = $Escola->loadEscolasSerie($serie);

echo "<label id='labelEscola' style='color: red'>".@$msg_avaliado."</label>";
echo "<select required id='cp_escola' name='escola' class='custom-select'>";
echo    "<option value=''>Selecione uma escola</option>";
    foreach ($escolas as $escola) { 
            echo "<option value=".$escola['codigo'].">".$escola['escola']."</option>";
    } 
echo "</select>";
