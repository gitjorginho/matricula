<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('Conn.php');
require_once(__DIR__ . '/../../../../email.php');

class Aluno extends Conn {

    function __construct() {
        self::conect();
    }

    function allStatus() {
        $sql = "select * from reserva.alunostatusreserva order by status_descr";
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $status = $stmt->fetchAll();
        return $status;
    }

    function all() {
        $sql = "select * from escola.aluno left join matriculareserva on reserva_aluno = ed47_i_codigo
		left join escola.matricula on ed60_i_aluno = ed47_i_codigo 
		left join escola.turma on ed60_i_turma = ed57_i_codigo limit 50";
        $alunos = self::$conexao->query($sql);
        return $alunos;
    }

    function getAluno($aluno) {

        $sql = "
            select 
            *, 
                (
                select 
                    ed261_c_nome
                from 
                    escola.censomunic 
                where 
                    ed261_i_codigo = 2905701) as cidade, 
                (
                select 
                    loc_v_nome 
                from 
                    territorio.localidade 
                where 
                loc_i_cod = ed47_i_localidade
                )
            from 
            reserva.alunoreserva as ar
            left join reserva.escolareserva as er 
                on ar.id_alunoreserva = er.id_alunoreserva
            left join escola.serie as s 
                on er.ed221_i_serie = s.ed11_i_codigo
            where 
            ar.id_alunoreserva = $aluno";
        //die($sql);

        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aluno;
    }

    function getAlunoMatriculado($aluno) {
        $sql = "select *,
               (select ed261_c_nome from censomunic where ed261_i_codigo = ed47_i_censomunicend) as cidade,
                (select loc_v_nome from territorio.localidade where loc_i_cod = ed47_i_localidade)
                from aluno
				left join matriculareserva on reserva_aluno = ed47_i_codigo
				left join matricula on ed60_i_aluno = reserva_aluno
                left join turma on ed57_i_codigo = ed60_i_turma
                left join turno on ed57_i_turno = ed15_i_codigo
                left join escola on ed18_i_codigo = ed57_i_escola
				left join serie s on reserva_turma = s.ed11_i_codigo
                where ed47_i_codigo = $aluno and ed60_c_situacao = 'MATRICULADO' AND ed60_d_datamatricula > '2020-01-01'";

        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aluno;
    }

    function findAluno($aluno, $cod_aluno, $responsavel, $data_nascimento, $status_id, $offset = '0') {

        if ($data_nascimento != '') {
            $data_to_database = $this->dateToDatabase($data_nascimento);
            $where_data_nascimento = "and ed47_d_nasc = '$data_to_database' ";
        } else {
            $where_data_nascimento = '';
        }
       
        if ($status_id == '') {
            $where_status = '';
        } else {
            $where_status = " and alunostatusreserva_id in ({$status_id})";
        }
        if ($cod_aluno != '') {

            $where_codigo_aluno = " ar.id_alunoreserva = " . $cod_aluno . " and ";
        } else {
            $where_codigo_aluno = "";
        }

        $sql = "
            select 
                ar.id_alunoreserva, 
                ed47_v_nome,
                ed47_c_nomeresp,
                ed47_d_nasc, 
                ed11_c_descr,
                status_descr,
                alunostatusreserva_id,
                ed47_v_codigoseguranca
            from reserva.alunoreserva ar
            left join reserva.escolareserva as er 
                on ar.id_alunoreserva = er.id_alunoreserva
            left join escola.serie as s 
                on er.ed221_i_serie = s.ed11_i_codigo
            inner join reserva.alunostatusreserva asr 
            on asr.id = ar.alunostatusreserva_id
            where 
                $where_codigo_aluno ed47_v_nome ilike '%{$aluno}%' and 
                ed47_c_nomeresp ilike '%$responsavel%' 
                $where_data_nascimento 
                $where_status
            order by 
                ar.id_alunoreserva  
            limit 40 offset  $offset";
        //echo $sql;
        
        $alunos = self::$conexao->query($sql);

        $sql = "
            select 
                count(*) as total_registros
            from reserva.alunoreserva ar
            where 
                $where_codigo_aluno 
                ar.ed47_v_nome ilike '%{$aluno}%' and
                ar.ed47_c_nomeresp ilike '%{$responsavel}%'
                $where_data_nascimento 
                $where_status";

        $registros = self::$conexao->query($sql);
        foreach ($registros as $aluno) {
            $_SESSION['total_registros'] = $aluno['total_registros'];
        }

        return $alunos;
    }

