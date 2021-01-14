<?php
require_once(__DIR__.'/../../../../config.php');

class Conn
{
    static $conexao = null;

    static function conect(){

          if (isProduction()){
            $servidor = "172.31.99.79";
            $bancoDeDados = "sge2020";
          }else{
            $servidor = "172.31.99.76";
            $bancoDeDados = "sge2020_hom";
          }
            
            $porta = 5432;
            $usuario = "ecidade";
            $senha = "3c1d@d3@2020$09";
                   try {
              self::$conexao = new PDO("pgsql:host=$servidor;dbname=$bancoDeDados", $usuario, $senha);
          }catch (PDOException $e){
             echo "Erro de conexão com o banco de dados!";
          }
       return null;
    }

}