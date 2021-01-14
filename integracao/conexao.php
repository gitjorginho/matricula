<?php
/**
 * Created by Aloisio Carvalho
 * Date: 19/11/2019
 * Time: 16:05
 */

 require_once('config.php');
 require_once('inc_funcao.php');
 
        class Conexao
        {
            public function conn()
            {

                   //verifica se ta configurado pra producao
               if (isProduction()){
                    $servidor = "172.31.99.75";
                    $bancoDeDados = "sge2020";
                }else{
                    $servidor = "172.31.99.76";
                    $bancoDeDados = "sge2020_hom";
                }
                
                $porta = 5432;
                $usuario = "ecidade";
                $senha = "3c1d@d3@2020$09";
                $conexao = pg_connect("host=$servidor port=$porta dbname=$bancoDeDados user=$usuario password=$senha");
                return $conexao;
            }
        }
?>
