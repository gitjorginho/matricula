<?php
session_start();
require_once('../../../../../library/fpdf.php');
require_once('../../classe/Conn.php');
header("Content-Type: text/html;  charset=ISO-8859-1", true);
Conn::conect();

$sql_atualiza = 'select reserva.atualizaCodigoGAluno()'; 
$stmt = Conn::$conexao->prepare($sql_atualiza);
$stmt->execute();

$total_aluno =0; 

// todas as escolas reserva.escolareserva
$sql_relatorio ="select "
                ."alunoreserva.id_alunoreserva  as id_portal, " 
                ."alunoreserva.ed47_i_codigo as cod_seg, " 
                ."( "
                ."select ed60_d_datamatricula from matricula "
                ."      where ed60_i_aluno = alunoreserva.ed47_i_codigo and ed60_c_situacao = 'MATRICULADO'"  
                ."      order by ed60_d_datamatricula desc "  
                ."      limit 1"
                .") as data_matricula,"
                ."("
                ."select ed18_c_codigoinep from matricula"
                ."      join turma on ed57_i_codigo = ed60_i_turma "  
                ."      join escola on ed57_i_escola  = ed18_i_codigo "
                ."      where ed60_i_aluno = alunoreserva.ed47_i_codigo and ed60_c_situacao = 'MATRICULADO' "  
                ."      order by ed60_d_datamatricula desc " 
                ."      limit 1"
                .") as cod_inep,"
                ."("
                ."select ed18_c_nome from matricula "
                ."      join turma on ed57_i_codigo = ed60_i_turma"  
                ."      join escola on ed57_i_escola  = ed18_i_codigo"
                ."      where ed60_i_aluno = alunoreserva.ed47_i_codigo and ed60_c_situacao = 'MATRICULADO'"  
                ."      order by ed60_d_datamatricula desc " 
                ."      limit 1 "
                .") as escola,"
                ."("
                ."select trim(ed11_c_descr) from matricula "
                ."      join turma on ed57_i_codigo = ed60_i_turma "  
                ."      join turmaserieregimemat on ed220_i_turma = ed57_i_codigo"
                ."      join serieregimemat on ed220_i_serieregimemat = ed223_i_codigo "
                ."      join serie on ed223_i_serie = ed11_i_codigo "
                ."      where ed60_i_aluno = alunoreserva.ed47_i_codigo and ed60_c_situacao = 'MATRICULADO' "  
                ."      order by ed60_d_datamatricula desc " 
                ."      limit 1"
                .") as serie, "
                ."trim(alunoreserva.ed47_v_nome) as ed47_v_nome, "
                ."alunoreserva.ed47_d_nasc, "
                ."alunoreserva.ed47_v_mae, "
                ."alunoreserva.ed47_c_nomeresp, "
                ."aluno.ed47_d_cadast "
                ."from reserva.alunoreserva " 
                ."join reserva.escolareserva on escolareserva.id_alunoreserva = alunoreserva.id_alunoreserva " 
                ."join escola.escola on ed18_i_codigo = ed56_i_escola "
                ."join aluno ON aluno.ed47_i_codigo = reserva.alunoreserva.ed47_i_codigo "
                . "join matricula M on M.ed60_i_aluno = aluno.ed47_i_codigo "
                . "join turma T on T.ed57_i_codigo = M.ed60_i_turma "
                . "join calendario C on C.ed52_i_codigo = T.ed57_i_calendario "
                ."where ed52_i_ano = '2020' and ed60_c_situacao in ('MATRICULADO') and alunoreserva.alunostatusreserva_id = 8 "
                ."order by (escola, alunoreserva.ed47_v_nome) asc  ";
               
//die($sql_relatorio);

$stmt = Conn::$conexao->prepare($sql_relatorio);
$stmt->execute();
$reserva_alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$formato = $_GET['formato'];


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

$oPdf->Cell(20, 5, "COD INEP",1, 0 , "L", 1);
$oPdf->Cell(260, 5, "UNIDADE ESCOLAR", 1, 1, 'C', 1);

$oPdf->Cell(20, 5, "COD.PORTAL", 1, 0 , "L", 1);
$oPdf->Cell(20, 5, "COD.SGE", 1, 0 , "L", 1);
$oPdf->Cell(80, 5, "NOME", 1, 0 , "L", 1);
$oPdf->Cell(40, 5, "DT. NASCIMENTO", 1, 0 , "L", 1);
$oPdf->Cell(40, 5, "SÉRIE", 1, 0 , "L", 1);
$oPdf->Cell(40, 5, "DT. MATRÍCULA", 1, 0 , "L", 1);
$oPdf->Cell(40, 5, "DT. CADASTRO SGE", 1, 1 , "L", 1);
$oPdf->Cell(100, 5,  "", 1, 0 , "L", 1);
$oPdf->Cell(80, 5,  "NOME MÃE", 1, 0 , "L", 1);
$oPdf->Cell(100, 5,  "NOME DO RESPONSÁVEL",1, 1 , "L", 1);
$oPdf->Cell(280, 5,  "",1, 1 , "L", 0);
$total_reserva_aluno = 0;
$escola='';
foreach ($reserva_alunos as $aluno) {
    if ($escola != $aluno['escola']){
            $oPdf->Cell(20, 5, $aluno['cod_inep'], 1, 0, 'L',1);
            $oPdf->Cell(260, 5, $aluno['escola'],1, 1, 'C', 1);
            $escola =$aluno['escola']; 
        }

    
    $oPdf->Cell(20, 5, $aluno['id_portal'], 1, 0, 'L',1);
    $oPdf->Cell(20, 5, $aluno['cod_seg'], 1, 0, 'L');
    $oPdf->Cell(80, 5, $aluno['ed47_v_nome'], 1, 0, 'L',0);
    $oPdf->Cell(40, 5,  date('d-m-Y', strtotime($aluno['ed47_d_nasc'])), 1, 0, 'L');
    $oPdf->Cell(40, 5, $aluno['serie'], 1, 0, 'L');
    $oPdf->Cell(40, 5, date('d-m-Y', strtotime($aluno['data_matricula'])), 1, 0, 'L');
    $oPdf->Cell(40, 5, date('d-m-Y', strtotime($aluno['ed47_d_cadast'])), 1, 1, 'L');
    $oPdf->Cell(100, 5,  "", 1, 0, 'L');
    $oPdf->Cell(80, 5,  $aluno['ed47_v_mae'], 1, 0, 'L');
    $oPdf->Cell(100, 5,  $aluno['ed47_c_nomeresp'], 1, 1, 'L');
    $total_aluno++;

}


$oPdf->SetFont('arial', 'b', 8);
$oPdf->Ln();
$oPdf->Cell(280, 5, 'Total geral de alunos : ' . $total_aluno, 1, 1, 'L', 1);
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
