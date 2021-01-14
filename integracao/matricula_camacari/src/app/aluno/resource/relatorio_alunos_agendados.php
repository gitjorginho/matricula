<?php
session_start();
require_once('../../../../../library/fpdf.php');
require_once('../../classe/Conn.php');
require_once('../../include/funcoes.php');
header("Content-Type: text/html;  charset=ISO-8859-1", true);
Conn::conect();

$date_initial = dateToDatabase($_GET['date_initial']).' 00:00'; 
$date_end = dateToDatabase($_GET['date_end']).' 23:59';

//die(var_dump($_GET));

if ($_GET['escola'] != "") {
    $where_escola = " and ed18_i_codigo in ({$_GET['escola']})";
} else {
    $where_escola = '';
}

if ($_GET['com_periodo'] == 'on') {
    $where_composto = " and ed47_d_agedamento between ('$date_initial') and ('$date_end')";
}else{
    $where_composto = '';
}

// todas as escolas reserva.escolareserva
$sql_escola =
    "select ed56_i_escola, ed18_c_codigoinep, trim(ed18_c_nome) as ed18_c_nome from reserva.escolareserva
join escola.escola on ed56_i_escola  = ed18_i_codigo
join reserva.alunoreserva on alunoreserva.id_alunoreserva  = escolareserva.id_alunoreserva
join reserva.auditoriareserva on auditoriareserva.id_alunoreserva = alunoreserva.id_alunoreserva
where alunostatusreserva_id in(7) $where_escola $where_composto 
group by ed56_i_escola,ed18_c_codigoinep,ed18_c_nome
order by ed18_c_nome";

//die($sql_escola);
$stmt = Conn::$conexao->prepare($sql_escola);
$stmt->execute();
$reserva_escolas = $stmt->fetchAll();


class PDF extends FPDF
{
    function Footer()
    {
        $this->SetFont('arial', '', 6);
        $this->SetY(-15);
        $this->Line(10, 200, 290, 200);
        $this->Cell(40, 5, 'Portal Lista Reserva', 0, 1, 'L');
        $this->Cell(40, 5, 'Versao 1', 0, 0, 'L');
        $this->Cell(50, 5, basename($_SERVER['PHP_SELF'], '.php') . '.php', 0, 0, 'L');
        $this->Cell(100, 5, 'Emissor : ' . $_SESSION['id_usuario'] . '-' . $_SESSION['nome'], 0, 0, 'C');
        $this->Cell(30, 5, 'Exercício: ' . Date('Y', strtotime('today')), 0, 0, 'L');
        $this->Cell(30, 5, 'Data: ' . Date('d/m/Y', strtotime('today')), 0, 0, 'L');
        $this->AliasNbPages();
        $this->Cell(290, 5, 'Pagina ' . $this->PageNo() . ' de {nb}', 0, 1, 'L');
    }
}

$oPdf = new PDF('L');
$oPdf->AliasNbPages();
$oPdf->setfillcolor(223);
$oPdf->SetAutoPageBreak(true, 20);
$oPdf->addPage();

$oPdf->Image('../../../../../img/Cabecalho_pdf_L1.png', 3, 3, 290);
$oPdf->SetXY(20, 40);
$oPdf->SetFont('arial', 'b', 8);
$oPdf->Text('220', '23', 'Data da Impressão : ' . date('d/m/Y', strtotime('today')));
$oPdf->Text('220', '27', 'Usuário: ' . $_SESSION['id_usuario'] . ' - ' . $_SESSION['nome']);
 $oPdf->Text('220', '31', 'Status: Agendado para Matrícula ' );
if ($_GET['com_periodo'] == 'on') {
    $oPdf->Text('220', '35', 'Período: ' . date('d/m/Y',strtotime($date_initial)) . ' até ' . date('d/m/Y',strtotime($date_end)));
}
$oPdf->SetXY(10, 50);


