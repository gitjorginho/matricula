<?php
/*******************************************************************************
  ESTA FUNÇÃO RECEBE UMA DATA E RETORNA POR EXTENSO 
*******************************************************************************/
function valorExtenso($dataEntrada){
    $dataEntrada = strtotime($dataEntrada);
    
    $data = date('D',$dataEntrada);
    $mes = date('M',$dataEntrada);
    $dia = date('d',$dataEntrada);
    $ano = date('Y',$dataEntrada);
 
    $semana = array(
        'Sun' => 'Domingo',
        'Mon' => 'Segunda-Feira',
        'Tue' => 'Terca-Feira',
        'Wed' => 'Quarta-Feira',
        'Thu' => 'Quinta-Feira',
        'Fri' => 'Sexta-Feira',
        'Sat' => 'Sábado'
    );
 
    $mes_extenso = array(
        'Jan' => 'Janeiro',
        'Feb' => 'Fevereiro',
        'Mar' => 'Marco',
        'Apr' => 'Abril',
        'May' => 'Maio',
        'Jun' => 'Junho',
        'Jul' => 'Julho',
        'Aug' => 'Agosto',
        'Nov' => 'Novembro',
        'Sep' => 'Setembro',
        'Oct' => 'Outubro',
        'Dec' => 'Dezembro'
    );

 $dataSaida= $semana["$data"] . ", {$dia} de " . $mes_extenso["$mes"] . " de {$ano}";

 return $dataSaida;	
}	
/*******************************************************************************
  ESTA FUNÇÃO FORMATA O NÚMERO DE TELEFONE PARA IMPRESSÃO
*******************************************************************************/
function formataTelefone($num){
    IF (strlen($num) == 12){
        $resulteTelefone = "(".substr($num,0,2).") ".substr($num,2,6)."-".substr($num,8);
    }elseif (strlen($num) == 14){
        $resulteTelefone = "(".substr($num,0,2).") ".substr($num,2,7)."-".substr($num,9);
    }else{
        $resulteTelefone = $num;
    }
 return $resulteTelefone;	
}
/*******************************************************************************
  ESTA FUNÇÃO GERA UM CÓDIDO DE SEGURANÇA COM OS DADOS ENVIADOS
*******************************************************************************/
function criptografia($dadosCriptografia){
    $resultCriptografica = hash('ripemd160', $dadosCriptografia);
     return ($resultCriptografica);	
}
function retornaPrimeiroUltimoNome($nome)
{
$temp = explode(" ",$nome);
$nomeNovo = $temp[0] . " " . $temp[count($temp)-1];
return $nomeNovo;
}
?>  