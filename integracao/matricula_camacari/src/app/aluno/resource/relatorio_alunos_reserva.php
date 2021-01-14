<?php

session_start();
require_once('../../../../../library/fpdf.php');
require_once('../../classe/Conn.php');
header("Content-Type: text/html;  charset=ISO-8859-1", true);
Conn::conect();


$date_initial = dateToDatabase($_GET['date_initial']);
$date_end = dateToDatabase($_GET['date_end']);

//die(var_dump($_GET));

// if($_GET['com_periodo'] == 'on'){
//     $where_date = "and adr_d_data between '$date_initial' and '$date_end'"; 
//  }else{
//      $where_date = "";
//  }



if ($_GET['escola'] != 0) {
    $where_escola = "where ed18_i_codigo = {$_GET['escola']}";
} else {
    $where_escola = '';
}

if ($_GET['status'] != '') {
    $where_status = "and alunostatusreserva_id in({$_GET['status']})";
} else {
    $where_status = '';
}

$where_composto =  '';
if ($_GET['status'] == '' and  $_GET['escola'] == '0') {
    $where_composto =  '';
} else {
    $where_composto .= ' where ';

    $w_status = '';
    if ($_GET['status'] != '') {
        $w_status = " alunostatusreserva_id in( {$_GET['status']})";
    }

    $w_escola = '';
    if ($_GET['escola'] != '0') {
        $w_escola .= " ed18_i_codigo = {$_GET['escola']}";
    }

    if ($_GET['status'] != '' and  $_GET['escola'] != '0') {
        $where_composto .= $w_status . ' and ' . $w_escola;
    } else {
        $where_composto .= $w_status . $w_escola;
    }
}
$where_period = '';
if ($_GET['com_periodo'] == 'on') {
    $where_period = " and adr_d_data between '$date_initial' and '$date_end'";
    if ($where_composto == '') {
        $where_composto = " where adr_d_data between '$date_initial' and '$date_end'";
    } else {
        $where_composto .= " and adr_d_data between '$date_initial' and '$date_end'";
    }
}

// todas as escolas reserva.escolareserva
$sql_escola =
    "select ed56_i_escola, trim(ed18_c_nome) as ed18_c_nome from reserva.escolareserva
join escola.escola on ed56_i_escola  = ed18_i_codigo
join reserva.alunoreserva on alunoreserva.id_alunoreserva  = escolareserva.id_alunoreserva
join reserva.auditoriareserva on auditoriareserva.id_alunoreserva = alunoreserva.id_alunoreserva
$where_composto 
group by ed56_i_escola,ed18_c_nome
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
        $this->Cell(40, 5, 'Versao 2', 0, 0, 'L');
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

$oPdf->SetXY(10, 50);


