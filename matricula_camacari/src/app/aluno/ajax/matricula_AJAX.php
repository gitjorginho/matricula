<?php
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 30/01/2020
 * Time: 13:18
 */
require_once('../../classe/Matricula.php');

switch ($_GET['funcao']){
    case'matricular':
        $matricula = new Matricula();
        $matricula = $matricula->matricular();
        echo $matricula;
        break;
}