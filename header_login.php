<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="ISO-8859-1"/>
    <title>LISTA DE ESPERA - CAMAÇARI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="application/javascript" src="js/jquery.mask.min.js"></script>

</head>


<body>

<div class="img_header">
    <div class="img_center_top">
        <img src="img/titulo.png" width="1000"/>
    </div>
</div>

<?php
if (@$banner_passo == 1) {
    ?>
    <div class="guia_status">
        <div class="guia_status_center">
            <img src="<?php echo @$img_banner_passo ?>" width="600"/>
        </div>

    </div>
    <?php
}
?>
<div class="conteiner1">




