<?php
session_start();
$banner_passo = 1;
$img_banner_passo = 'img/guia_04.jpg';
require_once('header.php');

$_SESSION['passo'] = array();



?>



<br>
<br>
<h2 class="text-center">Comprovante</h2>

<style>

</style>
<div class="card-body">
    <iframe class="iframe_comprvante" style="border: 1px solid black" scrolling="auto" src="comprovante_pdf.php" frameborder="0">

    </iframe>
</div>

<div class="form-group">
    <div class="row">
        <div class="col">
            <div class="card-body">
                <a class="col-md-3 btn btn-success" href="index.php">Voltar ao Início</a>
            </div>
        </div>

    </div>
</div>



<?php
require_once('footer.php');

?>