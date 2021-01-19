<?php
//producao
//$ambiente = 'producao';

//homologacao
$ambiente = 'homologacao';


//Estabele�e o tamanha max da imagem para upload de documento
$tamanhoImagemUploadDocumentoAluno = '4000000'; // ex. 4000000 = 4mb







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
