<?php
session_start();
include_once("conexao.php");
include_once("email.php");
$conexao        = new Conexao();
header("Content-Type: text/html;  charset=ISO-8859-1", true);

$nomeAluno      = strtoupper(f_Anti_Injection(trim($_SESSION['vch_nome'])));
$endereco       = f_Anti_Injection($_SESSION['vch_endereco']);
$bairro         = f_Anti_Injection($_SESSION['vch_bairro']);
$cep            = f_Anti_Injection($_SESSION['vch_cep']);
$nomeResponsavel= strtoupper(f_Anti_Injection(($_SESSION['vch_responsavel'])));
$cpf            = $_SESSION['vch_cpf'];
$mae            = strtoupper(f_Anti_Injection(trim($_SESSION['vch_mae'])));
$nascimento     = dateToDatabase($_SESSION['sdt_nascimento']);
$sexo           = f_Anti_Injection($_SESSION['vch_sexo']);
$localidade     = f_Anti_Injection($_SESSION['vch_localidade']);
$cidade         = f_Anti_Injection($_SESSION['vch_cidade']);
$numero         = f_Anti_Injection($_SESSION['vch_numero']);
$telefone       = $_SESSION['vch_telefone'];
$escola         = $_SESSION['escola'];
$serie          = $_SESSION['vch_serie'];

$vch_complemento= strtoupper(f_Anti_Injection($_SESSION['vch_complemento']));
$email          = strtolower($_SESSION['vch_email']);

if (!isset($_SESSION['vch_orgaopublico'])){
  $vch_orgaopublico = null;       
}else{
  $vch_orgaopublico = strtoupper($_SESSION['vch_orgaopublico']);
}
//var_dump($_SESSION);
//faz a busca deste aluno na base de dados do sge para pegar o id dele
$buscaCodigoAluno = "
SELECT ed47_i_codigo FROM escola.aluno  
where sem_acentos(ed47_v_mae ) ILIKE sem_acentos('%$mae%') 
and sem_acentos(ed47_v_nome) ilike sem_acentos('%$nomeAluno%')
and ed47_d_nasc = '$nascimento' limit 1
";

//$buscaCodigoAluno = "SELECT aluno.ed47_i_codigo FROM escola.aluno WHERE aluno.ed47_d_nasc = '$nascimento' AND aluno.ed47_v_nome = '$nomeAluno' AND trim(aluno.ed47_v_mae) ilike '$mae' limit 1 ;";
$resultCodigoAluno = pg_query($conexao->conn(), $buscaCodigoAluno);
$aluno_sge = pg_fetch_assoc($resultCodigoAluno);


    //se exite aluno cadatrado no sge
    if (pg_numrows($resultCodigoAluno) > 0) {
        $ed47_i_codigo = $aluno_sge['ed47_i_codigo'];
    } else {
        $ed47_i_codigo = 'null';
    }

$escola_origem = 'null';
$serie_origem = 'null';

    //Verifica se aluno tem cadastro no sge
    if ($ed47_i_codigo != 'null') {
        //verifica se o aluno já tem alguma matricula
        $buscaEscolaSerieAluno = "select ed18_i_codigo,ed11_i_codigo from escola.matricula 
        join escola.aluno on ed60_i_aluno = ed47_i_codigo
        join escola.turma on ed57_i_codigo = ed60_i_turma
        join escola.escola on ed18_i_codigo = ed57_i_escola 
        join escola.turmaserieregimemat on  ed220_i_turma = ed57_i_codigo
        join escola.serieregimemat on ed223_i_codigo = ed220_i_serieregimemat
        join escola.serie on ed11_i_codigo = ed223_i_serie
        where ed47_i_codigo = $ed47_i_codigo and ed60_c_situacao = 'MATRICULADO' 
        order by ed60_d_datamatricula desc
        limit 1";
        $result = pg_query($conexao->conn(), $buscaEscolaSerieAluno);
        $matricula_sge = pg_fetch_assoc($result);

        if ($result != false) {
           // verifica se é null
           if($matricula_sge['ed18_i_codigo']==null){
              $escola_origem = 'null';
           }else{
              $escola_origem = $matricula_sge['ed18_i_codigo'];
           } 
            
           if($matricula_sge['ed11_i_codigo'] == null){
              $serie_origem = 'null';
           }else{
              $serie_origem = $matricula_sge['ed11_i_codigo'];
           }
           
        }

    }



    pg_query($conexao->conn(),"BEGIN") or die("Could not start transaction\n");

   //faz insert em tabela alunoreserva e retorna o id do alunoreseva inserido para registrar em auditoria
    $insertAlunoReserva = "
      INSERT INTO reserva.alunoreserva (
        ed47_i_codigo, 
        ed47_v_nome, 
        ed47_v_ender, 
        ed47_v_bairro, 
        ed47_v_cep, 
        ed47_c_nomeresp, 
        ed47_v_cpf, 
        ed47_v_mae, 
        ed47_d_nasc, 
        ed47_v_sexo,
        ed47_i_localidade,
        municipio,
        ed47_c_numero,
        ed47_v_telef,
            vch_orgaopublico,
        ed47_v_compl,
        email_resp,
        alunostatusreserva_id
      ) 
      VALUES (
        $ed47_i_codigo, 
        '$nomeAluno',
        '$endereco', 
        '$bairro', 
        '$cep', 
        '$nomeResponsavel', 
        '$cpf', 
        '$mae', 
        '$nascimento', 
        '$sexo',
        '$localidade',
        '$cidade',
        '$numero',
        '$telefone',
            '$vch_orgaopublico',
        '$vch_complemento',
        '$email',
        '1'		
      ) returning id_alunoreserva;
    ";
    //echo ($insertAlunoReserva);
    //echo('<br>');
    //exit;
    $resultInsertAlunoReserva = pg_query($conexao->conn(), $insertAlunoReserva);
    $resultInsertAlunoReserva = pg_fetch_assoc($resultInsertAlunoReserva);
    $idAlunoReserva = $resultInsertAlunoReserva['id_alunoreserva'];
    
    //insert na tabela escolareserva
    $insertEscolaReserva = "
      INSERT INTO reserva.escolareserva(
        id_alunoreserva, 
        ed56_i_escola, 
        ed221_i_serie,
        ed56_i_escola_origem,
        ed221_i_serie_origem                                        
      )
      VALUES(
        $idAlunoReserva, 
        $escola, 
        $serie,
        $escola_origem,
        $serie_origem 
      )
    ";
    //echo $insertEscolaReserva;
    //exit;
    
    $resultInsertEscolaReserva = pg_query($conexao->conn(), $insertEscolaReserva);

    //insert em auditoria
    $insertAuditoriaReserva = "
      INSERT INTO reserva.auditoriareserva(
        adr_v_acao, 
        adr_v_informacao,
        adr_d_data, 
        id_alunoreserva
      )
      VALUES(
        'Cadastro aluno reserva', 
        'cadastro',   
         now(), 
         $idAlunoReserva
      ) returning adr_d_data
    ;";
    $resultInsertAuditoria = pg_query($conexao->conn(), $insertAuditoriaReserva);
    $DataAuditoria = pg_fetch_assoc($resultInsertAuditoria);
    $data_registro = $DataAuditoria['adr_d_data'];
    
    $_SESSION['codigo'] = $idAlunoReserva;

    //verifica se todas a query foi correta
