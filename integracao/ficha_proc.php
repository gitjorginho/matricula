<?php
session_start();
include_once("conexao.php");
include_once("email.php");
$conexao = new Conexao();

$debug = false;

header("Content-Type: text/html;  charset=ISO-8859-1", true);

//******************************************************************************
//                  Inicializando as variáveis  
//******************************************************************************

$nomeAluno          = strtoupper(trim($_SESSION['vch_nome']));
$endereco           = $_SESSION['vch_endereco'];
$bairro             = $_SESSION['vch_bairro'];
$cep                = $_SESSION['vch_cep'];
$nomeResponsavel    = strtoupper($_SESSION['vch_responsavel']);
$cpf                = $_SESSION['vch_cpf'];
$mae                = strtoupper(trim($_SESSION['vch_mae']));
$nascimento         = dateToDatabase($_SESSION['sdt_nascimento']);
$sexo               = $_SESSION['vch_sexo'];
$localidade         = $_SESSION['vch_localidade'];
$cidade             = $_SESSION['vch_cidade'];
$numero             = $_SESSION['vch_numero'];
$telefone           = $_SESSION['vch_telefone'];
$escola             = $_SESSION['escola'];
$serie              = $_SESSION['vch_serie'];
$vch_complemento    = strtoupper($_SESSION['vch_complemento']);
$email              = strtolower($_SESSION['vch_email']);

// Início do Dados de integração com o SGE 

$nacionalidade  = $_SESSION['vch_nacionalidade'];
$pais           = $_SESSION['vch_pais'];
$uf             = $_SESSION['vch_uf'];
$naturalidade   = $_SESSION['vch_naturalidade'];



// Fim do Dados de integração com o SGE 

if (!isset($_SESSION['vch_orgaopublico'])) {
    $vch_orgaopublico = null;
} else {
    $vch_orgaopublico = strtoupper($_SESSION['vch_orgaopublico']);
}

//******************************************************************************
//                  Verifica se o aluno está cadastradp no SGE
//                  Tabela [escola.aluno]  
//******************************************************************************

$buscaCodigoAluno = "
    SELECT 
        aluno.ed47_i_codigo 
    FROM 
        escola.aluno 
    WHERE 
    aluno.ed47_d_nasc = '$nascimento' and "
    . " sem_acentos(aluno.ed47_v_nome) = sem_acentos('$nomeAluno') and "
    . " sem_acentos(trim(aluno.ed47_v_mae)) ilike sem_acentos('$mae') limit 1;";

$resultCodigoAluno = pg_query($conexao->conn(), $buscaCodigoAluno);

$aluno_sge = pg_fetch_assoc($resultCodigoAluno);

//******************************************************************************
//                  Recebe o número do SGE, caso exista.     
//******************************************************************************

if ($aluno_sge != false) {
    $ed47_i_codigo = $aluno_sge['ed47_i_codigo'];
} else {
    $ed47_i_codigo = 'null';
}

$escola_origem = 'null';
$serie_origem = 'null';

//******************************************************************************
//                  Verifica se o aluno já tem matrícula.     
//******************************************************************************
if ($ed47_i_codigo != 'null') {

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
        if ($matricula_sge['ed18_i_codigo'] == null) {
            $escola_origem = 'null';
        } else {
            //Carrega a escola
            $escola_origem = $matricula_sge['ed18_i_codigo'];
        }

        if ($matricula_sge['ed11_i_codigo'] == null) {
            $serie_origem = 'null';
        } else {
            //Carrega a série
            $serie_origem = $matricula_sge['ed11_i_codigo'];
        }
    }
}

pg_query($conexao->conn(), "BEGIN") or die("Could not start transaction\n");

//******************************************************************************
//                  Insere o aluno na Reserva com o status "CADASTRADO"     
//******************************************************************************

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
$resultInsertAlunoReserva = pg_query($conexao->conn(), $insertAlunoReserva);
$resultInsertAlunoReserva = pg_fetch_assoc($resultInsertAlunoReserva);
$idAlunoReserva = $resultInsertAlunoReserva['id_alunoreserva'];

//echo ($insertAlunoReserva);
//echo('<br>');
//exit;

//******************************************************************************
//                  Insere a unidade escolar escolhida pelo aluno     
//******************************************************************************

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

$resultInsertEscolaReserva = pg_query($conexao->conn(), $insertEscolaReserva);
//echo $insertEscolaReserva;
//echo('<br>');
//exit;


