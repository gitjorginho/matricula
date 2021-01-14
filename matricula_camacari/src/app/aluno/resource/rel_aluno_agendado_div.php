<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../../classe/Conn.php');
Conn::conect();

?>

<div class="card-body">
    <iframe class="iframe_comprvante" width="100%"  height="1000" style="border: 1px solid black" scrolling="auto" src="app/aluno/resource/relatorio_alunos_agendados.php?escola=<?php echo $_POST['escola'].'&com_periodo='.@$_POST['com_periodo'].'&date_initial='.$_POST['date_initial'].'&date_end='.$_POST['date_end'] ?>" frameborder="0">

    </iframe>
</div>

