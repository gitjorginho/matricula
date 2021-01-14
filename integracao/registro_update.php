

<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once ('conexao.php');

$conexao = new Conexao();
$conn = $conexao->conn();
//var_dump($_POST);
$codigo_aluno       =$_POST['vch_codigo'];
$nome_aluno         =strtoupper($_POST['vch_nome']);
$sexo_aluno         =$_POST['vch_sexo'];
$data_nascimento    = dateToDatabase($_POST['sdt_nascimento']);
$nome_mae           =$_POST['vch_mae'];
$nome_responsavel   =$_POST['vch_responsavel'];
$cpf_responsavel    =$_POST['vch_cpf'];
$endereco           =$_POST['vch_endereco'];
$bairro             =$_POST['vch_bairro'];
$localidade         =$_POST['vch_localidade'];
$telefone           =$_POST['vch_telefone'];
$cep                =$_POST['vch_cep'];
$cidade             =$_POST['vch_cidade'];
$serie              =$_POST['vch_serie'];
$escola             =$_POST['escola'];
$codigo_localidade  =$_POST['vch_localidade'];
$cpf_responsavel    = str_replace(['.','-'],'',$cpf_responsavel);
$numero             =$_POST['vch_numero'];
$_SESSION['vch_serie'] = $serie;
$cpf_responsavel = str_replace(['.','-'],'',$cpf_responsavel);
$telefone = str_replace(['(',')','-'],'',$telefone);
$telefone = trim($telefone);

//		$sql_insert_monitoramentomatriculareserva = "insert into monitoramentomatriculareserva
//		(mmr_acao, mmr_campos, mmr_dataregistro, mmr_idaluno) values ('Atualizou',
//		'Nome: $nome_aluno - Endereco: $endereco - Bairro: $bairro -
//		Cep: $cep - Nome Responsavel: $nome_responsavel - Nome da Mae: $nome_mae - Sexo: $sexo_aluno -
//		Nascimento: $data_nascimento - Localidade: $codigo_localidade - Telefone: $telefone -
//		Numero: $numero - Cpf responsavel: $cpf_responsavel','now', $codigo_aluno);";
//		$monitoramentoreserva = pg_query($conn, $sql_insert_monitoramentomatriculareserva);
		
		$sql_update_aluno ="
		UPDATE reserva.alunoreserva SET ed47_c_numero ='$numero',ed47_v_nome='$nome_aluno', ed47_v_telef='$telefone' , ed47_v_ender='$endereco',ed47_v_bairro='$bairro',ed47_v_cep='$cep',ed47_c_nomeresp='$nome_responsavel',
		ed47_v_mae='$nome_mae',ed47_v_sexo='$sexo_aluno',ed47_d_nasc='$data_nascimento',municipio='$cidade',ed47_i_localidade=$codigo_localidade
		WHERE id_alunoreserva = $codigo_aluno ";
		$result = pg_query($conn,$sql_update_aluno);

		$sql_update_escola = "update reserva.escolareserva set ed56_i_escola = $escola, ed221_i_serie = $serie where id_alunoreserva = $codigo_aluno";
        $result = pg_query($conn,$sql_update_escola);


        // chama a pagina de comprovante
                $_SESSION['vch_nome'] = trim($nome_aluno);
				//$_SESSION['turma'] = $turma;
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