$total_aluno_geral = 0;
foreach ($reserva_escolas as $escola) {

    //titulos das escolas
    $oPdf->SetFont('arial', 'b', 8);
    $oPdf->Ln();

    $oPdf->Cell(280, 5, $escola['ed18_c_nome'], 1, 1, 'C', 1);


    //Titulos das series
    $sql_serie = "select ed221_i_serie , trim(ed11_c_descr) as ed11_c_descr from reserva.escolareserva
    join serie on ed11_i_codigo  = ed221_i_serie 
    join reserva.alunoreserva on alunoreserva.id_alunoreserva  = escolareserva.id_alunoreserva
    join reserva.auditoriareserva on auditoriareserva.id_alunoreserva = alunoreserva.id_alunoreserva
    where ed56_i_escola = {$escola['ed56_i_escola']} $where_status $where_period
    group by ed221_i_serie,ed11_c_descr
    order by ed11_c_descr";



    $stmt = Conn::$conexao->prepare($sql_serie);
    $stmt->execute();
    $reserva_series = $stmt->fetchAll();


    $total_aluno_escola = 0;

    foreach ($reserva_series as $serie) {
        $oPdf->SetFont('arial', 'b', 8);
        $oPdf->Cell(280, 5, $serie['ed11_c_descr'], 1, 1, 'L', 1);

        $oPdf->SetFont('arial', 'b', 6);
        $oPdf->Cell(10, 5, 'Cod. SGE', 1, 0, 'C', 1);
        $oPdf->Cell(10, 5, 'ID', 1, 0, 'C', 1);
        $oPdf->Cell(55, 5, 'Nome Aluno', 1, 0, 'C', 1);
        $oPdf->Cell(15, 5, 'Nascimento', 1, 0, 'C', 1);
        $oPdf->Cell(55, 5, 'Mãe', 1, 0, 'C', 1);
        $oPdf->Cell(55, 5, 'Responsavel', 1, 0, 'C', 1);
        $oPdf->Cell(65, 5, 'Status', 1, 0, 'C', 1);
        $oPdf->Cell(15, 5, 'Data matricula', 1, 1, 'C', 1);


        $sql_alunos = "select
        reserva.alunoreserva.ed47_i_codigo, 
        reserva.alunoreserva.id_alunoreserva,
        trim(reserva.alunoreserva.ed47_v_nome) ed47_v_nome,
        to_char(reserva.alunoreserva.ed47_d_nasc, 'DD/MM/YYYY') as ed47_d_nasc,
        reserva.alunoreserva.ed47_d_nasc as data_nascimento,
        trim(reserva.alunoreserva.ed47_v_mae) as ed47_v_mae,
        trim(reserva.alunoreserva.ed47_c_nomeresp) as ed47_c_nomeresp ,
        coalesce(reserva.alunostatusreserva.status_descr,'Cadastrado') as status_descr ,
        (select ed60_d_datamatricula from matricula where ed60_c_situacao = 'MATRICULADO' and  ed60_i_codigo = reserva.alunoreserva.ed47_i_codigo
        order by ed60_d_datamatricula desc 
        limit 1)  
        from reserva.alunoreserva
        join reserva.auditoriareserva on auditoriareserva.id_alunoreserva = alunoreserva.id_alunoreserva
        left join reserva.escolareserva on	reserva.alunoreserva.id_alunoreserva = reserva.escolareserva.id_alunoreserva
        left join reserva.alunostatusreserva on id = alunostatusreserva_id
        where ed56_i_escola = {$escola['ed56_i_escola']} and ed221_i_serie = {$serie['ed221_i_serie']} $where_status $where_period
        order by
        reserva.alunoreserva.ed47_v_nome";

        
        $stmt = Conn::$conexao->prepare($sql_alunos);
        $stmt->execute();
        $reserva_alunos = $stmt->fetchAll();





        $oPdf->SetFont('arial', '', 6);



        foreach ($reserva_alunos as $aluno) {
            $total_aluno_escola++;
            $total_aluno_geral++;


            $codigo_sge = $aluno['ed47_i_codigo'];
            //caso não tenha o codigo sge tenta encontrar com nome , mae , dt nascimento
            // if ($aluno['ed47_i_codigo'] == ''){

            // //    $codigo_sge = 12334;
            //    $sql_codigo_sge = "
            //    select ed47_i_codigo from aluno 
            //    where ed47_v_nome ilike '%{$aluno['ed47_v_nome']}%' 
            //    and ed47_d_nasc = '{$aluno['data_nascimento']}' 
            //    and  ed47_v_mae ilike '%{$aluno['ed47_v_mae']}%'
            //    order by  ed47_d_cadast desc
            //    limit 1
            //    ";    

            //    $stmt = Conn::$conexao->prepare($sql_codigo_sge);
            //    $stmt->execute();
            //    $codigo_sge = $stmt->fetch(PDO::FETCH_ASSOC);
            //    $codigo_sge = $codigo_sge['ed47_i_codigo'];

            // }

            $oPdf->Cell(10, 5, $codigo_sge, 1, 0, 'C');
            $oPdf->Cell(10, 5,  $aluno['id_alunoreserva'], 1, 0, 'C');
            $oPdf->Cell(55, 5, $aluno['ed47_v_nome'], 1, 0, 'C');
            $oPdf->Cell(15, 5,  $aluno['ed47_d_nasc'], 1, 0, 'C');
            $oPdf->Cell(55, 5,  $aluno['ed47_v_mae'], 1, 0, 'C');
            $oPdf->Cell(55, 5,  $aluno['ed47_c_nomeresp'], 1, 0, 'C');
            $oPdf->Cell(65, 5,  $aluno['status_descr'], 1, 0, 'C');
            if ($aluno['ed60_d_datamatricula'] == ''){
                $dataMatricula = '';
            }
            else{
                $dataMatricula = date("d/m/Y", strtotime($aluno['ed60_d_datamatricula']));
            }    
            $oPdf->Cell(15, 5,  $dataMatricula, 1, 1, 'C');
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
