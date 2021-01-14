<?php

//session_start();
//$cp_serie = $_SESSION['cp_serie'];
//die ($heh);
//echo (var_dump($_SESSION));
//$cp_serie = (var_dump($_SESSION['cp_serie']));
/*
 * User: Aloisio Carvalho
 * Date: 29/01/2020
 * Time: 09:38
 */
require_once('Conn.php');

class Matricula extends Conn {

    function __construct() {
        self::conect();
    }

    function getMatricula($aluno) {

        $sql = "SELECT 
                escola.aluno.ed47_i_codigo,
                escola.matricula.ed60_i_codigo,
                escola.matricula.ed60_d_datamatricula,
                escola.turma.ed57_i_codigo,
                escola.turma.ed57_c_descr,
                trim(escola.escola.ed18_c_nome) AS escola,
                trim(escola.serie.ed11_c_descr) AS serie,
                trim(escola.turno.ed15_c_nome) AS turno
                FROM
                escola.aluno
                INNER JOIN reserva.alunoreserva ON
                    alunoreserva.ed47_i_codigo = aluno.ed47_i_codigo
                INNER JOIN escola.matricula ON
                    matricula.ed60_i_aluno = aluno.ed47_i_codigo
                INNER JOIN escola.turma ON
                    escola.turma.ed57_i_codigo = escola.matricula.ed60_i_turma
                INNER JOIN escola.escola ON
                    escola.turma.ed57_i_escola = escola.escola.ed18_i_codigo
                INNER JOIN escola.matriculaserie ON
                    escola.matriculaserie.ed221_i_matricula = escola.matricula.ed60_i_codigo
                INNER JOIN escola.serie ON
                    escola.serie.ed11_i_codigo = escola.matriculaserie.ed221_i_serie
                INNER JOIN escola.calendario ON
                    escola.calendario.ed52_i_codigo = escola.turma.ed57_i_calendario
                INNER JOIN escola.turno ON
                    escola.turno.ed15_i_codigo = escola.turma.ed57_i_turno
                WHERE
                escola.aluno.ed47_i_codigo IN (" . $aluno . ")  
                AND matricula.ed60_c_situacao = 'MATRICULADO'
                AND matricula.ed60_c_ativa = 'S'
                AND escola.matriculaserie.ed221_c_origem = 'S'
                AND escola.calendario.ed52_i_ano = 2020";
        //echo ($sql);
        //exit;        
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $matricula = $stmt->fetch(PDO::FETCH_ASSOC);
        return $matricula;
    }
    function getUsuarioMatricula($codigoAluno) {
        
        $sql = "SELECT nome from escola.aluno
                INNER JOIN escola.matricula ON
                escola.aluno.ed47_i_codigo = escola.matricula.ed60_i_aluno 
                INNER JOIN escola.matriculamov ON
                escola.matricula.ed60_i_codigo = escola.matriculamov.ed229_i_matricula
                INNER JOIN configuracoes.db_usuarios ON
                configuracoes.db_usuarios.id_usuario = escola.matriculamov.ed229_i_usuario
                where 
                escola.matricula.ed60_i_aluno = ".$codigoAluno." order by escola.matriculamov.ed229_i_codigo desc limit 1"; 
        //echo ($sql);
        //exit;        
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $usuarioMatricula = $stmt->fetch(PDO::FETCH_ASSOC);
        return $usuarioMatricula;
    }

