<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('conexao.php');
$conexao = new Conexao();
$conn = $conexao->conn();

if((isset($_POST['login'])) && (isset($_POST['senha']))){

    $usuario    = f_Anti_Injection(trim($_POST['login']));
    $senha      = f_Anti_Injection(trim($_POST['senha']));
    
    $senha = md5($senha);
    $senha = sha1($senha);

    $sql_login = "select * from configuracoes.db_usuarios where login = 
    '$usuario' and senha = '$senha'";

    $result = pg_query($conn, $sql_login);
    $testaUsuario  = pg_fetch_assoc($result);
    
    if (pg_num_rows($result) > 0){
        $nome = $testaUsuario ['nome'];
        $email = $testaUsuario ['email'];
        $id_usuario = $testaUsuario ['id_usuario'];
        // Verifica se o usuário tem um perfil atribuido no Sistema para o Acesso Restrito
        $sql_perfil = "select * from configuracoes.db_permherda where id_usuario =  '$id_usuario' and (id_perfil = 18 or id_perfil = 81)";
        // 
    
        $resultperf = pg_query($conn, $sql_perfil);
        $testaPerfil  = pg_fetch_assoc($result);

        if (pg_num_rows($resultperf) == 0){
            $_SESSION['login']  = $usuario;
            $_SESSION['email']  = $email;
            $_SESSION['nome']   = $nome;
            $_SESSION['id_usuario'] = $id_usuario; 

            header('Location:matricula_camacari/src/index.php');
        }else{
           echo"<script>alert('Usuário sem perfil definido, favor contactar o administrador do Sistema!');
           location= 'Login.php';</script>";
        }  
    }else{
     echo"<script>alert('Login ou senha incorreta, favor digitar novamente!');
     location= 'Login.php';</script>";
    }

}else{
     echo"<script>alert('Login ou senha incorreta, favor digitar novamente!');
     location= 'Login.php';</script>";
}
?>