//******************************************************************************
//                  Insere o responsável e o momento do cadastramento     
//******************************************************************************

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


$_SESSION['codigo'] = $idAlunoReserva;                                          // Grava na Sessão do Sistema para impressão do Comprovante

//******************************************************************************
//                     Integração do Portal com SGE
//    Cadastra o Aluno no Schena de "ESCOLA" (SGE), quando não localizado!     
//******************************************************************************

if ($aluno_sge == false) { // Aluno não existe no SGE
  
//******************************************************************************
//                     Trata os dados 
//******************************************************************************
    if ($telefone != '') {
       $telefone = substr($telefone, 0, 4) . "" . substr($telefone, 5, 15);     // Trata o Telefone - Tabela reserva.alunoreserva Varchar 20 e escola.aluno Varchar 15
    }
    if (($uf == '')||($uf == null)) {
        $uf = 0;
    }
    if (($naturalidade == '')||($naturalidade == null)) {
        $naturalidade = 0;
    }
    $codCensoMunEnd       = buscaCodCenso($cidade,'BA','municipio');            // Consulta o código do Censo passando o município. 
    $codCensoEstadoEnd    = buscaCodCenso($cidade,'BA','estado');               // Consulta o código do Censo passando o Estado.
    $cpf                  = removeCaracter($cpf); 

 
    //**************************************************************************
    //                     Cadastra o aluno no SGE
    //                     Tabela [escola.aluno]
    //**************************************************************************
    $insertAlunoEscola = "
      INSERT INTO escola.aluno(
        ed47_v_nome, 
        ed47_v_ender, 
        ed47_v_compl, 
        ed47_v_bairro, 
        ed47_v_cep, 
        ed47_c_nomeresp,
        ed47_c_emailresp,
        ed47_v_cpf, 
        ed47_v_mae, 
        ed47_d_nasc, 
        ed47_v_sexo,
        ed47_v_telcel, 
        ed47_c_numero, 
        ed47_i_localidade,
        ed47_i_pais,
        ed47_i_filiacao,
        ed47_d_cadast,
        ed47_v_telef,
        ed47_i_login,
        ed47_c_zona,
        ed47_i_nacion,
        ed47_i_censoufnat,
        ed47_i_censomunicnat,
        ed47_i_censoufend,
        ed47_i_censomunicend
      ) 
      VALUES (
        '$nomeAluno',
        '$endereco', 
        '$vch_complemento',
        '$bairro', 
        '$cep', 
        '$nomeResponsavel', 
        '$email',
        '$cpf', 
        '$mae', 
        '$nascimento', 
        '$sexo',
        '".$telefone."',    
        '$numero', 
        '$localidade',
        10,
        1,
        now(),
        '',
        0,
        'URBANA',
        '$nacionalidade',
        '$uf',
        '$naturalidade',
        '$codCensoEstadoEnd',
        '$codCensoMunEnd'
      ) returning ed47_i_codigo;
    ";
    $resultInsertAlunoEscola = pg_query($conexao->conn(), $insertAlunoEscola);
    $resultInsertAlunoEscola = pg_fetch_assoc($resultInsertAlunoEscola);
    $NumeroAlunoSGE = $resultInsertAlunoEscola['ed47_i_codigo'];
    if ($debug == true){
        echo ("Insere o aluno : {$insertAlunoEscola}");
        echo('<br>');
        echo ("Numero no SGE: {$NumeroAlunoSGE}");
        echo('<br>');
    }   
    //**************************************************************************
    //                     Retorna o número do SGE - 
    //                     garante a integração de Portal com o SGE. 
    //**************************************************************************
    $updateAlunoReserva = "UPDATE reserva.alunoreserva 
			SET ed47_i_codigo = " . $NumeroAlunoSGE . " 
			WHERE alunoreserva.id_alunoreserva = " . $idAlunoReserva;
    $resulttAlunoReserva = pg_query($conexao->conn(), $updateAlunoReserva);
    if ($debug == true){
        echo ("Integração Portal com SGE: {$updateAlunoReserva}");
        echo('<br>');
    }    
    
    //**************************************************************************
    //                     Garante o relacionamento entre Aluno x Bairro 
    //                     Tabela Associativa [AlunoBairro]. 
    //**************************************************************************

    $sqlConsulta = "SELECT j13_codi,j13_descr FROM cadastro.bairro where j13_descr like '" . $bairro . "' limit 1";
    $rsConsulta = pg_query($conexao->conn(), $sqlConsulta);
    $linharsConsulta = pg_fetch_assoc($rsConsulta);
    if ($debug == true){
        echo ("Consulta o Id do Bairro: {$sqlConsulta}");
        echo('<br>');
    }    
    If ($linharsConsulta) {
        $CodigoBairro = $linharsConsulta['j13_codi'];
        $DescricaoBairro = $linharsConsulta['j13_descr'];

        $sqlInsertBairro = "
                INSERT INTO escola.alunobairro(
                    ed225_i_codigo,
                    ed225_i_aluno, 
                    ed225_i_bairro
                )
                VALUES(
                    nextval('escola.alunobairro_ed225_i_codigo_seq') , 
                    " . $NumeroAlunoSGE . ",
                    " . $CodigoBairro . ")";

        $resultInsertBairro = pg_query($conexao->conn(), $sqlInsertBairro);
        if ($debug == true){
            echo ("Cria o Relacionamento ID Bairro X ID Aluno: {$sqlInsertBairro}");
            echo('<br>');
        }    
    }

    //**************************************************************************
    //                     Garante o relacionamento entre Aluno x RuaBairroCEP 
    //                     Tabela Associativa [AlunoRuaBairroCEP]. 
    //**************************************************************************
    $ruaBairroCEP = buscaRuaBairroCEP($endereco,$cep,$bairro);                  // Consulta o ID do CEP

    If ($ruaBairroCEP != 0 ) {

        $sqlInsertRuaBairroCEP  = "
                INSERT INTO escola.alunoruasbairrocep(
                    j76_i_codigo,
                    j76_i_aluno, 
                    j76_i_ruasbairrocep,
                    j76_d_criadoem
                )
                VALUES(
                     nextval('escola.alunoruasbairrocep_j76_i_codigo_seq'), 
                    " . $NumeroAlunoSGE . ",
                    " . $ruaBairroCEP . ",                        
                    now())";

        $resultRuaBairroCEP = pg_query($conexao->conn(), $sqlInsertRuaBairroCEP);
        if ($debug == true){
            echo ("Cria o Relacionamento ID Aluno X ID CEP: {$sqlInsertRuaBairroCEP}");
            echo('<br>');   
        }    
    }
    
    //**************************************************************************
    //                     Cadastra o nome da mãe na tabela de cidadão 
    //                     Tabela [cidadao]. 
    //                     Falta tratar alguns atributos [X]
    //**************************************************************************

    $insertCidadao = "
      INSERT INTO ouvidoria.cidadao(
        ov02_sequencial,
        ov02_seq, 
        ov02_nome, 
        ov02_ident, 
        ov02_cnpjcpf, 
        ov02_endereco, 
        ov02_numero, 
        ov02_compl,
        ov02_bairro,
        ov02_munic, 
        ov02_uf, 
        ov02_cep, 
        ov02_situacaocidadao, 
        ov02_ativo, 
        ov02_data,
        ov02_datanascimento, 
        ov02_sexo 
       ) 
      VALUES (
        nextval('ouvidoria.cidadao_ov02_sequencial_seq'),
        1, 
        '$mae',
        '', 
        '',
        '', 
        '',
        '',
        '',
        '',
        '',
        '',
        2,
        true,
        now(),
        null,
        ''
      ) returning ov02_sequencial;
    ";
    $resultCidadao = pg_query($conexao->conn(), $insertCidadao);
    $resultCidadao = pg_fetch_assoc($resultCidadao);
    $numeroCidadaoSGE = $resultCidadao['ov02_sequencial'];
    if ($debug == true){
        echo ("Cadastra o nome da mãe: {$insertCidadao}");
        echo('<br>');
        echo ("Numero da mãe: {$numeroCidadaoSGE}");
        echo('<br>');
    }   
    
    //**************************************************************************
    //                     Garante o relacionamento de Aluno x Cidadao 
    //                     Tabela [AlunoCidadao]. 
    //**************************************************************************

    $sqlInsertAlunoCidadao = "
                INSERT INTO escola.alunocidadao(
                    ed330_sequencial,
                    ed330_cidadao, 
                    ed330_cidadao_seq, 
                    ed330_aluno
                )
                VALUES(
                     nextval('escola.alunocidadao_ed330_sequencial_seq') , 
                    " . $numeroCidadaoSGE . ",
                     1,   
                    " . $NumeroAlunoSGE . ")";

    $resultInsertAlunoCidadao = pg_query($conexao->conn(), $sqlInsertAlunoCidadao);
    if ($debug == true){
        echo ("Cadastra o relacionamento AlunoCidadao: {$sqlInsertAlunoCidadao}");
        echo('<br>');
    }
    //**************************************************************************
    //                     Cadastra o Responsável pelo Aluno
    //                     Tabela [ouvidoria.cidadao]. 
    //**************************************************************************
    //@@ Avaliar a possibilidade de incluir o telefone 
    $insertCidadaoContato = "
      INSERT INTO ouvidoria.cidadao(
        ov02_sequencial,
        ov02_seq, 
        ov02_nome, 
        ov02_ident, 
        ov02_cnpjcpf, 
        ov02_endereco, 
        ov02_numero, 
        ov02_compl,
        ov02_bairro,
        ov02_munic, 
        ov02_uf, 
        ov02_cep, 
        ov02_situacaocidadao, 
        ov02_ativo, 
        ov02_data,
        ov02_datanascimento, 
        ov02_sexo 
       ) 
      VALUES (
        nextval('ouvidoria.cidadao_ov02_sequencial_seq'),
        1, 
        '$nomeResponsavel',
        '', 
        '',
        '', 
        '',
        '',
        '',
        '',
        '',
        '',
        2,
        true,
        now(),
        null,
        ''
      ) returning ov02_sequencial;
    ";
    $resultCidadaoContato = pg_query($conexao->conn(), $insertCidadaoContato);
    $resultCidadaoContato = pg_fetch_assoc($resultCidadaoContato);
    $numeroCidadaoContatoSGE = $resultCidadaoContato['ov02_sequencial'];
    if ($debug == true){
        echo ("Cadastra o Responsável: {$insertCidadaoContato}");
        echo('<br>');
        echo ("Numero do Responsável: {$numeroCidadaoContatoSGE}");
        echo('<br>');
    }
    //**************************************************************************
    //                     Garante o relacionamento Aluno x Responsável  
    //                     Tabela Associativa [AlunoCidadaoContato]. 
    //**************************************************************************
    //@@@ Corrigir a sub sequencia... retirar a informação estática. 
    $sqlInsertAlunoCidadaoContato = "
        INSERT INTO escola.alunocidadaocontato(
            ed332_sequencial,
            ed332_aluno, 
            ed332_cidadao, 
            ed332_cidadao_seq
        )
        VALUES(
             nextval('escola.alunocidadaocontato_ed332_sequencial_seq'),"
            . $NumeroAlunoSGE . ","
            . $numeroCidadaoContatoSGE . ",
             1)";

    $resultInsertAlunoCidadaoContato = pg_query($conexao->conn(), $sqlInsertAlunoCidadaoContato);
    if ($debug == true){
        echo ("Cria o Relacionamento ID Aluno X ID Responsável: {$sqlInsertAlunoCidadaoContato}");
        echo('<br>');
    }
    //**************************************************************************
    //                     Garante o relacionamento Aluno x Telefone  
    //                     Tabela Associativa [AlunoTelefone]. 
    //                     Inclui o tipo [1] de celular cadastrado no Portal
    //**************************************************************************

    $sqlInsertAlunotelefone = "
        INSERT INTO escola.telefonealuno(
            ed50_i_codigo,
            ed50_i_aluno, 
            ed50_i_tipotelefone, 
            ed50_i_numero,
            ed50_i_ramal, 
            ed50_t_obs 
        )
        VALUES(
             nextval('escola.telefonealuno_ed50_i_codigo_seq'),"
            . $NumeroAlunoSGE . ",
             1, 
             0,
             null,
             '$telefone' 
             )";

    $resultInsertAlunotelefone = pg_query($conexao->conn(), $sqlInsertAlunotelefone);
    if ($debug == true){
        echo ("Cadastra o telefone do Aluno: {$sqlInsertAlunotelefone}");
        echo('<br>');
        exit;
    }
    
}
// verifica se houve erro na execução as querys
if ($resultInsertAuditoria and
        $resultInsertEscolaReserva and
        $resultInsertAlunoReserva and
        $resultInsertAlunoEscola and
        $resulttAlunoReserva) {                                                 

    pg_query($conexao->conn(), "COMMIT") or die("Transaction commit failed\n");
    //enviarEmailCadastro();
    header('Location:comprovante.php');
} else {
    // Retorna a sequencia no caso de ROOLLBACK; 
    $idAlunoReserva = $idAlunoReserva - 1;
    $Sqltxt = ("ROLLBACK; SELECT setval('reserva.alunoreserva_id_alunoreserva_seq'," . $idAlunoReserva . ");");
    pg_query($conexao->conn(), $Sqltxt) or die("Transaction rollback failed\n");
    DIE('Cadastro não realizado, favor voltar a tela inicial para cadastrar ou entrar em contato com Matrícula Atende.');
}

