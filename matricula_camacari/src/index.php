<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
session_start();

//verifica se usuario ta logado
 if(!isset($_SESSION['id_usuario'])){
     echo "<script>

     alert('Sessão expirada, faça login novamente.');
             history.go(-1);
     </script>
     ";
     die();
}



$nome = @$_SESSION ['nome'];
$partes = explode (' ',$nome);
$primeiroNome = array_shift($partes);
$ultimoNome = array_pop($partes);
$email = @$_SESSION ['email'];
clearstatcache();
?>

<!DOCTYPE html>
<html>
<head>
    
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Portal de Lista de Espera</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
<!--    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">-->
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="css/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="css/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="css/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="css/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="css/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
   <link rel="stylesheet" href="css/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="css/plugins/summernote/summernote-bs4.css">
    <!-- Google Font: Source Sans Pro -->

    <!-- Multi-select - Tela de administração --> 
    <link rel="stylesheet" type="text/css" href="../src/js/multi-select-styles.css">
<!--    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">-->
<script type ="text/javascript" language="javascript" src="../src/app/aluno/ajax/ScriptAjax.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark navbar-success">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" id="btn-push_menu" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-home" onclick="getForm('app/dashboard/dashboard.php')"></i>
                </a>
           </li>
            <li class="nav-item dropdown">
                <a class="nav-link" href="../src/manual/manualportal.pdf" target="_blank">
                  Suporte
                </a>
            </li>
           <!-- Notifications Dropdown Menu -->
             <li class="nav-item dropdown">
                <a class="nav-link" href="../../index.php">
                  Sair
                </a>

            </li>
<!--            <li class="nav-item">-->
<!--                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">-->
<!--                    <i class="fas fa-th-large"></i>-->
<!--                </a>-->
<!--            </li>-->
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar elevation-4 sidebar-light-success .sidebar-toggle-btn">
        <!-- Brand Logo -->
        <a class="brand-link navbar-success">

            <span class="brand-text font-weight-light" style="color: white" ><b>Secretaria de Educacao</b></span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="css/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?php echo $primeiroNome ." ".$ultimoNome; ?></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->

<!--                    <li class="nav-item has-treeview">-->
<!--                        <a href="#" class="nav-link">-->
<!--                            <i class="fas fa-user-graduate"></i>-->
<!--                            <p>-->
<!--                                Aluno-->
<!--                                <i class="fas fa-angle-left right"></i>-->
<!--                            </p>-->
<!--                        </a>-->
<!--                        <ul class="nav nav-treeview">-->
<!--                            <li class="nav-item">-->
<!--                                <a style="cursor: pointer" onclick="getForm('app/aluno/form_inserir_aluno.php')" class="nav-link">-->
<!--                                    <i class="fas fa-plus"></i>-->
<!--                                    <p>Inserir Alunos</p>-->
<!--                                </a>-->
<!--                            </li>-->
<!--                            <li class="nav-item">-->
<!--                                <a style="cursor: pointer" onclick="getForm('app/aluno/lista_matricular.php')" class="nav-link">-->
<!--                                    <i class="far fa-list-alt"></i>-->
<!--                                    <p>Lista Alunos</p>-->
<!--                                </a>-->
<!--                            </li>-->
<!--                            <li class="nav-item">-->
<!--                                <a style="cursor: pointer" onclick="getForm('app/aluno/rel_alunos_bairro.php')" class="nav-link">-->
<!--                                    <i class="fas fa-user-check"></i>-->
<!--                                    <p>Relatï¿½rio Alunos por Bairro</p>-->
<!--                                </a>-->
<!--                            </li>-->
<!---->
<!--                        </ul>-->
<!--                    </li>-->

                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-graduate"></i>
                            <p>
                                Aluno
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                           <!-- <li class="nav-item">
                                <a style="cursor: pointer" onclick="getForm('app/aluno/form_inserir_aluno.php')" class="nav-link">
                                    <i class="fas fa-plus"></i>
                                    <p>Inserir Alunos</p>
                                </a>
                            </li>-->
                            <li class="nav-item">
                                <a style="cursor: pointer" onclick="getForm('app/aluno/lista_matricular.php')" class="nav-link">
                                    <i class="far fa-list-alt"></i>
                                    <p>Lista Alunos</p>
                                </a>
                            </li>


                        </ul>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="fas fa-copy"></i>
                            <p>
                                Relatórios
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a style="cursor: pointer" onclick="getForm('app/aluno/rel_alunos_bairro.php')" class="nav-link">
                                    <i class="fas fa-copy"></i>
                                    <p>Alunos Por Bairro</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a style="cursor: pointer" onclick="getForm('app/aluno/rel_vagas_disponiveis.php')" class="nav-link">
                                   <i class="fas fa-copy"></i>
                                   <p>Vagas Disponiveis Por Escola</p>
                                </a>
                            </li>
			              <!--  <li class="nav-item">
                                <a style="cursor: pointer" onclick="getForm('app/aluno/rel_segm_disponiveis.php')" class="nav-link">
                                   <i class="fas fa-copy"></i>
                                   <p>Vagas Por Segmento</p>
                                </a>
                            </li>-->
                            <li class="nav-item">
                                <a style="cursor: pointer" onclick="getForm('app/aluno/rel_escola_segm_disponiveis.php')" class="nav-link">
                                    <i class="fas fa-copy"></i>
                                    <p>Vagas Por Escola e Segmento</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a style="cursor: pointer" onclick="getForm('app/aluno/rel_aluno_reserva.php')" class="nav-link">
                                    <i class="fas fa-copy"></i>
                                    <p>Alunos Reserva</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a style="cursor: pointer" onclick="getForm('app/aluno/rel_aluno_matriculado.php')" class="nav-link">
                                    <i class="fas fa-copy"></i>
                                    <p>Matriculados no Portal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a style="cursor: pointer" onclick="getForm('app/aluno/rel_aluno_agendado.php')" class="nav-link">
                                    <i class="fas fa-copy"></i>
                                    <p>Alunos Agendados</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark" id="title">Lista de Espera Online 2020</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a id="subtitle1">Home</a></li>
                            <li class="breadcrumb-item active"id="subtitle2" >Lista de Espera Online 2020</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row" id="cp_loading" style="display: none">
                    <div class=" offset-5 col-3"><img src="img/loading.gif" width="100"></div>
                </div>
                <div id="putForm">

                </div>

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer d-none d-md-block">
        <strong>Copyright &copy; 2014-2020 <a href="">Secretaria de Educação de Camaçari</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


<!-- jQuery -->
<script src="css/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="js/index.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="css/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="css/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
 <script src="css/plugins/chart.js/Chart.min.js"></script>
 <!--<script src="js/loader.js"></script>-->
<!-- Sparkline -->
<!--<script src="css/plugins/sparklines/sparkline.js"></script>-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!-- JQVMap -->
<script src="css/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="css/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="css/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="css/plugins/moment/moment.min.js"></script>

<script src="css/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="css/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="css/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="css/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="css/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!--<script src="css/dist/js/pages/dashboard.js"></script>-->
<!-- AdminLTE for demo purposes -->
<script src="css/dist/js/demo.js"></script>
<script type="text/javascript" src="js/jquery.mask.min.js"></script>
<script src="js/aluno/form_alterar_aluno.js"></script>
<script src="js/aluno/lista_matricula_aluno.js"></script>
<script>
    //getForm('app/dashboard/dashboard.php');
</script>

</body>
</html>
