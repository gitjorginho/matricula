<?php
session_start();
require_once('header.php');

$passo = $_SESSION['passo'];
if (!in_array(1, $passo)) {
    header('Location:index.php');
}

$nome_aluno     = strtoupper($_SESSION['vch_nome']);
$data_nascimento= $_SESSION['sdt_nascimento'];
$nome_mae       = $_SESSION['vch_mae'];

include_once("conexao.php");
$conexao = new Conexao();
$conn = $conexao->conn();

if (isset($_POST['vch_endereco'])) {
    if (!in_array(2, $passo)) {
        array_push($passo, 2);
        $_SESSION['passo'] = $passo;
    }
    $_SESSION = array_merge($_SESSION, $_POST);
    header('Location: ficha_cadastro_opcao.php');
}


?>
<div class="centr">
    <br>
    <h2 class="text-center">Documentos Necessários</h2>
    <form id="ficha" method="post" action="#">

</form>

<script type="text/javascript">
    $(document).ready(function () {
        $("#btndn").removeClass("disabled").addClass("active");
    });

    function GravarForm(frm)
    {
        $result = valida();
        if ($result != false) {
            document.forms["ficha"].submit();
        }
    }

    jQuery(function ($) {
        $("#vch_telefone").mask("(99) 9 9999-9999");
    });

    function valida()
    {

    }

</script>


<?php
require_once('footer.php');
?>