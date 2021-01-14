<?php

/*
 * User: Rodolfo Araujo
 * Date: 23/08/2020
 * Time: 21:00
 */
require_once('Conn.php');

class Agendamento extends Conn {

    function __construct() {
        self::conect();
    }

    function getHorarioAgendamento() {

        $sql = "SELECT trim(re006_v_horarioagendamento) as re006_v_horarioagendamento
	FROM reserva.horarioagendamento;";
        //echo ($sql);
        //exit;        
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $horarioAgendamento = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $horarioAgendamento;
    }

}
?>