function enviarEmailCadastro() {

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
      <td>' . $ed47_i_codigo . '</td>
      <td>' . $idAlunoReserva . '</td>
      <td>' . trim($_SESSION['vch_nome']) . '</td>
      <td>' . $Escola['ed18_c_nome'] . '</td>
      <td>' . $Serie['ed11_c_descr'] . '</td>
      <td>Cadastrado</td>
      <td>Cadastro no portal de lista de reserva</td>
    </tbody>
  </table>
  <br>
  <table style="width: 100%;">
    <thead>
      <th style="background: gray; color:white; height:50px"> Data Registro: ' . $data_registro . '</th>
    </thead>
  </table>
  
  </body>
  </html>
  ';

   // envialEmail($mensagem);
}

function dateToDatabase($date) {
    $date = explode('/', $date);
    $date_to_database = "$date[0]-$date[1]-$date[2]";
    return $date_to_database;
}

function removeCaracter($texto) {
 $texto = trim($texto);
 $texto = str_replace(".", "", $texto);
 $texto = str_replace(",", "", $texto);
 $texto = str_replace("-", "", $texto);
 $texto = str_replace("/", "", $texto);
 return $texto;
}

function buscaRuaBairroCEP($logradouro,$cep, $bairro){
    $conexao2 = new Conexao();

    $sqlConsulta = "select j32_i_codigo from "
            . "cadastro.ruasbairrocep RBC, cadastro.ruasbairro RB, "
            . "cadastro.bairro B, cadastro.ruas R, cadastro.ruascep RC "
            . "where "
            . "RBC.j32_ruasbairro = RB.j16_codigo and "
            . "RB.j16_bairro = B.j13_codi and "
            . "RB.j16_lograd = R.j14_codigo and "
            . "RBC.j32_ruascep = RC.j29_codigo and "
            . "R.j14_nome like '".$logradouro."' and "
            . "RC.j29_cep = '".$cep."' and "
            . "B.j13_descr like '".$bairro."'";
    //echo $sqlConsulta;
    $rsConsulta = pg_query($conexao2->conn(), $sqlConsulta);
    $linharsConsulta = pg_fetch_assoc($rsConsulta);
    
    if ($linharsConsulta) {
        $ruaBairroCeo = $linharsConsulta['j32_i_codigo'];
    } else {
        $ruaBairroCeo =0; 
    }
    return $ruaBairroCeo;
}
function buscaCodCenso($municipio,$estado,$tipoCodigo) {
    $conexao2 = new Conexao();
    if ($tipoCodigo == 'municipio'){
        $StringConcatena = 'ed261_i_codigo';
    } else{
        $StringConcatena = 'ed261_i_censouf';
    }
    $sqlConsulta = "select ".$StringConcatena." "
            . "FROM escola.censomunic CM, escola.censouf CUF " 
            . "where "
            . "CM.ed261_i_censouf = CUF.ed260_i_codigo and "
            . "ed261_c_nome like '".$municipio."%' and "
            . "CUF.ed260_c_sigla like '".$estado."'";
    $rsConsulta = pg_query($conexao2->conn(), $sqlConsulta);
    $linharsConsulta = pg_fetch_assoc($rsConsulta);
    //echo $sqlConsulta;
    //var_dump($linharsConsulta);
    
    if ($linharsConsulta) {
         if ($tipoCodigo == 'municipio'){
            $codCenso = $linharsConsulta['ed261_i_codigo'];
         }else {
            $codCenso = $linharsConsulta['ed261_i_censouf'];
         }   
    } else {
        $codCenso =0; 
    }
    return $codCenso;
}