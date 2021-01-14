<?php
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 29/01/2020
 * Time: 10:50
 */
require_once('Conn.php');

class Localidade extends Conn
{
    function __construct()
    {
        self::conect();
    }

    function all()
    {

    }

    function findEndereco($text)
    {
        $ender = $text;

        $sql = "select j14_codigo,j14_nome, j13_descr, j13_codi 
            from cadastro.ruas r
            inner join ruasbairro rb on r.j14_codigo = rb.j16_lograd
            inner join ruasbairrocep rbc on rbc.j32_ruasbairro = rb.j16_codigo
            inner join bairro b on b.j13_codi = rb.j16_bairro 
            where j14_nome ilike '%{$ender}%'";

        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $enderecos = $stmt->fetchAll();
        return $enderecos;
    }

    function loadEndereco($codigo,$endereco_2)
    {
        $sql = " select j14_codigo,j14_nome as endereco, j13_codi as codigo_bairro,j13_descr as bairro ,j29_cep as cep ,trim(ed261_c_nome) as cidade 
            from cadastro.ruas r
            inner join ruasbairro rb on r.j14_codigo = rb.j16_lograd
            inner join ruasbairrocep rbc on rbc.j32_ruasbairro = rb.j16_codigo
            inner join bairro b on b.j13_codi = rb.j16_bairro 
            inner join ruascep on j29_codigo = j32_ruascep
	        inner join censomunic on j13_i_censomunic = ed261_i_codigo 	 
            where j14_codigo = {$codigo} and j13_codi = {$endereco_2}";

        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $endereco = $stmt->fetch(PDO::FETCH_ASSOC);
        return $endereco;

    }


    function loadLocalidade($codigo){

        $sql= " select * 
                from territorio.localidade 
                where loc_i_bairro = $codigo";

        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $localidade = $stmt->fetchAll();
        return $localidade;
    }
}