    function matricular() {

        $cp_escolas = $_POST['cp_escolas'];
        $codigo_aluno = $_POST['codigo_aluno'];

        $cp_base = $_POST['cp_base'];
        $cp_calendario = $_POST['cp_calendario'];

        $cp_turmas = $_POST['cp_turmas'];
        $cp_serie = $_SESSION ['cp_serie'];

        //string
        $cp_curso = $_POST['cp_curso'];
        $cp_base = $_POST['cp_base'];
        $cp_calendario = $_POST['cp_calendario'];
        $cp_etapa = $_POST['cp_etapa'];
        $cp_turno = $_POST['cp_turno'];

        //evitar acesso demais

        $sql = ("select ed47_i_codigo from aluno 
            inner join matricula on ed47_i_codigo= ed60_i_aluno
            where ed47_i_codigo = $codigo_aluno and ed60_d_datamatricula > '2020-01-01' and ed60_c_situacao = 'MATRICULADO'");
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $verifica_matricula = $stmt->fetchAll();
        if (count($verifica_matricula) > 0) {
            $response = array('status' => 'erro', 'msg' => utf8_encode('Matricula ja existente'));
            return json_encode($response);
        }

        try {

            $sql_total = "
   			select ed336_vagas, ed57_i_codigo, count (aluno.ed47_v_nome) as alunos, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
   			case
   			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
   			when ed15_c_nome='MANHÃƒ 1' then 'MATUTINO'
   			when ed15_c_nome='NOITE 1' then 'NOTURNO'
   			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
   			when ed15_c_nome='MANHÃƒ 2' then 'MATUTINO'
   			when ed15_c_nome='NOITE 2' then 'NOTURNO'
   			else ed15_c_nome
   			end
   			as turno
   			from escola e
   			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
   			left join turmaturnoreferente ON turmaturnoreferente.ed336_turma = t.ed57_i_codigo
   			left join matricula on t.ed57_i_codigo = ed60_i_turma
   			left join aluno on ed47_i_codigo = ed60_i_aluno
   			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
   			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
   			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
   			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
   			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
   			where ed18_i_codigo = $cp_escolas and c.ed52_i_ano = 2020 and ed11_i_codigo =  $cp_serie and ed57_i_codigo = $cp_turmas
			and (ed60_c_situacao like '%MATRICULADO%' or ed60_c_situacao like '%TRANSFERIDO REDE%' OR ed60_c_situacao like '%RECLASSIFICADO%')
   			group by ed57_i_codigo, ed57_c_descr, ed11_c_descr, ed15_c_nome, ed336_vagas
   			having count (distinct(aluno.ed47_v_nome)) < ed336_vagas
   			";
            $stmt = self::$conexao->prepare($sql_total);
            $stmt->execute();
            $pesqtotal = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e;
        }
        $resulttotal = $pesqtotal["ed57_i_codigo"];


        if ($resulttotal != '') {

            //PESQUISA DE BASE

            try {
                $sql = ("select * from base where ed31_c_descr = '$cp_base'");
                $stmt = self::$conexao->prepare($sql);
                $stmt->execute();
                $pesqbase = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo $e;
            }
            $resultbase = $pesqbase["ed31_i_codigo"];

            //PESQUISA DE CALENDARIO

            try {
                $sql = ("select * from calendario where ed52_c_descr = '$cp_calendario'");
                $stmt = self::$conexao->prepare($sql);
                $stmt->execute();
                $pesqcalendario = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo $e;
            }
            $resultcalendario = $pesqcalendario["ed52_i_codigo"];

            //PESQUISA DE TURNO

            try {
                $sql = ("select * from turno where ed15_c_nome = '$cp_turno'");
                $stmt = self::$conexao->prepare($sql);
                $stmt->execute();
                $pesqturno = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo $e;
            }
            $resultturno = $pesqturno["ed15_i_codigo"];

            //die ($resultturno);

            try {
                $sql = ("select * from alunocurso where ed56_i_aluno = $codigo_aluno");
                $stmt = self::$conexao->prepare($sql);
                $stmt->execute();
                $pesqalucurso = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo $e;
            }
            $resurso = $pesqalucurso["ed56_i_codigo"];

            if ($pesqalucurso["ed56_i_codigo"] == '') {
                try {
                    $insert_alunocurso = "insert into alunocurso (ed56_i_escola,ed56_i_aluno,ed56_i_base,ed56_i_calendario, ed56_c_situacao, 
                   ed56_i_baseant, ed56_i_calendarioant, ed56_c_situacaoant)
                   values ($cp_escolas,$codigo_aluno,$resultbase,$resultcalendario,'MATRICULADO',null,null,null) returning ed56_i_codigo;";

                    $stmt = self::$conexao->prepare($insert_alunocurso);
                    $stmt->execute();
                    $alcurso = $stmt->fetch(PDO::FETCH_ASSOC);
                    $resurso = $alcurso['ed56_i_codigo'];

                    //insert em possib

                    $insert_alunopossib = "insert into alunopossib (ed79_i_alunocurso,ed79_i_serie, ed79_i_turno, ed79_i_turmaant, 
   				ed79_c_resulant, ed79_c_situacao) values 
   			   ($resurso,$cp_serie,$resultturno,null,null,null);";

                    $stmt = self::$conexao->prepare($insert_alunopossib);
                    $stmt->execute();
                    $alpossib = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo $e;
                }
            } else {
                //UPDATES alunocurso, alunopossib

                try {

                    $update_alunocurso = "update alunocurso set ed56_i_escola = $cp_escolas, ed56_i_base = $resultbase, 
   					ed56_i_calendario = $resultcalendario, ed56_c_situacao = 'MATRICULADO' where ed56_i_aluno = $codigo_aluno";

                    $stmt = self::$conexao->prepare($update_alunocurso);
                    $stmt->execute();
                    $upalcurso = $stmt->fetch(PDO::FETCH_ASSOC);

                    $update_alunopossib = "update alunopossib set ed79_i_serie = $cp_serie, 
   				  ed79_i_turno = $resultturno where ed79_i_alunocurso = $resurso";

                    $stmt = self::$conexao->prepare($update_alunopossib);
                    $stmt->execute();
                    $upalpossib = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo $e;
                }
            }

            try {
                $insert_matricula = "insert into matricula (ed60_i_aluno,ed60_i_turma,
               ed60_i_numaluno, ed60_c_situacao, ed60_c_concluida, ed60_i_turmaant, ed60_c_rfanterior,
               ed60_d_datamatricula, ed60_d_datamodif, ed60_t_obs, ed60_c_ativa, ed60_c_tipo, ed60_c_parecer,
               ed60_d_datasaida, ed60_d_datamodifant, ed60_tipoingresso)
               values ($codigo_aluno,$cp_turmas,null,'MATRICULADO',
               'N',null, null, now(),now(), 'N','S', 'N', null,null, now(), 1) returning ed60_i_codigo ;";

                $stmt = self::$conexao->prepare($insert_matricula);
                $stmt->execute();
                $matrrt = $stmt->fetch(PDO::FETCH_ASSOC);
                $resmatr = $matrrt['ed60_i_codigo'];
            } catch (PDOException $e) {
                echo $e;
            }

