<?php
session_start();
require_once('upload_doc_aluno.php');

header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once ('conexao.php');
require_once('email_sge.php');

$conexao = new Conexao();
$conn = $conexao->conn();

$codigo_aluno_sge = $_POST['vch_codigo_sge'];
//$codigo_aluno       =$_POST['vch_codigo'];
$nome_aluno         =strtoupper($_POST['vch_nome']);
//$sexo_aluno         =$_POST['vch_sexo'];
$data_nascimento    = dateToDatabase($_POST['sdt_nascimento']);
$nome_mae           =$_POST['vch_mae'];
//$nome_responsavel   =$_POST['vch_responsavel'];
//$email_responsavel  =$_POST['vch_email_responsavel'];
//$cpf_responsavel    =$_POST['vch_cpf'];
//$endereco           =$_POST['vch_endereco'];
//$complemento        =$_POST['vch_complemento'];
//$orgaopublico 		=$_POST['vch_orgaopublico'];
//$bairro             =$_POST['vch_bairro'];
//$localidade         =$_POST['vch_localidade'];
//$telefone           =$_POST['vch_telefone'];
//$cep                =$_POST['vch_cep'];
//$cidade             =$_POST['vch_cidade'];
//$serie              =$_POST['vch_serie'];
//$escola             =$_POST['escola'];
//$numero             =$_POST['vch_numero'];
//$acoes              =$_POST['vch_acoes'];

