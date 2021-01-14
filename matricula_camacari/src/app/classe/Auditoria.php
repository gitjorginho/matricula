<?php

//session_start();
//$cp_serie = $_SESSION['cp_serie'];
//die ($heh);
//echo (var_dump($_SESSION));
//$cp_serie = (var_dump($_SESSION['cp_serie']));
/*
 * User: Rodolfo Araujo
 * Date: 10/08/2020
 * Time: 10:00
 */
require_once('Conn.php');

class Auditoria extends Conn {

    function __construct() {
        self::conect();
    }

    function getAuditoriaCadastro($aluno) {

        $sql = "SELECT ed47_v_nome,adr_v_acao, adr_v_informacao, reserva.auditoriareserva.id_alunoreserva, "
               ."adr_i_codigo, adr_d_data FROM reserva.auditoriareserva "
               ."INNER JOIN reserva.alunoreserva "
               ."ON auditoriareserva.id_alunoreserva = alunoreserva.id_alunoreserva "
               ."where reserva.auditoriareserva.id_alunoreserva = (".$aluno.") order by adr_d_data Asc";
        //echo ($sql);
        //exit;        
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $auditoriaCadastros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $auditoriaCadastros;
    }
    function getAuditoria($aluno) {

        $sql = "SELECT id, usuario_id, nome_usuario, reserva.auditoriausuarioaluno.id_alunoreserva, descricao, data_modificacao "
               ."FROM reserva.auditoriausuarioaluno "
                ."INNER JOIN reserva.alunoreserva "
                ."ON auditoriausuarioaluno.id_alunoreserva = alunoreserva.id_alunoreserva "
                ."where alunoreserva.id_alunoreserva = (".$aluno.") order by data_modificacao Asc";


        //echo ($sql);
        //exit;        
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $auditoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $auditoria;
    }

}
?>