if($resultInsertAuditoria and $resultInsertEscolaReserva and $resultInsertAlunoReserva){
    pg_query($conexao->conn(),"COMMIT") or die("Transaction commit failed\n");
    
    enviarEmailCadastro();
 
    header('Location:comprovante.php');
}else{
    // Retorna a sequencia no caso de ROOLLBACK; 
    $idAlunoReserva = $idAlunoReserva - 1;
    $Sqltxt = ("ROLLBACK; SELECT setval('reserva.alunoreserva_id_alunoreserva_seq',".$idAlunoReserva.");"); 
    pg_query($conexao->conn(),$Sqltxt) or die("Transaction rollback failed\n");
    DIE('Cadastro não realizado, favor voltar a tela inicial para cadastrar ou entrar em contato com Matrícula Atende.');
  
}

function enviarEmailCadastro(){
  
  global $conexao;
  global $idAlunoReserva;
  global $data_registro;
  global $ed47_i_codigo;

  $SqlEscola = "select ed18_c_nome from escola.escola where ed18_i_codigo = {$_SESSION['escola']} ";  
   $result = pg_query($conexao->conn(), $SqlEscola);
   $Escola = pg_fetch_assoc($result);
   
   $SqlSerie = "select ed11_c_descr from escola.serie where ed11_i_codigo = {$_SESSION['vch_serie']} ";  
   $result = pg_query($conexao->conn(), $SqlSerie);
   $Serie = pg_fetch_assoc($result);
   
  
  $mensagem = '
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
  </head>
  <body>
  
  <table style="width: 100%;">
    <thead>
      <th style="background: gray; color:white;height:50px">Portal Lista de Reserva</th>
    </thead>
  </table>
  <br>
  <table border="1" style="width: 100%;">
    <thead>
      <th>Cod. SGE</th>  
      <th>Id</th>
      <th>Aluno</th>
      <th>Escola Destino</th>
      <th>Serie Destino</th>
      <th>Status</th>
      <th>Atividade</th>
    </thead>
    <tbody>
      <td>'.$ed47_i_codigo.'</td>
      <td>'.$idAlunoReserva.'</td>
      <td>'.trim($_SESSION['vch_nome']).'</td>
      <td>'.$Escola['ed18_c_nome'].'</td>
      <td>'.$Serie['ed11_c_descr'].'</td>
      <td>Cadastrado</td>
      <td>Cadastro no portal de lista de reserva</td>
    </tbody>
  </table>
  <br>
  <table style="width: 100%;">
    <thead>
      <th style="background: gray; color:white; height:50px"> Data Registro: '.$data_registro.'</th>
    </thead>
  </table>
  
  </body>
  </html>
  ';
  $mailDestino = ['auditoria.listadeespera@educa.camacari.ba.gov.br']; // Email da seduc
  //$mailDestino = array('jorgeallan@msn.com'); // Email da seduc
  //$mailDestino = 'jorgeallan@msn.com';

  envialEmail($mensagem,$mailDestino,'','');
   

}

function dateToDatabase($date)
{
    $date = explode('/', $date);
    $date_to_database = "$date[0]-$date[1]-$date[2]";
    return $date_to_database;
}
