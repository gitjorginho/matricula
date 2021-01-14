<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../../classe/Conn.php');
Conn::conect();

?>

<div class="card-body">
    <iframe class="iframe_comprvante" width="100%"  height="755" style="border: 0px solid black" scrolling="auto" src="app/aluno/resource/relatorio_aluno_matriculado_excel.php?formato=<?php echo $_POST['formato']?>" frameborder="0">

    </iframe>
</div>

