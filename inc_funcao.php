<?php

/**
 * Created by Rodolfo Ara�jo
 * Date: 25/08/2000
 * Time: 14:27
 * Finalidade: Compartilhar fun��es em PHP
 */
/*******************************************************************************
                    FUN�?O QUE PREVINE SQL INJECTION
*******************************************************************************/
function f_Anti_Injection($sql)
{
    // remove palavras que contenham sintaxe sql
    
    $sql = preg_replace("/(from|FROM|update|UPDATE|select|SELECT|insert|INSERT|delete|DELETE|where|WHERE|drop|DROP table|show tables|#|\*|--|\\\\)/i","",$sql);
    //limpa espa�os vazio
    $sql = trim($sql);
    //tira tags html e php
    $sql = strip_tags($sql);
    // Adiciona barras invertidas a string
    $sql = addslashes($sql);

    return $sql;
}