    function updateAluno() {
        $aluno = $_GET;
        if (isset($_SESSION['id_usuario'])) {

         $validacao = $this->validacao($aluno);
         if($validacao != true){
             return $validacao;
         }
            // TRATA A IMPRESSÃO DA AUTORIAÇÃO DE MATRÍCULA QUANTO AGENDADA.
            //$_SESSION['matriculaAgendada'] = $_GET['alunostatusreserva_id'];
            
            $data_nascimento = $this->dateToDatabase($aluno['data_nascimento']);
            $_SESSION['statusAluno'] = $aluno['alunostatusreserva_id']; 
            $_SESSION['codigo'] = $aluno['codigo'];
            $_SESSION['dataOperacao'] = date("d-m-Y H:i:s"); 
            
            $sql = "
                UPDATE
                reserva.alunoreserva SET 
                    ed47_v_nome='{$aluno['nome_aluno']}',
                    ed47_v_ender ='{$aluno['endereco']}',
                    ed47_v_bairro = '{$aluno['bairro']}', 
                    ed47_v_compl = '{$aluno['ed47_v_compl']}',
                    ed47_v_cep='{$aluno['cep']}',
                    ed47_v_telef='{$aluno['telefone']}',
                    ed47_c_nomeresp='{$aluno['responsavel']}',
                    ed47_d_nasc='{$data_nascimento}',
                    ed47_v_mae='{$aluno['mae']}',
                    ed47_v_sexo='{$aluno['sexo']}',
                    ed47_c_numero='{$aluno['numero']}',
                    ed47_i_localidade='{$aluno['localidade']}',
                    ed47_v_cpf='{$aluno['reponsavel_cpf']}',
                    vch_orgaopublico='{$aluno['vch_orgaopublico']}',
                    email_resp='{$aluno['email_resp']}',
                    alunostatusreserva_id='{$aluno['alunostatusreserva_id']}',
                    observacao='{$aluno['observacao']}'
                WHERE 
                    id_alunoreserva ={$aluno['codigo']}";

            self::$conexao->query($sql);

            $sql_cpf_responsavel = "
                UPDATE 
                reserva.escolareserva SET 
                    ed56_i_escola= '{$aluno['escola']}',
                    ed221_i_serie = '{$aluno['serie']}'  
                WHERE 
                    id_alunoreserva={$aluno['codigo']}";
            self::$conexao->query($sql_cpf_responsavel);

            // registrando na tabela auditoriausuarioaluno
            //die(var_dump($_SESSION['id_usuario'],$_SESSION['nome']));
            $sql_auditoria_usuario = "
                INSERT INTO 
                reserva.auditoriausuarioaluno
                    (
                    usuario_id,
                    nome_usuario,
                    id_alunoreserva,
                    descricao,
                    data_modificacao
                    )
                VALUES
                    (
                    {$_SESSION['id_usuario']},
                    '{$_SESSION['nome']}',
                    {$aluno['codigo']},
                    'atualizar dados aluno',
                    '{$_SESSION['dataOperacao']}'
                    ) 
                returning data_modificacao;";

            $stmt = self::$conexao->prepare($sql_auditoria_usuario);
            $stmt->execute();
            $data_modificacao = $stmt->fetch(PDO::FETCH_ASSOC);


            // enviar email
            //$this->EnviarEmail($aluno['codigo'], $aluno['nome_aluno'], $aluno['escola'], $aluno['serie'], $aluno['alunostatusreserva_id'], $_SESSION['id_usuario'], $_SESSION['nome'], $data_modificacao['data_modificacao']);


            $response = array(
                'status' => 'ok'
            );
            return json_encode($response);
        }


        return json_encode(['status' => 'expirou']);
    }


    function validacao($aluno){

        // varifica se o nome do aluno sóe tem letras
         if (!ctype_alpha($aluno['nome_aluno'])){
             return 'Nome Ivalido';
         }
    }


