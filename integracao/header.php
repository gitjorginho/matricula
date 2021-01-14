<?php

header("Content-Type: text/html;  charset=ISO-8859-1", true);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="ISO-8859-1" />
    <title>LISTA DE ESPERA - CAMA�ARI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/fichaCadUpdate.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="application/javascript" src="js/jquery.mask.min.js"></script>

</head>


<body>

    <div class="img_header">
        <div class="img_center_top">
            <img class="img-responsive" src="img/titulo.png" width="1000" />
        </div>
    </div>

    <div class="nav_bar">
        <div class="nav_bar_center">
                <nav class="navbar navbar-expand-lg navbar-light mynav" style="background-color: #02CA74; z-index:500; ">
                <h3 class="tituloMenuMobile"> Lista de reserva 2020</h3>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alterna navega��o">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php"><b>Inicio</b></a>
                        </li>
                        &nbsp
                        <!--                   <li class="nav-item">-->
                        <!--                        <a class="nav-link" href="relatorio_bairro_reserva.php"><b>Lista de Alunos com Reserva</b></a>-->
                        <!--                    </li>-->
                        &nbsp
                        <!-- <li class="nav-item">
                        <a class="nav-link" href="relatorio_aluno_reserva.php"><b>Lista de Alunos com Reserva</b></a>
                    </li>-->
                        &nbsp
                        <!--<li class="nav-item">
                        <a class="nav-link" href="lista.php"><b>Lista de Vagas Dispon?veis</b></a>
                    </li>-->
                        &nbsp
                        <li class="nav-item">
                            <a class="nav-link" href="duvidas.php"><b>D�vidas Frequentes</b></a>
                        </li>
                        &nbsp
                        &nbsp
                        &nbsp
                        &nbsp
                        <li class="nav-item" style="s">
                            <a class="nav-link" href="Login.php"><b>Acesso Restrito</b></a>
                        </li>
                    </ul>
                </div>
            </nav>
        
        </div>
    </div>
    <?php
    if (@$banner_passo == 1) {
    ?>
        <div class="guia_status">
            <div class="guia_status_center" >
                <img  style="margin-top: 5px;" src="<?php echo @$img_banner_passo ?>" class="img-responsive imgBanner" width="450" />
            </div>

        </div>
    <?php
    }
    ?>
    <div class="conteiner1">