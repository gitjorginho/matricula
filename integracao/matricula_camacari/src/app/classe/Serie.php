<?php
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 02/02/2020
 * Time: 11:56
 */

require_once('Conn.php');

class Serie extends Conn
{
    function __construct()
    {
        self::conect();

    }

    function all(){
        $sql = "select * from escola.serie order by trim(ed11_c_descr)";
        $series = self::$conexao->query($sql);
        return $series;
    }
}