//$_SESSION['vch_serie'] = $serie;
//$telefone = str_replace(['(',')','-'],'',$telefone);
//$telefone = trim($telefone);

		
		//salva alteracao aluno
		/*$sql_update_aluno ="
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
		$result = pg_query($conn,$sql_update_aluno);*/
		 
		
		//salva a alteracao da escola
		/*$sql_update_escola = "update reserva.escolareserva set ed56_i_escola = $escola, ed221_i_serie = $serie where id_alunoreserva = $codigo_aluno";
        $result = pg_query($conn,$sql_update_escola);

		//salva auditoria  
		$sql_auditoria = "INSERT INTO reserva.auditoriausuarioaluno
		(nome_usuario, id_alunoreserva, descricao, data_modificacao, acoes)
		VALUES('PROPRIO ALUNO',$codigo_aluno, 'atulizar dados pelo proprio aluno', now(), '$acoes')";
		$result = pg_query($conn,$sql_auditoria);*/
		//exit(var_dump($_FILES));
	
 
		
		$arrDadosDoc = uploadImagemDocAluno($_FILES,$codigo_aluno_sge);

			$bResultInsertDocumento  = true; 
			foreach($arrDadosDoc as $documento ){
			  //exit(var_dump($documento));
			  $seFrenteVerso = explode('-',$documento['nome_documento']);
			  if ($seFrenteVerso[2] == 'FRENTE' ){
				 $ctipoDocumento = 'F';
			  }else if ($seFrenteVerso[2] == 'VERSO'){
				 $ctipoDocumento = 'V';
			  }else{
				 $ctipoDocumento = 'U';
			  }
			   
			  $enderecoServidor = 'https://listadeesperaseduc.camacari.ba.gov.br/';
		
			  $sqlInsertDocumentos = "INSERT INTO reserva.documentoalunoreservasge
			  (ed47_i_codigo, id_documentoreserva, nome_documento, caminho_documento,tipo_documento)
			  VALUES($codigo_aluno_sge, {$documento['id_documentoreserva']} , '{$documento['nome_documento']}', '$enderecoServidor{$documento['caminho_documento']}', '$ctipoDocumento');
			  "; 
			  
				$resultInsertDocumento = pg_query($conn,$sqlInsertDocumentos);
			  
			  //se alguma insercao deu errado marca a variaVEL FALSE  
			  if($resultInsertDocumento == false){
				  $bResultInsertDocumento = false;
			  }
			 
		  }

			//$ano_anterior = date("Y",strtotime(date("Y-m-d")."- 1 year"));

			$sqlJaExisteRematricula = "select true as existeRematricula
				                         from escola.confirmacaorematricula
				                        where ed60_i_aluno = {$codigo_aluno_sge}
				                        limit 1;";

			$result = pg_query($conn,$sql_turma_anterior_aluno);

			if (pg_num_rows($result) >= 0)
			{
				$existeRematricula = pg_fetch_assoc($result);
			}
			else
			{
				$existeRematricula['existeRematricula'] = false;
			}
			

			if ($existeRematricula['existeRematricula'] == false)
			{
				/*$sql_turma_anterior_aluno = "
				select ed57_i_escola,ed57_i_calendario,ed57_i_codigo from matricula 
				join turma on ed57_i_codigo = ed60_i_turma
				join calendario on ed57_i_calendario = ed52_i_codigo
				where ed60_i_aluno = {$codigo_aluno_sge} and ed52_i_ano = $ano_anterior and ed60_c_situacao in ('MATRICULADO','APROVADO')
				order by ed60_d_datamatricula desc
				limit 1";*/

				$sql_turma_anterior_aluno = "select ed57_i_escola,
				                                    ed57_i_calendario,
				                                    ed57_i_codigo 
				                               from matricula 
											   join turma on ed57_i_codigo = ed60_i_turma
											   join calendario on ed57_i_calendario = ed52_i_codigo
											  where ed60_i_aluno = {$codigo_aluno_sge} 
											    and ed52_i_ano = 2020 
											    and ed60_c_situacao in ('MATRICULADO')
											    and ed60_c_concluida = 'N'
											    and ed60_c_ativa = 'S'
											  order by ed60_d_datamatricula desc
											  limit 1;";
				
				$result = pg_query($conn,$sql_turma_anterior_aluno);
	            $arrDadosTurmaAnterior = pg_fetch_assoc($result);
			
				if (pg_num_rows($result) >= 0){

					$sqlInsertConfirmacaoRematricula = "
					INSERT INTO escola.confirmacaorematricula
					(edu01_escola, edu01_calendario, edu01_turma, edu01_aluno, edu01_criado_em)
					VALUES({$arrDadosTurmaAnterior['ed57_i_escola']}, {$arrDadosTurmaAnterior['ed57_i_calendario']}, {$arrDadosTurmaAnterior['ed57_i_codigo']}, {$codigo_aluno_sge}, now());
					";
				
					$resultInsertConfirmacaoRematricula = pg_query($conn,$sqlInsertConfirmacaoRematricula);

					//Inserção na nova tabela de auditoria do sistema de reserva para atender aos alunos que não possuem código no sistema de reserva.
					if (pg_affected_rows($resultInsertConfirmacaoRematricula))
					{
						$sqlInsertAuditoriaRematriculaReserva = "INSERT INTO reserva.auditoriareservasge
																(ed47_i_codigo, adr_v_acao, adr_v_informacao)
																VALUES({$codigo_aluno_sge}, 'REMATRICULA 2021 REALIZADA', '');";

						$result = pg_query($conn,$sqlInsertAuditoriaRematriculaReserva);

						if (pg_affected_rows($result))
						{
							//enviar email com a confirmação da rematrícula
							$mensagem = 'Rematrícula do aluno (código) ' . $codigo_aluno_sge . ' realizada com sucesso.';
							envialEmail($mensagem,'gustavo.araujo@jcl-tecnologia.com.br','','');
						}
						else
						{
							//enviar e-mail para mim
						}
					}

				}else{
					header('Location:rematricula_update_sge.php?not_matricula=');
					die(); 	
				}

		        // chama a pagina de comprovante
		        $_SESSION['vch_nome'] = trim($nome_aluno);
		        $_SESSION['escola'] = $escola;
				header('Location:comprovante_sge.php');
			}
			else
			{
				$_SESSION['codigo_sge'] = $codigo_aluno_sge;
	            $_SESSION['rematricula'] = true;
	            header('Location:index.php');
			}	
            
			

function dateToDatabase($date)
{
    $date = explode('/', $date);
    $date_to_database = "$date[0]-$date[1]-$date[2]";
    return $date_to_database;
}