            //PESQUISA DE TURMAREFERENTE

            try {
                $sql = ("select * from turmaturnoreferente where ed336_turma = '$cp_turmas'");
                $stmt = self::$conexao->prepare($sql);
                $stmt->execute();
                $pesqturmaturnoreferente = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo $e;
            }
            $resultturmaturnoreferente = $pesqturmaturnoreferente["ed336_codigo"];



            try {
                $insert_matriculaturnoreferente = "insert into matriculaturnoreferente (ed337_matricula,ed337_turmaturnoreferente)
               values ($resmatr,$resultturmaturnoreferente);";

                $stmt = self::$conexao->prepare($insert_matriculaturnoreferente);
                $stmt->execute();
                $matrttr = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo $e;
            }

            try {
                $insert_matriculaserie = "insert into matriculaserie (ed221_i_matricula,ed221_i_serie, ed221_c_origem)
               values ($resmatr,$cp_serie, 'S');";
                $stmt = self::$conexao->prepare($insert_matriculaserie);
                $stmt->execute();
                $matrserie = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo $e;
            }
            $sql_verifica = "select ed47_i_codigo from aluno 
                inner join matricula on ed47_i_codigo= ed60_i_aluno
                where ed60_d_datamatricula > '2020-01-01' and ed60_i_codigo = $resmatr";
            $stmt = self::$conexao->prepare($sql_verifica);
            $stmt->execute();
            $verifica = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($verifica['ed47_i_codigo'] == '' || $verifica['ed47_i_codigo'] == null) {
                $response = array('status' => 'erro', 'msg' => utf8_encode('Matricula nao efetivada!'));
                return json_encode($response);
            } else {

                if ($verifica['ed47_i_codigo'] == $codigo_aluno) {
                    $response = array('status' => 'ok', 'msg' => utf8_encode('Matriculado com sucesso! Matricula : ' . $resmatr));
                    //return 'Matriculou com sucesso! Matricula : ' . $resmatr;
                    return json_encode($response);
                } else {
                    $response = array('status' => 'erro', 'msg' => utf8_encode('Matricula nao efetivada!'));
                    return json_encode($response);
                }
            }
        } else {
            $response = array('status' => 'erro', 'msg' => utf8_encode('Não há vaga na Turma selecionada!'));
            return json_encode($response);
        }
    }

}
?>

