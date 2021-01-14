<?php
session_start();
require_once ('conexao.php');
$conexao = new Conexao();
$conn=$conexao->conn();
header("Content-Type: text/html;  charset=ISO-8859-1", true);


if (isset($_GET['funcao'])) {

    switch ($_GET['funcao']) {

        case 'carregar_localidade':
            $codigo_bairro = $_GET['codigo'];

            $sql_localidade = "
                select * 
                from territorio.localidade 
                where loc_i_bairro = $codigo_bairro
            ";
              
            $result = pg_query($conn, $sql_localidade);
            $localidades = pg_fetch_all($result);

            echo '<option></option>';
            foreach ($localidades as $localidade) {
                echo '<option value='.$localidade['loc_i_cod'].'>'.$localidade['loc_v_nome']."</option>";
            }

            break;


        case 'carregar_endereco':

            $codigo = $_GET['codigo'];
            //echo ($codigo);
	    
            $array = explode("-", $codigo);

		//foreach($array as $valores)	{
		$rua = $array[0];
		$bair = $array[1];
		//}
            $sql = " select j14_codigo,j14_nome as endereco, j13_codi as codigo_bairro,j13_descr as bairro ,j29_cep as cep ,trim(ed261_c_nome) as cidade 
            from cadastro.ruas r
            inner join cadastro.ruasbairro rb on r.j14_codigo = rb.j16_lograd
            inner join cadastro.ruasbairrocep rbc on rbc.j32_ruasbairro = rb.j16_codigo
            inner join cadastro.bairro b on b.j13_codi = rb.j16_bairro 
            inner join cadastro.ruascep on j29_codigo = j32_ruascep
	        inner join escola.censomunic on j13_i_censomunic = ed261_i_codigo 	 
            where j14_codigo = {$rua} and j13_codi = {$bair}";

            //echo $sql;
            //exit;
            
            $result = pg_query($conn, $sql);
            $endereco = pg_fetch_assoc($result);

            $enderOBJ = new stdClass();
            $enderOBJ->cep = "{$endereco['cep']}";
            $enderOBJ->cidade = "{$endereco['cidade']}";
            $enderOBJ->bairro = trim(utf8_encode($endereco['bairro']));
            $enderOBJ->codigo_bairro = trim($endereco['codigo_bairro']);
            $enderOBJ->endereco = "{$endereco['endereco']}";


            echo json_encode($enderOBJ);

            break;


        case 'carregar_autocomplete':

            $ender = trim($_GET['endereco']);


            $sql = "select j14_codigo,
                           j14_nome, 
                           j13_descr, 
                           j13_codi,
                           loc_i_cod,
                           loc_v_nome
            from cadastro.ruas r
            inner join cadastro.ruasbairro rb on r.j14_codigo = rb.j16_lograd
            inner join cadastro.ruasbairrocep rbc on rbc.j32_ruasbairro = rb.j16_codigo
            inner join cadastro.bairro b on b.j13_codi = rb.j16_bairro 
            inner join  territorio.localidade on loc_i_bairro  = j13_codi
            where loc_v_nome ilike '%$ender%' 
                 or j14_nome ilike '%$ender%'
                 or j13_descr ilike '%$ender%'
                 ";
          
            echo $sql;
            $result = pg_query($conn, $sql);
            $enderecos = pg_fetch_all($result);

            foreach ($enderecos as $endereco) {
                echo "<option data-localidade='{$endereco['loc_i_cod']}' value='{$endereco['j14_codigo']}-{$endereco['j13_codi']}'>" . $endereco['j14_nome'] .' - '.$endereco['j13_descr'].' - '.$endereco['loc_v_nome']."</option>";
            }
            break;


        case 'select_turma':		
	   
         $data = $_SESSION['sdt_nascimento'];
		//$data = date("d/m/Y", strtotime($data_session));
		// Separa em dia, mês e ano
		list($dia, $mes, $ano) = explode('/', $data);
		$mescorte = 03;
		$diacorte = 31;
		$anocorte = 2020;

		// Descobre que dia é hoje e retorna a unix timestamp
		$hoje = mktime(0, 0, 0, $mescorte, $diacorte, $anocorte);
		// Descobre a unix timestamp da data de nascimento do fulano
		$nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

		// Depois apenas fazemos o cálculo já citado :)
		$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
		//echo ($data."-".$data_session);
	if ($idade == 1){
		    $escola = $_GET['codigo'];
            $sql_turma = "select ed57_i_codigo, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
			case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
			end
			as turno
			from escola e
			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
			where ed18_i_codigo = $escola and (ed57_c_descr ilike '%G1%' or ed57_c_descr ilike '%GRUPO 01%')
			and c.ed52_i_ano = 2020 
			";
	
	}elseif($idade == 2){
		    $escola = $_GET['codigo'];
            $sql_turma = "select ed57_i_codigo, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
			case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
			end
			as turno
			from escola e
			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
			where ed18_i_codigo = $escola and (ed57_c_descr ilike '%G2%' or ed57_c_descr ilike '%GRUPO 02%')
			and c.ed52_i_ano = 2020 
			";
	
	}elseif ($idade == 3){
		    $escola = $_GET['codigo'];
            $sql_turma = "select ed57_i_codigo, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
			case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
			end
			as turno
			from escola e
			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
			where ed18_i_codigo = $escola and (ed57_c_descr ilike '%G3%' or ed57_c_descr ilike '%GRUPO 03%')
			and c.ed52_i_ano = 2020 
			";
	
	}elseif ($idade == 4){
		    $escola = $_GET['codigo'];
            $sql_turma = "select ed57_i_codigo, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
			case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
			end
			as turno
			from escola e
			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
			where ed18_i_codigo = $escola and (ed57_c_descr ilike '%G4%' or ed57_c_descr ilike '%GRUPO 04%')
			and c.ed52_i_ano = 2020 
			";
	
	}elseif ($idade == 5){
		    $escola = $_GET['codigo'];
            $sql_turma = "select ed57_i_codigo, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
			case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
			end
			as turno
			from escola e
			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
			where ed18_i_codigo = $escola and (ed57_c_descr ilike '%G5%' or ed57_c_descr ilike '%GRUPO 05%')
			and c.ed52_i_ano = 2020 
			";
	
		}elseif (($idade > 5)&&($idade < 16)){
		    $escola = $_GET['codigo'];
            $sql_turma = "select ed57_i_codigo, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
			case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
			end
			as turno
			from escola e
			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
			where ed18_i_codigo = $escola and ed11_c_descr not like '%GRUPO%'
			and ed11_c_descr not like '%EIXO%' and c.ed52_i_ano = 2020";
	
		}else{		
            $escola = $_GET['codigo'];
            $sql_turma = "
			select ed57_i_codigo, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
			case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
			end
			as turno
			from escola e
			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
			where ed18_i_codigo = $escola and ed11_c_descr not like '%GRUPO%' and c.ed52_i_ano = 2020 
			";
	}
		//die $sql_turma;
            //where ed18_i_codigo = $escola
            $result = pg_query($conn, $sql_turma);
            $turmas = pg_fetch_all($result);
            echo '<option></option>';
            foreach ($turmas as $turma) {
                echo '<option value=' . $turma['ed57_i_codigo'] . '>' . 'Turma: ' . $turma['turma'] . "- Serie: " . $turma['serie'] . '- Turno: ' . $turma['turno'] . "</option>";
            }
            break;

    }
}


//if (isset($_GET['endereco'])) {
//
//    $ender = $_GET['endereco'];
//
//    $sql = "select j14_codigo,j14_nome, j13_descr
//            from cadastro.ruas r
//            inner join ruasbairro rb on r.j14_codigo = rb.j16_lograd
//            inner join ruasbairrocep rbc on rbc.j32_ruasbairro = rb.j16_codigo
//            inner join bairro b on b.j13_codi = rb.j16_bairro
//            where j14_nome ilike '%{$ender}%'";
//
//
//    $result = pg_query($conn, $sql);
//    $enderecos = pg_fetch_all($result);
//
//    foreach ($enderecos as $endereco) {
//        echo "<option value='{$endereco['j14_codigo']}'>" . $endereco['j14_nome'] . " - " . $endereco['j13_descr'] . "</option>";
//    }
//}