    function storeAluno() {
        $aluno = $_GET;

        $nome_aluno = trim($aluno['nome']);
        $endereco = trim($aluno['endereco']);
        $serie = trim($aluno['serie']);
        $bairro = trim($aluno['bairro']);
        $cep = trim($aluno['cep']);
        $telefone = trim($aluno['telefone']);
        $responsavel = trim($aluno['responsavel']);
        $mae = trim($aluno['mae']);
        $sexo = trim($aluno['sexo']);
        $numero = trim($aluno['numero']);
        $localidade = trim($aluno['localidade']);
        $data_nascimento = $this->dateToDatabase($aluno['data_nascimento']);

        $sql = "select * from aluno
                where TRIM(ed47_v_nome) ilike trim('$nome_aluno')
                and TRIM(ed47_c_nomeresp) ilike TRIM('$responsavel')
                and ed47_d_nasc = '$data_nascimento'";

        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $alunos = $stmt->fetchAll();

        if (count($alunos) != 0) {
            $response = array('status' => 'Erro !', 'msg' => 'Aluno j? Cadastrado.');
            return json_encode($response);
        }

        $sql = "
            INSERT INTO aluno 
                (
                ed47_v_nome,
                ed47_v_ender,
                ed47_v_bairro,
                ed47_v_cep,
                ed47_v_telef,
                ed47_c_nomeresp,
                ed47_d_nasc,
                ed47_v_mae,
                ed47_v_sexo,
                ed47_c_numero,
                ed47_i_localidade,
                ed47_i_censomunicend,
                ed47_i_pais
                ) 
            VALUES 
                (
                '$nome_aluno',
                '$endereco',
                '$bairro',
                '$cep',
                '$telefone',
                '$responsavel',
                '{$data_nascimento}',
                '$mae',
                '$sexo',
                '$numero',
                '$localidade',
                '2905701',
                10
                ) 
            RETURNING ed47_i_codigo";


        $codigo_aluno = self::$conexao->query($sql);


        foreach ($codigo_aluno as $codigo) {
            $codigo_return = $codigo['ed47_i_codigo'];
        }

        $sql_reserva = "INSERT INTO matriculareserva (reserva_aluno,reserva_cpfresponsavel,reserva_turma)  VALUES ('{$codigo_return}','{$aluno['reponsavel_cpf']}','{$aluno['serie']}')";
        self::$conexao->query($sql_reserva);

        $response = array('status' => '', 'msg' => 'Aluno Cadastrado Com Sucesso ! Codigo:' . $codigo_return);
        return json_encode($response);
    }

    private function EnviarEmail($aluno_id, $nome, $escola_destino, $serie_destino, $status_id, $usuario_id, $usuario_nome, $data_modificacao) {

        //Pega o nome da escola
        $SqlEscola = "select ed18_c_nome from escola where ed18_i_codigo = $escola_destino ";

        $stmt = self::$conexao->prepare($SqlEscola);
        $stmt->execute();
        $Escola = $stmt->fetch(PDO::FETCH_ASSOC);

        //Pega o nome da serie  
        $SqlSerie = "select ed11_c_descr from serie where ed11_i_codigo = $serie_destino ";

        $stmt = self::$conexao->prepare($SqlSerie);
        $stmt->execute();
        $Serie = $stmt->fetch(PDO::FETCH_ASSOC);

        //Pega o nome do status
        $SqlStatus = "select status_descr from reserva.alunostatusreserva where id = $status_id ";

        $stmt = self::$conexao->prepare($SqlStatus);
        $stmt->execute();
        $Status = $stmt->fetch(PDO::FETCH_ASSOC);

        //pega o codigo sge
        $SqlStatus = "select ed47_i_codigo from reserva.alunoreserva where id_alunoreserva = $aluno_id ";

        $stmt = self::$conexao->prepare($SqlStatus);
        $stmt->execute();
        $ed47_i_codigo = $stmt->fetch(PDO::FETCH_ASSOC);


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
                <th>id</th>
                <th>aluno</th>
                <th>Escola Destino</th>
                <th>Serie Destino</th>
                <th>Status</th>
                <th>Atividade</th>
              </thead>
              <tbody>
              <td>' . $ed47_i_codigo['ed47_i_codigo'] . '</td>
              <td>' . $aluno_id . '</td>
              <td>' . trim($nome) . '</td>
              <td>' . trim($Escola['ed18_c_nome']) . '</td>
              <td>' . trim($Serie['ed11_c_descr']) . '</td>
              <td>' . trim($Status['status_descr']) . '</td>
                <td>Dados atualizado com acesso restrito</td>
              </tbody>
            </table>
            <br>
             <table border="1" style="width: 100%;">
                  <thead>
                  <th>Id Usuario </th>
                  <th>Nome Usuario </th>
                  </thead>
                  <tbody>
                   <td>' . $usuario_id . '</td>
                   <td>' . $usuario_nome . '</td>
                  </tbody>       
             </table>    
            <br>    
            <table style="width: 100%;">
              <thead>
                <th style="background: gray; color:white; height:50px"> Data Registro: ' . $data_modificacao . '</th>
              </thead>
            </table>
            </body>
            </html>
            ';
        $mailDestino = 'auditoria.listadeespera@educa.camacari.ba.gov.br'; // Email da seduc
        //$mailDestino = 'rodolfosaneto@gmail.com'; // Email pessoal
        envialEmail($mensagem,$mailDestino,'','');
    }

    private function dateToDatabase($data) {
        if ($data != '') {
            list($dia, $mes, $ano) = explode('/', $data);
            $ano = trim($ano);
            $mes = trim($mes);
            $dia = trim($dia);
            return "$ano-$mes-$dia";
        }
        return '';
    }

}