$total_aluno_geral = 0;
foreach ($reserva_escolas as $escola) {

    //titulos das escolas
    $oPdf->SetFont('arial', 'b', 8);
    $oPdf->Ln();

    $oPdf->Cell(20, 5, $escola['ed18_c_codigoinep'], 1, 0, 'C', 1);
    $oPdf->Cell(260, 5, $escola['ed18_c_nome'], 1, 1, 'C', 1);


    //Titulos das series
    $sql_serie = "select ed221_i_serie , trim(ed11_c_descr) as ed11_c_descr from reserva.escolareserva
    join escola.serie on ed11_i_codigo  = ed221_i_serie 
    join reserva.alunoreserva on alunoreserva.id_alunoreserva  = escolareserva.id_alunoreserva
    join reserva.auditoriareserva on auditoriareserva.id_alunoreserva = alunoreserva.id_alunoreserva
    where alunostatusreserva_id in(7) and ed56_i_escola = {$escola['ed56_i_escola']} $where_composto
    group by ed221_i_serie,ed11_c_descr
    order by ed11_c_descr";

    //die ($sql_serie);

    $stmt = Conn::$conexao->prepare($sql_serie);
    $stmt->execute();
    $reserva_series = $stmt->fetchAll();


    $total_aluno_escola = 0;

    foreach ($reserva_series as $serie) {
        $oPdf->SetFont('arial', 'b', 8);
        $oPdf->Cell(280, 5, $serie['ed11_c_descr'], 1, 1, 'L', 1);

        $oPdf->SetFont('arial', 'b', 6);
        $oPdf->Cell(30, 5, 'Data do Agendamento', 1, 0, 'C', 1);
        $oPdf->Cell(15, 5, 'Cod. SGE', 1, 0, 'C', 1);
        $oPdf->Cell(10, 5, 'ID', 1, 0, 'C', 1);
        $oPdf->Cell(65, 5, 'Nome Aluno', 1, 0, 'C', 1);
        $oPdf->Cell(15, 5, 'Nascimento', 1, 0, 'C', 1);
        $oPdf->Cell(55, 5, 'Mãe', 1, 0, 'C', 1);
        $oPdf->Cell(55, 5, 'Responsavel', 1, 0, 'C', 1);
        $oPdf->Cell(35, 5, 'Usuário', 1, 1, 'C', 1);
        

        $sql_alunos = "select
        reserva.alunoreserva.ed47_i_codigo, 
        reserva.alunoreserva.id_alunoreserva,
        trim(reserva.alunoreserva.ed47_v_nome) ed47_v_nome,
        to_char(reserva.alunoreserva.ed47_d_nasc, 'DD/MM/YYYY') as ed47_d_nasc,
        reserva.alunoreserva.ed47_d_nasc as data_nascimento,
        trim(reserva.alunoreserva.ed47_v_mae) as ed47_v_mae,
        trim(reserva.alunoreserva.ed47_c_nomeresp) as ed47_c_nomeresp ,
        coalesce(reserva.alunostatusreserva.status_descr,'Cadastrado') as status_descr ,
        (select nome_usuario from reserva.auditoriausuarioaluno where reserva.auditoriausuarioaluno.id_alunoreserva = reserva.alunoreserva.id_alunoreserva order by data_modificacao desc limit 1),
        ed47_d_agedamento
        from reserva.alunoreserva
        join reserva.auditoriareserva on auditoriareserva.id_alunoreserva = alunoreserva.id_alunoreserva
        left join reserva.escolareserva on	reserva.alunoreserva.id_alunoreserva = reserva.escolareserva.id_alunoreserva
        left join reserva.alunostatusreserva on id = alunostatusreserva_id
        where alunostatusreserva_id in(7) and ed56_i_escola = {$escola['ed56_i_escola']} and ed221_i_serie = {$serie['ed221_i_serie']}  $where_composto
        order by ed47_d_agedamento,
        reserva.alunoreserva.ed47_v_nome";

        //die($sql_alunos);
        $stmt = Conn::$conexao->prepare($sql_alunos);
        $stmt->execute();
        $reserva_alunos = $stmt->fetchAll();

        $oPdf->SetFont('arial', '', 6);

        foreach ($reserva_alunos as $aluno) {
            $total_aluno_escola++;
            $total_aluno_geral++;

            if ($aluno['ed47_d_agedamento'] == ''){
                $dataAgendamento = '';
            }
            else{
                $dataAgendamento = date("d/m/Y H:i:s", strtotime($aluno['ed47_d_agedamento']));
            }    
            $oPdf->Cell(30, 5,  $dataAgendamento, 1, 0, 'C');

            $codigo_sge = $aluno['ed47_i_codigo'];
            $oPdf->Cell(15, 5, $codigo_sge, 1, 0, 'C');
            $oPdf->Cell(10, 5,  $aluno['id_alunoreserva'], 1, 0, 'C');
            $oPdf->Cell(65, 5, $aluno['ed47_v_nome'], 1, 0, 'L');
            $oPdf->Cell(15, 5,  $aluno['ed47_d_nasc'], 1, 0, 'L');
            $oPdf->Cell(55, 5,  $aluno['ed47_v_mae'], 1, 0, 'L');
            $oPdf->Cell(55, 5,  $aluno['ed47_c_nomeresp'], 1, 0, 'L');
            $oPdf->Cell(35, 5, retornaPrimeiroUltimoNome($aluno['nome_usuario']), 1, 1, 'C');
        }
    }


    $oPdf->SetFont('arial', 'b', 8);
    $oPdf->Cell(280, 5, 'Total de alunos : ' . $total_aluno_escola, 1, 1, 'L', 1);
}
$oPdf->SetFont('arial', 'b', 8);
$oPdf->Ln();
$oPdf->Cell(280, 5, 'Total geral de alunos : ' . $total_aluno_geral, 1, 1, 'L', 1);
$oPdf->Image('../../../../../img/rodape.png', 0, 277, 210);
$oPdf->SetFont('arial', 'i', 6);
$data_atual = date("d-m-Y H:i:s");
$oPdf->Text(130, 288.5, $data_atual);



$oPdf->Output();


function dateToView($data)
{
    if ($data != '') {
        list($ano, $mes, $dia) = explode('-', $data);
        $ano = trim($ano);
        $mes = trim($mes);
        $dia = trim($dia);
        return "$dia/$mes/$ano";
    }
    return '';
}

function dateToDatabase($data)
{
    list($dia, $mes, $ano) = explode('/', $data);
    $ano = trim($ano);
    $mes = trim($mes);
    $dia = trim($dia);
    return "$ano-$mes-$dia";
}
