<?php
//producao
//$ambiente = 'producao';

//homologacao
$ambiente = 'homologacao';


function isProduction(){

    global $ambiente; 
    
    if ($ambiente == 'producao'){
     //se é producao return true
        return true;
    }else{
     // se não é homologacao  
        return false;
    }
    
}
