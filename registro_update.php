<?php
session_start();
require_once('upload_doc_aluno.php');

header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once ('conexao.php');

$conexao = new Conexao();
$conn = $conexao->conn();


$codigo_aluno       =$_POST['vch_codigo'];
$nome_aluno         =strtoupper($_POST['vch_nome']);
$sexo_aluno         =$_POST['vch_sexo'];
$data_nascimento    = dateToDatabase($_POST['sdt_nascimento']);
$nome_mae           =$_POST['vch_mae'];
$nome_responsavel   =$_POST['vch_responsavel'];
$email_responsavel  =$_POST['vch_email_responsavel'];
$cpf_responsavel    =$_POST['vch_cpf'];
$endereco           =$_POST['vch_endereco'];
$complemento        =$_POST['vch_complemento'];
$orgaopublico 		=$_POST['vch_orgaopublico'];
$bairro             =$_POST['vch_bairro'];
$localidade         =$_POST['vch_localidade'];
$telefone           =$_POST['vch_telefone'];
$cep                =$_POST['vch_cep'];
$cidade             =$_POST['vch_cidade'];
$serie              =$_POST['vch_serie'];
$escola             =$_POST['escola'];
$numero             =$_POST['vch_numero'];
$acoes              =$_POST['vch_acoes'];

$_SESSION['vch_serie'] = $serie;
$telefone = str_replace(['(',')','-'],'',$telefone);
$telefone = trim($telefone);

//		$sql_insert_monitoramentomatriculareserva = "insert into monitoramentomatriculareserva
//		(mmr_acao, mmr_campos, mmr_dataregistro, mmr_idaluno) values ('Atualizou',
//		'Nome: $nome_aluno - Endereco: $endereco - Bairro: $bairro -
//		Cep: $cep - Nome Responsavel: $nome_responsavel - Nome da Mae: $nome_mae - Sexo: $sexo_aluno -
//		Nascimento: $data_nascimento - Localidade: $codigo_localidade - Telefone: $telefone -
//		Numero: $numero - Cpf responsavel: $cpf_responsavel','now', $codigo_aluno);";
//		$monitoramentoreserva = pg_query($conn, $sql_insert_monitoramentomatriculareserva);
		//ed47_i_localidade=$codigo_localidade
		
		//salva alteracao aluno
		$sql_update_aluno ="
		UPDATE reserva.alunoreserva SET 
		ed47_c_numero ='$numero',
		ed47_v_nome='$nome_aluno', 
		ed47_v_telef='$telefone' , 
		ed47_v_ender='$endereco',
		ed47_v_compl='$complemento',
		ed47_v_bairro='$bairro',
		ed47_v_cep='$cep',
		ed47_c_nomeresp='$nome_responsavel',
		ed47_v_mae='$nome_mae',
		ed47_v_sexo='$sexo_aluno',
		ed47_d_nasc='$data_nascimento',
		municipio='$cidade', 
		email_resp='$email_responsavel',
		ed47_v_cpf='$cpf_responsavel',
		ed47_i_localidade='$localidade', 
		vch_orgaopublico='$orgaopublico' 
		WHERE id_alunoreserva = $codigo_aluno ";
		$result = pg_query($conn,$sql_update_aluno);
		 
		
		//salva a alteracao da escola
		$sql_update_escola = "update reserva.escolareserva set ed56_i_escola = $escola, ed221_i_serie = $serie where id_alunoreserva = $codigo_aluno";
        $result = pg_query($conn,$sql_update_escola);

		//salva auditoria  
		$sql_auditoria = "INSERT INTO reserva.auditoriausuarioaluno
		(nome_usuario, id_alunoreserva, descricao, data_modificacao, acoes)
		VALUES('PROPRIO ALUNO',$codigo_aluno, 'atulizar dados pelo proprio aluno', now(), '$acoes')";
		$result = pg_query($conn,$sql_auditoria);


		 //salvar o registro de documentos cadastrado e faz o upload das imagens
		 	 
		 $arrDadosDoc = uploadImagemDocAluno($_FILES);

		  $bResultInsertDocumento  = true; 
		  foreach($arrDadosDoc as $documento ){
			
			$seFrenteVerso = explode('-',$documento['nome_documento']);
			if ($seFrenteVerso[2] == 'FRENTE' ){
			   $ctipoDocumento = 'F';
			}else if ($seFrenteVerso[2] == 'VERSO'){
			   $ctipoDocumento = 'V';
			}else{
			   $ctipoDocumento = 'U';
			}
			 
			$enderecoServidor = 'https://listadeesperaseduc.camacari.ba.gov.br/';
	  
			$sqlInsertDocumentos = "INSERT INTO reserva.documentoalunoreserva
			(id_alunoreserva, id_documentoreserva, nome_documento, caminho_documento,tipo_documento)
			VALUES($codigo_aluno, {$documento['id_documentoreserva']} , '{$documento['nome_documento']}', '$enderecoServidor{$documento['caminho_documento']}', '$ctipoDocumento');
			"; 
			//die($sqlInsertDocumentos)  ;
			  $resultInsertDocumento = pg_query($conn,$sqlInsertDocumentos);
			
			//se alguma insercao deu errado marca a variaVEL FALSE  
			if($resultInsertDocumento == false){
				$bResultInsertDocumento = false;
			}
		   
		}





            



        // chama a pagina de comprovante
                $_SESSION['vch_nome'] = trim($nome_aluno);
				$_SESSION['escola'] = $escola;
				header('Location:comprovante.php');


//		$sql_if_reserva = "select * from matriculareserva where reserva_aluno = {$codigo_aluno}";
//		$result = pg_query($conn,$sql_if_reserva);
//		//die ($sql_update_aluno);
//		if(pg_num_rows($result) == 0){
//		$sql_reserva="
//		INSERT INTO matriculareserva (reserva_cpfresponsavel,reserva_aluno,reserva_turma,reserva_data)
//		VALUES ('$cpf_responsavel',$codigo_aluno,'$serie',now());
//		";
//			if(pg_query($conn,$sql_reserva)){
//				$_SESSION['vch_nome'] = trim($nome_aluno);
//				//$_SESSION['turma'] = $turma;
//				//$_SESSION['escola'] = $escola;
//				header('Location:comprovante.php');
//				}else{
//					echo "Não foi possível concluir a operação";
//					 }
//			}else{
//				$sql_reserva="
//				UPDATE matriculareserva
//				SET reserva_turma = '$serie', reserva_cpfresponsavel='$cpf_responsavel'
//				WHERE reserva_aluno= $codigo_aluno;
//				";
//				//die ("Aluno: ".$sql_update_aluno."  Reserva: ".$sql_reserva);
//		if(pg_query($conn,$sql_reserva)){
//				$_SESSION['vch_nome'] = trim($nome_aluno);
//				//$_SESSION['turma'] = $turma;
//				//$_SESSION['escola'] = $escola;
//				header('Location:comprovante.php');
//			}else{
//				echo "Não foi possível concluir a operação";
//				}
//				}

function dateToDatabase($date)
{
    $date = explode('/', $date);
    $date_to_database = "$date[0]-$date[1]-$date[2]";
    return $date_to_database;
}
