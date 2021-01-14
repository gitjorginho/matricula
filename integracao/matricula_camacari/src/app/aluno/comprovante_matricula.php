<?php
require_once('../../library/fpdf.php');
require_once('../classe/Conn.php');
header("Content-Type: text/html;  charset=ISO-8859-1", true);
Conn::conect();

$sql_codigo_escola =
"select ed18_i_codigo from aluno
 inner join matricula on ed60_i_aluno = ed47_i_codigo
 inner join matriculaturnoreferente on ed60_i_codigo = ed337_matricula
 inner join turmaturnoreferente on ed337_turmaturnoreferente = ed336_codigo
 inner join turma on ed57_i_codigo = ed336_turma
 inner join escola on ed57_i_escola = ed18_i_codigo
 where ed47_i_codigo = {$_GET['codigo_aluno']} and ed60_d_datamatricula > '2020-02-01' 
";

$stmt = Conn::$conexao->prepare($sql_codigo_escola);
$stmt->execute();
$codigoEscola = $stmt->fetch(PDO::FETCH_ASSOC);


$sql_escola = "select *,(select trim(j14_nome) from cadastro.ruas where j14_codigo = ed18_i_rua ) as rua, (select trim(j13_descr) from cadastro.bairro where j13_codi = ed18_i_bairro) as bairro from escola where ed18_i_codigo = {$codigoEscola['ed18_i_codigo']} ";
//$sql_escola = "select * from escola where ed18_i_codigo = 118";
$stmt = Conn::$conexao->prepare($sql_escola);
$stmt->execute();
$dadosEscola = $stmt->fetch(PDO::FETCH_ASSOC);

$nome_escola_cab = trim($dadosEscola['ed18_c_nome']);
$inep_escola_cab = trim($dadosEscola['ed18_c_codigoinep']);
$ruaescola_cab = trim($dadosEscola['rua']);
$numescola_cab = trim($dadosEscola['ed18_i_numero']);
$bairroescola_cab = trim($dadosEscola['bairro']);
$cidadeescola_cab = 'CAMACARI';
$estadoescola_cab = 'BA';
$telefoneescola_cab = '';


$conn = Conn::$conexao;



class PDF extends FPDF
{

    function Header()
    {
        global $nome_escola_cab;
        global $inep_escola_cab;
        global $ruaescola_cab;
        global $numescola_cab;
        global $bairroescola_cab;
        global $cidadeescola_cab;
        global $estadoescola_cab;
        global $telefoneescola_cab;
        global  $nome_aluno_cab;
        global $codigo_aluno_cab;
        global $serie_cab;
        global $turno_cab;

        $this->SetXY(1, 100);
        $this->Image('../../img/Cabecalho_pdf.png', 7, 3, 200);
        $this->SetFont('Arial', 'BI', 10);
        $this->Text(33, 9, 'PREFEITURA DE CAMAÇARI');
        $this->Text(33, 14, $nome_escola_cab);
        $this->SetFont('Arial', 'I', 8);
        $this->SetFont('Arial', '', 6);
        $this->Text(33, 18, 'INEP ESCOLA :' . $inep_escola_cab); //.'-'.'INEP:'.$inep_escola
        $this->Text(33, 21, $ruaescola_cab . ',');
        $this->Text(33, 24, $numescola_cab . " - " . $bairroescola_cab);
        $this->Text(33, 26, $cidadeescola_cab . " - " . $estadoescola_cab);
        $this->Text(33, 28, $telefoneescola_cab);
        $this->SetFont('Arial', '', 8);
        $this->Text(135, 13, 'FICHA DO ALUNO');
        $this->Text(135, 17, trim($codigo_aluno_cab).' - '.trim($nome_aluno_cab));
        $this->Text(135, 21, 'SERIE: '.trim($serie_cab).' - TURNO: '.trim($turno_cab));
        $this->SetY(36);
    }


    public function HeaderCentralizado() {

        global $nome_escola_cab;
        global $inep_escola_cab;
        global $ruaescola_cab;
        global $numescola_cab;
        global $bairroescola_cab;
        global $cidadeescola_cab;
        global $estadoescola_cab;
        global $telefoneescola_cab;
        global  $nome_aluno_cab;
        global $codigo_aluno_cab;
        global $serie_cab;
        global $turno_cab;

        $this->SetXY(1, 1);
        $this->Image('../../img/Cabecalho_pdf.png', 7, 3, 200);
        $this->SetFont('Arial', 'BI', 10);
        $this->Text(33, 9, 'PREFEITURA DE CAMAÇARI');
        $this->Text(33, 14, $nome_escola_cab);
        $this->SetFont('Arial', 'I', 8);
        $this->SetFont('Arial', '', 6);
        $this->Text(33, 18, 'INEP ESCOLA :' . $inep_escola_cab); //.'-'.'INEP:'.$inep_escola
        $this->Text(33, 21, $ruaescola_cab . ',');
        $this->Text(33, 24, $numescola_cab . " - " . $bairroescola_cab);
        $this->Text(33, 26, $cidadeescola_cab . " - " . $estadoescola_cab);
        $this->Text(33, 28, $telefoneescola_cab);
        $this->SetFont('Arial', '', 8);
        $this->Text(135, 13, 'FICHA DO ALUNO');
        $this->Text(135, 17, trim($codigo_aluno_cab).' - '.trim($nome_aluno_cab));
        $this->Text(135, 21, 'SERIE: '.trim($serie_cab).' - TURNO: '.trim($turno_cab));
        $this->SetY(36);


    }


//Page footer
//    function Footer() {
////#00#//footer
////#10#//Este método é usado para criar o rodapé da página. Ele é automaticamente chamado por |addPage|
////#10#//e |close| e não deve ser chamado diretamente pela aplicação. A  implementação  em  FPDF  está
////#10#//vazia, então você  deve  criar  uma  subclasse  e  sobrepor  o  método  se  você  quiser   um
////#10#//processamento específico.
////#15#//footer()
////#99#//Exemplo:
////#99#//class PDF extends FPDF
////#99#//{
////#99#//  function Footer()
////#99#//  {
////#99#//    Vai para 1.5 cm da borda inferior
////#99#//      $this->SetY(-15);
////#99#//    Seleciona Arial itálico 8
////#99#//      $this->SetFont('Arial','I',8);
////#99#//    Imprime o número da página centralizado
////#99#//      $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
////#99#//  }
////#99#//}
//
//        global $url;
//        if($this->imprime_rodape == true) {
//            //Position at 1.5 cm from bottom
//            $this->SetFont('Arial','',5);
//            $this->text(10,$this->h-8,'Base: '.db_base_ativa());
//            $this->SetFont('Arial','I',6);
//            $this->SetY(-10);
//            $nome_h = @$GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"];
//            $nome_h = substr($nome_h,strrpos($nome_h,"/")+1);
//            $result_nomeusu = db_query("select nome as nomeusu from db_usuarios where id_usuario =".db_getsession("DB_id_usuario"));
//            if (pg_numrows($result_nomeusu)>0){
//                $nomeusu = pg_result($result_nomeusu,0,0);
//            }
//            if (isset($nomeusu)&&$nomeusu!=""){
//                $emissor = $nomeusu;
//            }else{
//                $emissor = @$GLOBALS["DB_login"];
//            }
//            $this->Cell(0,10,$nome_h.'     Emissor: '.substr(ucwords(strtolower($emissor)),0,30).'     Exercício: '.db_getsession("DB_anousu").'    Data: '.date("d-m-Y",db_getsession("DB_datausu"))." - ".date("H:i:s"),"T",0,'C');
//            $this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,1,'R');
//        }
//
//    }

// mudar o angulo do texto
//    function TextWithDirection($x,$y,$txt,$direction='R')
//    {
//        $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
//        if ($direction=='R')
//            $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$txt);
//        elseif ($direction=='L')
//            $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$txt);
//        elseif ($direction=='U')
//            $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
//        elseif ($direction=='D')
//            $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
//        else
//            $s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$txt);
//        $this->_out($s);
//    }

// rotacionar o texto

//    function TextWithRotation($x,$y,$txt,$txt_angle,$font_angle=0)
//    {
//        $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
//
//        $font_angle+=90+$txt_angle;
//        $txt_angle*=M_PI/180;
//        $font_angle*=M_PI/180;
//
//        $txt_dx=cos($txt_angle);
//        $txt_dy=sin($txt_angle);
//        $font_dx=cos($font_angle);
//        $font_dy=sin($font_angle);
//
//        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
//            $txt_dx,$txt_dy,$font_dx,$font_dy,
//            $x*$this->k,($this->h-$y)*$this->k,$txt);
//        $this->_out($s);
//
//
//    }

}



$nome_escola = 'Escoal portal da matricula';
$codAluno = $_GET['codigo_aluno'];//'49852';//



$sqlAluno = "SELECT
	  ed18_c_codigoinep,ed18_i_codigo,ed60_i_codigo AS matricula,ed47_v_nome AS nome, ed47_v_mae AS nomemae, ed18_c_nome AS escola,
	  ed57_c_descr AS turma,ed47_d_nasc as dataNascimento, ed60_d_datamatricula as dataMatricula,
	  ed11_c_descr AS serie, ed15_c_nome AS turno,ed47_c_nomeresp AS responsavel,ed47_v_pai AS nomepai,
	  ed47_v_ender AS endereco, ed47_v_compl AS complemento, ed47_v_bairro as bairro,ed47_c_numero AS numero,
      ed47_i_transpublico AS transporte
FROM aluno
LEFT JOIN alunocurso on ed56_i_aluno = ed47_i_codigo
LEFT JOIN alunopossib on ed79_i_alunocurso = ed56_i_codigo
INNER JOIN matriculareserva on reserva_aluno = ed47_i_codigo
LEFT JOIN matricula ON ed47_i_codigo = ed60_i_aluno
LEFT JOIN turma ON matricula.ed60_i_turma = turma.ed57_i_codigo
LEFT JOIN escola ON escola.ed18_i_codigo = turma.ed57_i_escola
    --INNER JOIN matriculaserie ON matricula.ed60_i_codigo = matriculaserie.ed221_i_matricula
LEFT JOIN serie ON serie.ed11_i_codigo = reserva_turma
LEFT JOIN turno ON turno.ed15_i_codigo = turma.ed57_i_turno
WHERE ed47_i_codigo = '{$codAluno}' and ed60_c_situacao = 'MATRICULADO' and ed60_d_datamatricula > '2020-01-01' limit 1;";


//$sqlAluno = "
//SELECT
//	  ed18_c_codigoinep,ed18_i_codigo,ed60_i_codigo AS matricula,ed47_v_nome AS nome, ed47_v_mae AS nomemae, ed18_c_nome AS escola,
//	  ed57_c_descr AS turma,ed47_d_nasc as dataNascimento, ed60_d_datamatricula as dataMatricula,
//	  ed11_c_descr AS serie, ed15_c_nome AS turno,ed47_c_nomeresp AS responsavel,ed47_v_pai AS nomepai,
//	  ed47_v_ender AS endereco, ed47_v_compl AS complemento, ed47_v_bairro as bairro,ed47_c_numero AS numero,
//      ed47_i_transpublico AS transporte
//FROM aluno
//INNER JOIN alunocurso on ed56_i_aluno = ed47_i_codigo
//INNER JOIN alunopossib on ed79_i_alunocurso = ed56_i_codigo
//INNER JOIN matricula ON ed47_i_codigo = ed60_i_aluno
//INNER JOIN turma ON matricula.ed60_i_turma = turma.ed57_i_codigo
//INNER JOIN escola ON escola.ed18_i_codigo = turma.ed57_i_escola
//--INNER JOIN matriculaserie ON matricula.ed60_i_codigo = matriculaserie.ed221_i_matricula
//INNER JOIN serie ON serie.ed11_i_codigo = alunopossib.ed79_i_serie
//INNER JOIN turno ON turno.ed15_i_codigo = turma.ed57_i_turno
//WHERE ed47_i_codigo = '{$codAluno}' and ed60_c_situacao = 'MATRICULADO' and ed60_d_datamatricula > '2020-02-05' limit 1;";


$stmt = Conn::$conexao->prepare($sqlAluno);
$stmt->execute();
$dadosAluno = $stmt->fetch(PDO::FETCH_ASSOC);

//$nome_aluno_cab = strtoupper($dadosAluno['nome']);
$codigo_aluno_cab = strtoupper($codAluno );
$serie_cab = strtoupper($dadosAluno['serie']);
$turno_cab = strtoupper($dadosAluno['turno']);
//$result = db_query($sqlAluno);
//$dadosAluno = pg_fetch_assoc($result);

$sql_ficha_aluno = "SELECT aluno.*,far.*, censoufident.ed260_c_nome AS ufident, censoufnat.ed260_c_nome AS ufnat,
censoufcert.ed260_c_nome AS ufcert, censoufend.ed260_c_nome AS ufend, censomunicnat.ed261_c_nome AS municnat, 
censomuniccert.ed261_c_nome AS municcert, censomunicend.ed261_c_nome AS municend, censoorgemissrg.ed132_c_descr AS orgemissrg, 
pais.ed228_c_descr, sd100_tipo
FROM aluno
INNER JOIN pais ON pais.ed228_i_codigo = aluno.ed47_i_pais
LEFT JOIN censouf AS censoufident ON censoufident.ed260_i_codigo = aluno.ed47_i_censoufident
LEFT JOIN censouf AS censoufnat ON censoufnat.ed260_i_codigo = aluno.ed47_i_censoufnat
LEFT JOIN censouf AS censoufcert ON censoufcert.ed260_i_codigo = aluno.ed47_i_censoufcert
LEFT JOIN censouf AS censoufend ON censoufend.ed260_i_codigo = aluno.ed47_i_censoufend
LEFT JOIN censomunic AS censomunicnat ON censomunicnat.ed261_i_codigo = aluno.ed47_i_censomunicnat
LEFT JOIN censomunic AS censomuniccert ON censomuniccert.ed261_i_codigo = aluno.ed47_i_censomuniccert
LEFT JOIN censomunic AS censomunicend ON censomunicend.ed261_i_codigo = aluno.ed47_i_censomunicend
LEFT JOIN censoorgemissrg ON censoorgemissrg.ed132_i_codigo = aluno.ed47_i_censoorgemissrg
LEFT JOIN censocartorio ON censocartorio.ed291_i_codigo = aluno.ed47_i_censocartorio
LEFT JOIN tiposanguineo AS d ON d.sd100_sequencial = aluno.ed47_tiposanguineo
LEFT JOIN fardamento AS far ON far.fard_i_codaluno = aluno.ed47_i_codigo
LEFT JOIN alunoruasbairrocep ON alunoruasbairrocep.j76_i_aluno = aluno.ed47_i_codigo
LEFT JOIN ruasbairrocep ON ruasbairrocep.j32_i_codigo = alunoruasbairrocep.j76_i_ruasbairrocep
LEFT JOIN ruasbairro ON ruasbairro.j16_codigo = ruasbairrocep.j32_ruasbairro
LEFT JOIN ruas ON ruas.j14_codigo = ruasbairro.j16_lograd
LEFT JOIN bairro ON bairro.j13_codi = ruasbairro.j16_bairro
WHERE ed47_i_codigo IN ({$codAluno})
ORDER BY ed47_v_nome limit 1";

//$result = db_query($sql_ficha_aluno);
//$dados_ficha_aluno =  pg_fetch_assoc($result);

$stmt = Conn::$conexao->prepare($sql_ficha_aluno);
$stmt->execute();
$dados_ficha_aluno = $stmt->fetch(PDO::FETCH_ASSOC);

$nome_aluno_cab = $dados_ficha_aluno['ed47_v_nome'];


$head1 = "FICHA DO ALUNO";
$head2 = trim("{$dados_ficha_aluno['ed47_i_codigo']} - {$dados_ficha_aluno['ed47_v_nome']}");
$nome_turma_exibida = explode(' ', str_replace('Ã', 'A', trim($dadosAluno['turno'])));

switch (strtoupper($nome_turma_exibida[0])) {
    case 'TARDE':
        $turno_exibido = 'VESPERTINO';
        break;
    case 'MANHA':
        $turno_exibido = 'MATUTINO';
        break;
    case 'NOITE':
        $turno_exibido = 'NOTURNO';
        break;
    default:
        $turno_exibido = '';
        break;
}

$head3 = $serieTurmaTurno = trim($dadosAluno['serie']) . ' / ' . trim($dadosAluno['turma']) . ' Turno: ' . $turno_exibido;;
$iEscola = $dadosAluno['ed18_i_codigo'];

$oPdf = new PDF();
$oPdf->AliasNbPages();
//$oPdf->Open();
$oPdf->setfillcolor(223);
$oPdf->SetAutoPageBreak(false, 20);
$oPdf->addPage();

/** DADOS PESSOAIS */
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(160, 4, "   DADOS PESSOAIS", "LBT", 0, "L", 1);

$oPdf->Cell(34, 4, "   FOTO", 1, 1, "L", 1);
$oPdf->Cell(160, 2, "", "LR", 0, "C", 0);
$oPdf->Cell(34, 2, "", "LR", 1, "C", 0);
//
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(35, 4, strip_tags('Nome:'), 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(120, 4, $dados_ficha_aluno['ed47_v_nome'], 0, 0, "L", 0);
$oPdf->Cell(2, 4, "", "R", 1, "C", 0);
//
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->Setfont('arial', '', 7);
$oPdf->Cell(35, 4, 'Codigo :', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(27, 4, $dados_ficha_aluno['ed47_i_codigo'], 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Codigo Inep:', 0, 0, "R", 0);
$oPdf->Setfont('arial', 'b', 7);
$oPdf->Cell(20, 4, $dados_ficha_aluno['ed47_c_codigoinep'], 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(18, 4, 'Nº NIS', 0, 0, "R", 0);
$oPdf->Setfont('arial', 'b', 7);
$oPdf->Cell(25, 4, $dados_ficha_aluno['ed47_c_nis'], 0, 0, "L", 0);
$oPdf->Cell(2, 4, "", "R", 1, "C", 0);


$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->Setfont('arial', '', 7);
$oPdf->Cell(35, 4, 'Data Nascimento:', 0, 0, "L", 0);
$oPdf->Setfont('arial', 'b', 7);
$oPdf->Cell(20, 4, dateToView($dados_ficha_aluno['ed47_d_nasc']), 0, 0, "L", 0);//db_formatar($dados_ficha_aluno['ed47_d_nasc'], 'd')
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Sexo:', 0, 0, "R", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(20, 4, $dados_ficha_aluno['ed47_v_sexo'] == "M" ? "MASCULINO" : "FEMININO", 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(25, 4, 'Estado Civil:', 0, 0, "R", 0);

$estado_civil = $dados_ficha_aluno['ed47_i_estciv'];
switch ($estado_civil) {
    case 1:
        $estado_civil = 'Solteiro';
        break;
    case 2:
        $estado_civil = 'Casado';
        break;
    case 3:
        $estado_civil = 'Viúvo';
        break;
    case 4:
        $estado_civil = 'Divorciado';
        break;
    default :
        $estado_civil = '-';
        break;

}


$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(25, 4, $estado_civil, 0, 0, "L", 0);
$oPdf->Cell(2, 4, "", "R", 1, "C", 0);
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(35, 4, 'Tipo Sanguineo:', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(65, 4, $dados_ficha_aluno['sd100_tipo'] == "" ? "-" : $dados_ficha_aluno['sd100_tipo'], 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Raça:', 0, 0, "R", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(25, 4, $dados_ficha_aluno['ed47_c_raca'], 0, 0, "L", 0);
$oPdf->Cell(2, 4, "", "R", 1, "C", 0);
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(35, 4, 'Filiaçao:', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(120, 4, $dados_ficha_aluno['ed47_i_filiacao'] == "0" ? "NÃO DECLARADO / IGNORADO" : "PAI E/OU MÃE", 0, 0, "L", 0);
$oPdf->Cell(2, 4, "", "R", 1, "C", 0);
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);

$oPdf->Cell(35, 4, 'Filiação1 :', 0, 0, "L", 0);
$oPdf->Setfont('arial', 'b', 7);
$oPdf->Cell(120, 4, $dados_ficha_aluno['ed47_v_mae'], 0, 0, "L", 0);
$oPdf->Cell(2, 4, "", "R", 1, "C", 0);
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(35, 4, 'Filiação2 :', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(120, 4, $dados_ficha_aluno['ed47_v_pai'], 0, 0, "L", 0);
$oPdf->Cell(2, 4, "", "R", 1, "C", 0);

$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(35, 4, 'Nome Responsavel :', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(120, 4, $dados_ficha_aluno['ed47_c_nomeresp'], 0, 0, "L", 0);
$oPdf->Cell(2, 4, "", "R", 1, "C", 0);

$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(35, 4, 'Email do Responsavel:', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(122, 4, $dados_ficha_aluno['ed47_c_emailresp'], 'R', 0, "L", 0);
$oPdf->Ln();

$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->Cell(35, 4, 'Celular Responsavel:', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 12);
$oPdf->Cell(120, 4, $dados_ficha_aluno['ed47_celularresponsavel'], 0, 0, "L", 0);
$oPdf->Cell(2, 4, "", "R", 1, "C", 0);
$oPdf->Line(204, 35, 204, 80);

$oPdf->Cell(160, 2, "", "LR", 0, "C", 0);
$oPdf->Cell(34, 2, "", "LR", 1, "C", 0);
$oPdf->Cell(194, 4, "ENDEREÇO / CONTATOS", 1, 1, "L", 1);
$oPdf->Cell(194, 2, "", "LR", 1, "C", 0);

$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(20, 4, 'Endereço:', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(50, 4, substr($dados_ficha_aluno['ed47_v_ender'], 0, 37), 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(20, 4, 'Numero:', 0, 0, "R", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(101, 4, $dados_ficha_aluno['ed47_c_numero'], 'R', 1, "L", 0);
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(20, 4, 'Complemento:', 0, 0, '', 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(171, 4, $dados_ficha_aluno['ed47_v_compl'], 'R', 1, 'L', 0);
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(20, 4, 'UF:', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(40, 4, $dados_ficha_aluno['ufend'], 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Cidade:', 0, 0, "R", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(20, 4, $dados_ficha_aluno['municend'], 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(25, 4, 'Bairro:', 0, 0, "R", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(55, 4, substr($dados_ficha_aluno['ed47_v_bairro'], 0, 23), 0, 0, "L", 0);
$oPdf->Cell(1, 4, "", "R", 1, "C", 0);

$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(20, 4, 'Zona ', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(40, 4, $dados_ficha_aluno['ed47_c_zona'], 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Cep:', 0, 0, "R", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(100, 4, $dados_ficha_aluno['ed47_v_cep'], 0, 0, "L", 0);
$oPdf->Cell(1, 4, "", "R", 1, "C", 0);
$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(20, 4, 'Telefone :', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(40, 4, $dados_ficha_aluno['ed47_v_telef'] . ' ' . $dados_ficha_aluno['ed47_telefonecontato'], 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Cel :', 0, 0, "R", 0);
$oPdf->Setfont('arial', 'b', 7);
$oPdf->Cell(20, 4, $dados_ficha_aluno['ed47_v_telcel'], 0, 0, "L", 0);
$oPdf->Setfont('arial', '', 7);
$oPdf->Cell(24, 4, 'Email:', 0, 0, "R", 0);
$oPdf->Setfont('arial', 'b', 7);
$oPdf->Cell(56, 4, $dados_ficha_aluno['ed47_v_email'], 0, 0, "L", 0);
$oPdf->Cell(1, 4, "", "R", 1, "C", 0);

$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, '', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(40, 4, '', 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, '', 0, 0, "R", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(90, 4, '', 0, 0, "L", 0);
$oPdf->Cell(1, 4, "", "R", 1, "C", 0);

$oPdf->Cell(194, 2, "", "LR", 1, "C", 0);
$oPdf->Cell(194, 4, "FARDAMENTO", 1, 1, "L", 1);
$oPdf->Cell(194, 2, "", "LR", 1, "C", 0);


$fard_i_camisa = "-";
$fard_i_short = "-";

if ($dados_ficha_aluno['fard_i_sapato'] == 0 || $dados_ficha_aluno['fard_i_sapato'] == '') {
    $fard_i_sapato = "-";
} else {
    $fard_i_sapato = $dados_ficha_aluno['fard_i_sapato'];
}


if ($dados_ficha_aluno['fard_i_camisa'] < 7 || $dados_ficha_aluno['fard_i_camisa'] == '') {
    $fard_i_camisa = $dados_ficha_aluno['fard_i_camisa'] * 2;
} else if ($dados_ficha_aluno['fard_i_camisa'] == 7) {
    $fard_i_camisa = "P";
} else if ($dados_ficha_aluno['fard_i_camisa'] == 8) {
    $fard_i_camisa = "M";
} else if ($dados_ficha_aluno['fard_i_camisa'] == 9) {
    $fard_i_camisa = "G";
} else if ($dados_ficha_aluno['fard_i_camisa'] == 10) {
    $fard_i_camisa = "GG";
}


if ($dados_ficha_aluno['fard_i_short'] < 7 || $dados_ficha_aluno['fard_i_short'] == '') {
    $fard_i_short = $dados_ficha_aluno['fard_i_short'] * 2;
} else if ($dados_ficha_aluno['fard_i_short'] == 7) {
    $fard_i_short = "P";
} else if ($dados_ficha_aluno['fard_i_short'] == 8) {
    $fard_i_short = "M";
} else if ($dados_ficha_aluno['fard_i_short'] == 9) {
    $fard_i_short = "G";
} else if ($dados_ficha_aluno['fard_i_short'] == 10) {
    $fard_i_short = "GG";
}


$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(15, 4, 'Camisa:', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(56, 4, "Tamanho " . $fard_i_camisa, 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(15, 4, 'Short/Saia:', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(45, 4, "Tamanho " . $fard_i_short, 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(10, 4, 'Sapato:', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(49, 4, "Tamanho " . $fard_i_sapato, 0, 0, "L", 0);
$oPdf->Cell(1, 4, "", "R", 1, "C", 0);

$oPdf->Cell(194, 2, "", "LR", 1, "C", 0);
$oPdf->Cell(194, 4, "OUTRAS INFORMAÇÕES", 1, 1, "L", 1);
$oPdf->Cell(194, 2, "", "LR", 1, "C", 0);

$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Nacionalidade', 0, 0, "L", 0);


switch ($dados_ficha_aluno['ed47_i_nacion']){
    case 1:
        $ed47_i_nacion = "BRASILEIRA";
        break;
    case 2:
        $ed47_i_nacion = "BRASILEIRA NO EXTERIOR OU NATURALIZADO";
        break;
    case 3:
        $ed47_i_nacion = "ESTRANGEIRA";
        break;
    default:
        $ed47_i_nacion = "";
        break;
}




$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(40, 4, $ed47_i_nacion, 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'País', 0, 0, "R", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(90, 4, $dados_ficha_aluno['ed228_c_descr'], 0, 0, "L", 0);
$oPdf->Cell(1, 4, "", "R", 1, "C", 0);

$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'UF Nascimento', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(40, 4, $dados_ficha_aluno['ufnat'], 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Municipio Nascimento', 0, 0, "R", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(90, 4, $dados_ficha_aluno['municnat'], 0, 0, "L", 0);
$oPdf->Cell(1, 4, "", "R", 1, "C", 0);

$oPdf->Cell(3, 4, "", "L", 0, "C", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Transporte Publico', 0, 0, "L", 0);
$oPdf->SetFont('arial', 'b', 7);
$oPdf->Cell(40, 4, $dados_ficha_aluno['ed47_i_transpublico'] == "0" ? "NÃO UTILIZA" : "UTILIZA", 0, 0, "L", 0);
$oPdf->SetFont('arial', '', 7);
$oPdf->Cell(30, 4, 'Transporte', 0, 0, "R", 0);

switch ($dados_ficha_aluno['ed47_c_transporte']) {
    case 1 :
        $ed47_c_transporte = "ESTADUAL";
        break;
    case 2 :
        $ed47_c_transporte = "MUNICIPAL";
        break;

    default :
        $ed47_c_transporte = "";
        break;
}


$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(90, 4, $ed47_c_transporte, 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Bolsa Familia', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(50, 4, $dados_ficha_aluno['ed47_c_bolsafamilia'] == 'S' ? 'SIM' : 'NÃO', 0, 0, "L", 0);

$oPdf->setfont('arial', '', 7);
$oPdf->cell(18, 4, 'Cartão do SUS', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(32, 4, $dados_ficha_aluno['ed47_cartaosus'], 0, 0, "L", 0);


$oPdf->setfont('arial', '', 7);
$oPdf->cell(25, 4, '', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(35, 4, '', 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Local de Procedencia', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(160, 4, substr('-', 0, 30), 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);


$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Data de Procedencia', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(22, 4, '-', 0, 0, "L");//db_formatar($ed76_d_data, 'd')
$oPdf->cell(1, 4, "", 0, 0, "C", 0);

$oPdf->setfont('arial', '', 7);
$oPdf->cell(47, 4, 'Recebe Escolarização em Outro Espaço :', 0, 0, "L", 0);

switch (trim($dados_ficha_aluno['ed47_c_atenddifer'])) {
    case'1':
        $ed47_c_atenddifer = "EM HOSPITAL";
        break;
    case'2':
        $ed47_c_atenddifer = "EM DOMICÍLIO";
        break;
    case'3':
        $ed47_c_atenddifer = "NÃO RECEBE";
        break;
    default:
        $ed47_c_atenddifer = "";
        break;
}

$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(30, 4, $ed47_c_atenddifer, 0, 0, "L", 0);

$oPdf->cell(61, 4, "", "R", 1, "C", 0);

//////////////////////////////////////////////////////////

$oPdf->cell(194, 2, "", "LR", 1, "C", 0);
$oPdf->cell(194, 4, " DOCUMENTOS", 1, 1, "L", 1);
$oPdf->cell(194, 2, "", "LR", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Tipo de Certidão', 0, 0, "L", 0);

if (trim($dados_ficha_aluno['ed47_c_certidaotipo']) == "N") {
    $ed47_c_certidaotipo = "NASCIMENTO";
} else if ($dados_ficha_aluno['ed47_c_certidaotipo'] == "C") {
    $ed47_c_certidaotipo = "CASAMENTO";
} else {
    $ed47_c_certidaotipo = "";
}


$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(40, 4, $ed47_c_certidaotipo, 0, 0, "L", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Numero de Certidão', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(90, 4, $dados_ficha_aluno['ed47_c_certidaonum'], 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);


$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Folha :', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(40, 4, $dados_ficha_aluno['ed47_c_certidaofolha'], 0, 0, "L", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Livro', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(30, 4, $dados_ficha_aluno['ed47_c_certidaolivro'], 0, 0, "L", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(25, 4, 'Data de Emissão:', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(35, 4, dateToView($dados_ficha_aluno['ed47_c_certidaodata']), 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);
$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'UF Cartório', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(40, 4, $dados_ficha_aluno['ufcert'], 0, 0, "L", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Municipio', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(90, 4, $dados_ficha_aluno['municcert'], 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Cartorio:', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(160, 4, substr($dados_ficha_aluno['ed47_c_certidaocart'], 0, 90), 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->cell(188, 0.5, "", 1, 0, "C", 1);
$oPdf->cell(3, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, ' Nº Identidade:', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(40, 4, $dados_ficha_aluno['ed47_v_ident'], 0, 0, "L", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Complemento:', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(30, 4, $dados_ficha_aluno['ed47_v_identcompl'], 0, 0, "L", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(25, 4, 'UF de Identidade:', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(35, 4, $dados_ficha_aluno['ufident'], 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Orgão Emissor', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(100, 4, $dados_ficha_aluno['orgemissrg'], 0, 0, "L", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(25, 4, 'Data de Expedição', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(35, 4, dateToView($dados_ficha_aluno['ed47_d_identdtexp']), 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);


$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->cell(188, 0.5, "", 1, 0, "C", 1);
$oPdf->cell(3, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Nº CNH', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(40, 4, $dados_ficha_aluno['ed47_v_cnh'], 0, 0, "L", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Categoria CNH:', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(90, 4, $dados_ficha_aluno['ed47_v_categoria'], 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Emissao CNH:', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(40, 4, $dados_ficha_aluno['ed47_d_dtemissao'], 0, 0, "L", 0);//db_formatar($dados_ficha_aluno['ed47_d_dtemissao'], 'd')
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, '1º CNH', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(30, 4, dateToView($dados_ficha_aluno['ed47_d_dthabilitacao']), 0, 0, "L", 0);//db_formatar($dados_ficha_aluno['ed47_d_dthabilitacao'], 'd')
$oPdf->setfont('arial', '', 7);
$oPdf->cell(25, 4, 'Vencimento CNH:', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(35, 4, dateToView($dados_ficha_aluno['ed47_d_dtvencimento']), 0, 0, "L", 0);//db_formatar($dados_ficha_aluno['ed47_d_dtvencimento'] , 'd')
$oPdf->cell(1, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->cell(188, 0.5, "", 1, 0, "C", 1);
$oPdf->cell(3, 4, "", "R", 1, "C", 0);

$oPdf->cell(3, 4, "", "L", 0, "C", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'CPF:', 0, 0, "L", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(40, 4, $dados_ficha_aluno['ed47_v_cpf'], 0, 0, "L", 0);
$oPdf->setfont('arial', '', 7);
$oPdf->cell(30, 4, 'Nº Passaporte', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(35, 4, $dados_ficha_aluno['ed47_c_passaporte'], 0, 0, "L", 0);

$oPdf->setfont('arial', '', 7);
$oPdf->cell(20, 4, 'Cartão do SUS', 0, 0, "R", 0);
$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(35, 4, $dados_ficha_aluno['ed47_cartaosus'], 0, 0, "L", 0);
$oPdf->cell(1, 4, "", "R", 1, "C", 0);

$oPdf->cell(194, 2, "", "LR", 1, "C", 0);
$oPdf->cell(194, 4, "   NECESSIDADES ESPECIAIS", 1, 1, "L", 1);
$oPdf->cell(194, 2, "", "LR", 1, "C", 0);


$sSql22 = "select * from alunonecessidade left join escola on escola.ed18_i_codigo = alunonecessidade.ed214_i_escola inner join necessidade on necessidade.ed48_i_codigo = alunonecessidade.ed214_i_necessidade inner join aluno on aluno.ed47_i_codigo = alunonecessidade.ed214_i_aluno left join bairro on bairro.j13_codi = escola.ed18_i_bairro left join ruas on ruas.j14_codigo = escola.ed18_i_rua left join db_depart on db_depart.coddepto = escola.ed18_i_codigo left join pais on pais.ed228_i_codigo = aluno.ed47_i_pais left join planosaude on planosaude.plano_i_codigo = alunonecessidade.ed214_i_planosaude left join acessibilidade on acessibilidade.acesdade_i_codigo = alunonecessidade.ed214_i_acessibilidade where ed214_i_aluno = {$dados_ficha_aluno['ed47_i_codigo']} order by ed48_c_descr LIMIT 2";

$stmt = Conn::$conexao->prepare($sSql22);
$stmt->execute();
$rsResult22 = $stmt->fetchAll();

//$oDaoAlunonecessidade->sql_query("", "*", "ed48_c_descr LIMIT 2", " ed214_i_aluno = {$dados_ficha_aluno['ed47_i_codigo']} ");
//$rsResult22 = db_query($sSql22);

//if( !is_resource( $rsResult22 ) ) {
//    $oErro->sErro = pg_last_error();
//    throw new DBException( _M( MENSAGENS_EDU2_FICHAALUNO001 . 'erro_buscar_necessidade_aluno', $oErro ) );
//}

$iCont = 0;
$iLinhasNecessidades = count($rsResult22);//pg_num_rows( $rsResult22 );


//if ($iLinhasNecessidades > 0) {
//
//    $oPdf->cell(3, 4, "", "L", 0, "C", 0);
//    $oPdf->setfont('arial', 'b', 7);
//    $oPdf->cell(70, 4, "Descrição:", 0, 0, "L", 0);
//    $oPdf->cell(120, 4, "Necessidade Maior:", 0, 0, "L", 0);
//    $oPdf->cell(1, 4, "", "R", 1, "C", 0);
//    for ($iCont = 0; $iCont < $iLinhasNecessidades; $iCont++) {
//
//        db_fieldsmemory($rsResult22, $iCont);
//        $oPdf->cell(3, 4, "", "L", 0, "C", 0);
//        $oPdf->setfont('arial', '', 7);
//        $oPdf->cell(70, 4, '$ed48_c_descr', 0, 0, "L", 0);
//        $oPdf->cell(120, 4, '$ed214_c_principal', 0, 0, "L", 0);
//        $oPdf->cell(1, 4, "", "R", 1, "C", 0);
//        $iCont++;
//    }
//} else {

    $oPdf->cell(3, 4, "", "L", 0, "C", 0);
    $oPdf->setfont('arial', '', 7);
    $oPdf->cell(190, 4, "Nenhum registro.", 0, 0, "L", 0);
    $oPdf->cell(1, 4, "", "R", 1, "C", 0);
    $iCont++;
//}

for ($iFor = $iCont; $iFor < 2; $iFor++) {

    $oPdf->cell(3, 4, "", "L", 0, "C", 0);
    $oPdf->cell(190, 4, "", 0, 0, "C", 0);
    $oPdf->cell(1, 4, "", "R", 1, "C", 0);
}


$oPdf->setfont('arial', 'b', 7);
$oPdf->cell(194, 2, "", "LR", 1, "C", 0);
$oPdf->cell(97, 4, "   OBSERVAÇÔES", 1, 0, "L", 1);
$oPdf->cell(97, 4, "   CONTATO", 1, 1, "L", 1);
$oPdf->cell(97, 2, "", "LR", 0, "C", 0);
$oPdf->cell(97, 2, "", "LR", 1, "C", 0);


$alt_obs = 200;//$oPdf->getY();
$oPdf->setfont('arial', '', 7);
$oPdf->cell(3, 18, "", "L", 0, "C", 0);
$oPdf->multicell(91, 4, trim($dados_ficha_aluno['ed47_t_obs']) == "" ? "Nenhum registro." : substr(trim($dados_ficha_aluno['ed47_t_obs']), 0, 600), 0, "J", 0, 0);
$oPdf->setXY(104, 230);
$oPdf->cell(3, 20, "", "R", 0, "C", 0);

$oPdf->setXY(107, 237);
$oPdf->cell(3, 18, "", "L", 0, "C", 0);
$oPdf->multicell(91, 4, trim($dados_ficha_aluno['ed47_v_contato']) == "" ? "Nenhum registro." : substr(trim($dados_ficha_aluno['ed47_v_contato']), 0, 600), 0, "J", 0, 0);
$oPdf->setXY(201, 200);
$oPdf->cell(3, 50, "", "R", 1, "C", 0);

$oPdf->cell(97, 2, "", "LBR", 0, "C", 0);
$oPdf->cell(97, 2, "", "LBR", 1, "C", 0);
$oPdf->cell(194, 2, "", 0, 1, "C", 0);


$oPdf->cell(60, 4, "Recebi e estou ciente das regras da escola.", 0, 0, "L", 0);
$oPdf->cell(60, 4, "Assinatura do Responsável:", 0, 0, "R", 0);
$oPdf->cell(74, 4, ".............................................................................." .
    "........................", 0, 1, "L", 0);
$oPdf->cell(120, 4, "", 0, 0, "L", 0);
$oPdf->cell(74, 4, trim($dados_ficha_aluno['ed47_c_nomeresp']) != "" ? trim($dados_ficha_aluno['ed47_c_nomeresp']) : "", 0, 1, "C", 0);


//////////header
//Position at 1.5 cm from bottom
$oPdf->SetFont('Arial', '', 5);
//$oPdf->text(10,$oPdf->h-8,'Base: ');//.db_base_ativa() TAVA
$oPdf->SetFont('Arial', 'I', 6);
$oPdf->SetY(-10);
$nome = @$GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"];
$nome = substr($nome, strrpos($nome, "/") + 1);


//  $result_nomeusu = db_query("select nome as nomeusu from db_usuarios where id_usuario =".db_getsession("DB_id_usuario")); TAVA
//     if (pg_numrows($result_nomeusu)>0){
//         $nomeusu = pg_result($result_nomeusu,0,0); TAVA
//     }
//if (isset($nomeusu) && $nomeusu != "") {
//    $emissor = $nomeusu;
//} else {
//    $emissor = @$GLOBALS["DB_login"];
//}
$oPdf->Cell(0, 10, 'comprovante_matricula.php         Emissor: '.$nome_escola_cab.'                     Exercício: '.date('Y',strtotime('today')).'        Data: '.date("d-m-Y     H:i:s",strtotime('now')).'      Página ' . $oPdf->PageNo().' de {nb}', "T", 0, 'C');
// //db_getsession("DB_datausu")
//$oPdf->Cell(0,10,'$nome'.'     Emissor: '.substr(strtolower('$emissor')),0,30).'     Exercício: '.'db_getsession("DB_anousu")'.'    Data: '.date("d-m-Y",'')." - ".date("H:i:s"),"T",0,'C'); //db_getsession("DB_datausu")
//$oPdf->Cell(0, 10, 'Página ' . $oPdf->PageNo() . ' de {nb}', 0, 1, 'R');


$oPdf->imprime_rodape = false;
$oPdf->addPage();


//Comprovante de matricula
$matricula = $dadosAluno['matricula'];
$nome = strtoupper(trim($dadosAluno['nome']));
$escola = strtoupper($dadosAluno['escola']);

$nome_turma_exibida = explode(' ', str_replace('Ã', 'A', trim($dadosAluno['turno'])));

switch (strtoupper($nome_turma_exibida[0])) {
    case 'TARDE':
        $turno_exibido = 'VESPERTINO';
        break;
    case 'MANHA':
        $turno_exibido = 'MATUTINO';
        break;
    case 'NOITE':
        $turno_exibido = 'NOTURNO';
        break;
}

$serieTurmaTurno = trim($dadosAluno['serie']) . ' / ' . trim($dadosAluno['turma']) . ' Turno: ' . $turno_exibido;
if ($dadosAluno['datanascimento'] != "" || $dadosAluno['datanascimento'] != null) {
    $dataNascimento = strtoupper(date('d/m/Y', strtotime($dadosAluno['datanascimento'])));
}else{
    $dataNascimento ='';
}

if ($dadosAluno['datamatricula'] != ""  || $dadosAluno['datamatricula'] != null) {
    $dataMatricula = strtoupper(date('d/m/Y', strtotime($dadosAluno['datamatricula'])));
}else{
    $dataMatricula = '';
}

$ano_atual = date('Y');
$nomeMae = strtoupper(trim($dadosAluno["nomemae"]));
$nomePai = strtoupper(trim($dadosAluno["nomepai"]));
$nomeResp = strtoupper(trim($dadosAluno["responsavel"]));


$RepresentanteLegal = trim($dados_ficha_aluno['ed47_c_nomeresp']);
//}
$nacionalidade = strtoupper("Brasileira");
$rua = strtoupper(trim($dadosAluno['endereco']));
$numero = strtoupper(trim($dadosAluno['numero']));
$bairro = strtoupper(trim($dadosAluno['bairro']));
//$usualogado = @$GLOBALS["DB_login"];
//$sql_login = " SELECT * FROM db_usuarios WHERE login ='{$usualogado}'";
//$result = db_query($sql_login);TAVA
//$usuarioLogin = pg_fetch_assoc($result);
//$nomeUsuario = $usuarioLogin['nome'];
$transporte = $dadosAluno['transporte'];


$oPdf->SetXY(0, 0);
$oPdf->Image('../../img/ficha_matricula_modelo.jpg', 50, 50, 100);
$oPdf->SetFont('Arial', 'B', 13);
$oPdf->Text(75, 45, 'Comprovante de Matricula');
$oPdf->SetTextColor(150);
$oPdf->SetFont('Arial', 'B', 15);
$oPdf->Text(95, 62.5, $matricula);
$oPdf->SetFont('Arial', 'B', 7);
$oPdf->Text(80, 67.5, $nome);
$oPdf->Text(82, 73, $escola);
$oPdf->Text(103, 77.9, $serieTurmaTurno);
$oPdf->Text(77, 83.2, $nomeMae);
$oPdf->Text(86, 88.4, $dataNascimento);
$oPdf->Text(105, 93.5, $dataMatricula);

$oPdf->SetTextColor(0);
$oPdf->SetXY(10, 114);
$oPdf->SetFont('Arial', '', 6);
//$oPdf->Text(80, 113, 'Comprovante impresso em: ' . date('d/m/Y') . ' às ' . date('H:i:s'));
$oPdf->Cell(53, 4, '', 0, 0);
//$oPdf->Cell(10, 4, '', 0, 0);
$oPdf->Cell(80, 20, 'comprovante_matricula.php         Emissor: '.$nome_escola_cab.'                     Exercício: '.date('Y',strtotime('today')).'        Data: '.date("d-m-Y     H:i:s",strtotime('now')).'      Página ' . $oPdf->PageNo().' de {nb}', "T", 0, 'C');
//$oPdf->Cell(80, 4, substr(ucwords(strtolower($nomeUsuario)), 0, 30) . ' ' . strtolower($usuario['funcao']) . ' ' . '(matrícula:' . $usuario['matricula'] . ')', 0, 0, 'C');
//$oPdf->Cell(10, 4, '', 0, 0);


// COPIA COMPROVANTE
//$oPdf->HeaderCentralizado();
$oPdf->SetXY(1, 100);
$oPdf->Image('../../img/Cabecalho_pdf.png', 7, 160, 200);
$oPdf->SetFont('Arial', 'BI', 10);
$oPdf->Text(33, 166, 'PREFEITURA DE CAMAÇARI');
$oPdf->Text(33, 171, $nome_escola_cab);
$oPdf->SetFont('Arial', 'I', 8);
$oPdf->SetFont('Arial', '', 6);
$oPdf->Text(33, 175, 'INEP ESCOLA :' . $inep_escola_cab); //.'-'.'INEP:'.$inep_escola
$oPdf->Text(33, 178, $ruaescola_cab . ',');
$oPdf->Text(33, 181, $numescola_cab . " - " . $bairroescola_cab);
$oPdf->Text(33, 183, $cidadeescola_cab . " - " . $estadoescola_cab);
$oPdf->Text(33, 185, $telefoneescola_cab);
$oPdf->SetFont('Arial', '', 8);
$oPdf->Text(135, 170, 'FICHA DO ALUNO');
$oPdf->Text(135, 174, trim($codigo_aluno_cab).' - '.trim($nome_aluno_cab));
$oPdf->Text(135, 178, 'SERIE: '.trim($serie_cab).' - TURNO: '.trim($turno_cab));
$oPdf->SetY(36);




$oPdf->Image('../../img/ficha_matricula_modelo.jpg',50,210,100);
$oPdf->SetFont('Arial', 'B', 13);
$oPdf->Text(75, 205, 'Comprovante de Matricula');
$oPdf->SetTextColor(150);
$oPdf->SetFont('Arial', 'B', 15);
$oPdf->Text(95, 222.5, $matricula);
$oPdf->SetFont('Arial', 'B', 7);
$oPdf->Text(80, 228, $nome);
$oPdf->Text(82, 233, $escola);
$oPdf->Text(103, 238.5, $serieTurmaTurno);
$oPdf->Text(77, 243.5, $nomeMae);
$oPdf->Text(86, 248.5, $dataNascimento);
$oPdf->Text(105, 253.5, $dataMatricula);

$oPdf->SetTextColor(0);
$oPdf->SetXY(10, 273);
$oPdf->SetFont('Arial', '', 6);
//$oPdf->Text(80, 272, 'Comprovante impresso em: ' . date('d/m/Y') . ' às ' . date('H:i:s'));
$oPdf->Cell(53, 4, '', 0, 0);


 $oPdf->Cell(80, 10, 'comprovante_matricula.php         Emissor: '.$nome_escola_cab.'                     Exercício: '.date('Y',strtotime('today')).'        Data: '.date("d-m-Y     H:i:s",strtotime('now')).'      Página ' . $oPdf->PageNo().' de {nb}', "T", 0, 'C');
//$oPdf->Cell(80, 4, substr(ucwords(strtolower($nomeUsuario)), 0, 30) . ' ' . strtolower($usuario['funcao']) . ' ' . '(matrícula:' . $usuario['matricula'] . ')', 0, 0, 'C');
//$oPdf->Cell(10, 4, '', 0, 0);


//$oPdf->SetXY(10,150);
//$oPdf->Cell(85,4,$nomeUsuario,0,0,'C');
//$oPdf->SetFont('Arial', '', 6);
//$oPdf->Text(123,150,'Comprovante impresso em: '.date('d/m/Y').' às '.date('H:i:s'));


////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// /////////////////////////////////////////////////////////////////////////////////////////////////////
//TERMO DE RESPONSABILIDADE
//$oPdf->addPage();
//$oPdf->SetXY(0,0);
////$oPdf->Image('imagens/files/logo_relatorio.jpg',0,0,215);
//
//$oPdf->SetXY(0,0);
//$oPdf->SetFont('Arial', 'B', 10);
//$oPdf->Text(70,40,"TERMO DE RESPONSABILIDADE");
//$oPdf->SetFont('Arial','B', 10);
//$oPdf->Text(10, 60, $escola);
//$oPdf->Text(15, 75, 'Aluno(a): ' . $nome);
//$oPdf->Text(15, 80, 'Turma/Ano: ' . $serieTurmaTurno . ' / ' . $ano_atual);
//$oPdf->SetFont('Arial','', 10);
//$oPdf->SetXY(10,90);
//
//$oPdf->Image('imagens/files/bolinha_lista.jpg',11,106,2);
//$oPdf->Image('imagens/files/bolinha_lista.jpg',11,111,2);
//$oPdf->Image('imagens/files/bolinha_lista.jpg',11,121,2);
//$oPdf->Image('imagens/files/bolinha_lista.jpg',11,126,2);
//$oPdf->Image('imagens/files/bolinha_lista.jpg',11,141,2);
//$oPdf->Image('imagens/files/bolinha_lista.jpg',11,156,2);
//$oPdf->Image('imagens/files/bolinha_lista.jpg',11,161,2);
//$oPdf->Image('imagens/files/bolinha_lista.jpg',11,166,2);
//
//$text ="Eu {$RepresentanteLegal}, responsável pelo aluno acima, solicito a RENOVAÇÃO DE MATRÍCULA para o ano letivo de {$ano_atual} comprometendo-me em assumir total responsabilidade referente ao cumprimento do Regimento Escola Interno, com ênfase nos seguintes itens:
//   Ao uso do fardamento escolar nas dependêcias da Unidade Escolar;
// A zelar e presevar o patrimônio escolar - prédios, muros, salas, sanitários, área de circulação, mobiliário, equipamentos materiais e outros bens - ressarcindo a escola por quaisquer danos que venha causar;
//   A devolver os livros didáticos recebidos no inicio do ano letivo;
//   Ser respeitoso para com colegas, diretores, professores, funcionários e colaboradores da escola, independentemente de idade, gêreno, raça/etnia, religião, origem social, nacionalidade, deficiências, estado civil, orientacao sexual ou politica;
//   Não utilizar equipamentos eletrônicos como: telefones celulares, jogos portáteis, tocadores de música, máquinas fotográficas ou outros dispositivos de comunicação e entretenimento, exceto para uso didático quando solicitado pelo educador;
//   É proibido, consumir ou manusear qualquer tipo de drogas nas dependencias da Unidade Escolar;
//   Não é permitido portar armas ou instrumentos que possam colocar em risco a segurança das pessoas;
// Não é permitido divulgar, por qualquer meio de publicidade ou redes sócias, ações que envolvam direta ou indiretamente o nome da Unidade Escolar, funcionários ou educandos, sem prévia autorização da direção e/ou do Conselho Escolar.
// ";
//
//$oPdf->MultiCell(190,5,$text);
//$oPdf->SetFont('Arial', 'B', 10);
//$oPdf->SetXY(10,190);
//$oPdf->Cell(100,10,'Camaçari - BA, '.date('d-m-Y'));
//$oPdf->Line(10,220,80,220);
//$oPdf->Line(120,220,190,220);
//$oPdf->SetXY(10,220);
//$oPdf->Cell(70,10,$nomeUsuario,0,0,'C');
//$oPdf->Cell(40,10,'');
//$oPdf->Cell(70,10,$RepresentanteLegal,0,0,'C');


// TRANSPORTE ESCOLAR



if ($transporte == 1) {

    $oPdf->addPage();
    $oPdf->SetXY(0, 0);
    //  $oPdf->Image('imagens/files/logo_relatorio.jpg',0,0,215);
    $oPdf->SetFont('Arial', 'B', 13);
    $oPdf->Text(95, 40, 'ANEXO V');
    $oPdf->SetFont('Arial', 'B', 9);
    $oPdf->Text(55, 45, 'Termo de Uso de Transporte Escolar Público e Responsabilização');
    $oPdf->Text(10, 60, $escola);
    $oPdf->Text(15, 75, 'Aluno(a): ' . $nome);
    $oPdf->Text(15, 80, 'Turma/Ano: ' . $serieTurmaTurno . ' / ' . $ano_atual);
    $oPdf->SetXY(10, 90);
    $text = "Eu, {$RepresentanteLegal} responsável legal pelo(a) educando(a) acima identificado(as), comprometo-me em assumir total responsabilidade decorrente de má utilização do Transporte Escolar gratuito oferecido pela Prefeitura Municipal de Camaçari, o que implica em ressarcimento ao patrimônio público ou privado, em consequência de indisciplina grave e/ou ato deliberado de vandalismo acometido pelo(a) mesmo(a).";
    $oPdf->SetFont('Arial', '', 10);
    $oPdf->MultiCell(0, 5, $text, 0, 'J');
    $oPdf->SetXY(10, 130);
    $oPdf->SetMargins(10, 10);
    $oPdf->Line(10, 137, 150, 137);
    $oPdf->Ln(5);
    $oPdf->SetFont('Arial', '', 8);
    $oPdf->Cell(100, 10, "Responsavel Legal: ($RepresentanteLegal)");
    $oPdf->SetFont('Arial', '', 10);
    $oPdf->Ln(20);
    $oPdf->Cell(100, 10, 'Camaçari - BA, ' . date('d-m-Y'));
    $oPdf->Ln(20);
    //$oPdf->Line(10, 182, 50, 182);
    $oPdf->Ln(5);
    $oPdf->SetFont('Arial', 'I', 6);
  //  $oPdf->Cell(100, 10, substr(ucwords(strtolower($nome_escola_cab)), 0, 30));// 'Assinatura Diretor ou do Secretário Escolar');


    //////////header
//Position at 1.5 cm from bottom
//    $oPdf->SetFont('Arial', '', 5);
//    $oPdf->text(10, $oPdf->h - 8, 'Base: ' . db_base_ativa());
//    $oPdf->SetFont('Arial', 'I', 6);
//    $oPdf->SetY(-10);
//    $nome_h = @$GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"];
//    $nome_h = substr($nome_h, strrpos($nome_h, "/") + 1);
////    $result_nomeusu = db_query("select nome as nomeusu from db_usuarios where id_usuario =" . db_getsession("DB_id_usuario"));
//    if (pg_numrows($result_nomeusu) > 0) {
//        $nomeusu = pg_result($result_nomeusu, 0, 0);
//    }
//    if (isset($nomeusu) && $nomeusu != "") {
//        $emissor = $nomeusu;
//    } else {
//        $emissor = @$GLOBALS["DB_login"];
//    }
$oPdf->SetXY(1,285);
    $oPdf->Cell(0, 10, 'comprovante_matricula.php         Emissor: '.$nome_escola_cab.'                     Exercício: '.date('Y',strtotime('today')).'        Data: '.date("d-m-Y     H:i:s",strtotime('now')).'      Página ' . $oPdf->PageNo().' de {nb}', "T", 0, 'C');
    // $oPdf->Cell(0, 10, $nome_h . '     Emissor: ' . substr(ucwords(strtolower($emissor)), 0, 30) . '     Exercício: ' . db_getsession("DB_anousu") . '    Data: ' . date("d-m-Y", db_getsession("DB_datausu")) . " - " . date("H:i:s"), "T", 0, 'C');
   // $oPdf->Cell(0, 10, 'Página ' . $oPdf->PageNo() . ' de {nb}', 0, 1, 'R');
}


//ANEXO VII
$oPdf->addPage();
$oPdf->SetMargins(10, 10);
$oPdf->SetXY(0, 0);


//$oPdf->Image('../../img/anexo-v.jpg',0,0,215);
$oPdf->SetFont('Arial', 'B', 12);
$oPdf->Text(77, 40, 'Termo de Responsabilidade');
$oPdf->SetFont('Arial', 'B', 10);
$oPdf->Text(10, 60, $escola);
$oPdf->Text(15, 75, 'Aluno(a): ' . $nome);
$oPdf->Text(15, 80, 'Turma/Ano: ' . $serieTurmaTurno . ' / ' . $ano_atual);
$oPdf->SetFont('Arial', '', 10);

$text = "Eu,   {$RepresentanteLegal},responsável pelo(a) educando(a) acima identificado(a), solicito a matrícula para oanoletivo {ano_atual}, comprometendo-me:
       1. ao uso do fardamento escolar nas dependências da unidade escolar municipal (quando distribuído pelopoderpúblico municipal);
       2. a zelar e preservar o patrimônio escolar - prédio, muros, salas, sanitários, áreas de circulação, mobiliário, equipamentos materiais, transporte escolar e outros bens - ressarcindo a unidade escolar por quaisquer danos que venham a causar;
       3. a devolver os livros didáticos recebidos no período do ano letivo;
       4. ser respeitoso para com colegas, diretores, professores, coordenador pedagógico, funcionários e colaboradores  da unidade escolar, independentemente de idade, gênero, raça/etnia, religião,  origem  social,  nacionalidade, deficiências, estado civil, orientação sexual ou política;
       5. não utilizar equipamentos eletrônicos como: telefones celulares, jogos portáteis, tocadores de música,máquinasfotográficas ou outros dispositivos de comunicação e entretenimento, exceto para uso didático quando solicitado pelo educador;
       6. obedecer a proibição quanto ao consumo ou manuseamento de qualquer tipo de drogas nas dependências da unidade escolar municipal;
       7. não portar armas ou instrumentos que possam colocar em risco a segurança das pessoas;
       8. abster-se de atos que perturbem a ordem ou ofendam a dignidade da pessoa humana;
       9. não apelidar, xingar, discriminar ou expor a situações embaraçosas colegas, professores e/ou funcionários;
       10. não se ausentar da unidade escolar, sem que esteja devidamente autorizado pela família e pela unidade escolar;
       11. não divulgar, por qualquer meio de publicidade ou redes sociais, ações que envolvam direta ou indiretamenteonome da unidade escolar municipal, funcionários ou educandos, sem prévia autorização da direção e/ou do Conselho Escolar;
       12. não encaminhar a unidade escolar a criança ou adolescente com sintomas que exijam maiores cuidados mantê-lo em casa sob observação.";

$oPdf->SetXY(20, 90);
$oPdf->SetFont('Arial', '', 10);
$oPdf->MultiCell(0, 5, $text, 0, 'L');
$oPdf->Line(10, 240, 100, 240);
$oPdf->Text(10, 245, 'Responsavel Legal:(' . $RepresentanteLegal . ')');
$oPdf->Text(10, 255, 'Camaçari - BA, ' . date('d-m-Y'));
$oPdf->Line(10, 270, 100, 270);
$oPdf->SetFont('Arial', '', 10);


//$oPdf->Text(10, 275, substr(ucwords(strtolower($emissor)), 0, 30) . ' ' . strtolower($usuario['funcao']) . ' ' . '(matrícula:' . $usuario['matricula'] . ')');

//////////header
//Position at 1.5 cm from bottom
$oPdf->SetFont('Arial', '', 5);
//$oPdf->text(10,$oPdf->h-8,'Base: '.'DB SADAS');//db_base_ativa()
$oPdf->SetFont('Arial', 'I', 6);
$oPdf->SetY(-10);
//$nome_h = @$GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"];
//$nome_h = substr($nome_h, strrpos($nome_h, "/") + 1);

//$result_nomeusu = db_query("select nome as nomeusu from db_usuarios where id_usuario =".db_getsession("DB_id_usuario"));
//    if (pg_numrows($result_nomeusu)>0){
//        $nomeusu = pg_result($result_nomeusu,0,0);
//    }TAVA

//if (isset($nomeusu) && $nomeusu != "") {
//    $emissor = $nomeusu;
//} else {
//    $emissor = @$GLOBALS["DB_login"];
//}
//$oPdf->Cell(0,10,$nome_h.'     Emissor: '.substr(ucwords(strtolower($emissor)),0,30).'     Exercício: '.db_getsession("DB_anousu").'    Data: '.date("d-m-Y",db_getsession("DB_datausu"))." - ".date("H:i:s"),"T",0,'C'); TAVA
//$oPdf->Cell(0, 10, 'Página ' . $oPdf->PageNo() . ' de {nb}', 0, 1, 'R');
$oPdf->Cell(0, 10, 'comprovante_matricula.php         Emissor: '.$nome_escola_cab.'                     Exercício: '.date('Y',strtotime('today')).'        Data: '.date("d-m-Y     H:i:s",strtotime('now')).'      Página ' . $oPdf->PageNo().' de {nb}', "T", 0, 'C');

//ANEXO VIII
$oPdf->addPage();

$oPdf->SetXY(0, 0);
$oPdf->SetFont('Arial', 'B', 13);
$oPdf->Text(60, 50, 'Termo de Autorização de Uso de Imagem');
$oPdf->SetFont('Arial', 'B', 9);
//$oPdf->Text(60,45,'Termo de autorização de uso de Imagem (Menor de Idade)');
$oPdf->SetXY(10, 60);
$oPdf->SetMargins(10, 10);
$text = $text = "     {$nome}, nacionalidade {$nacionalidade}, menor de idade, neste ato devidamente representado por seu responsável legal, {$RepresentanteLegal}, natural de {naturalidade} residente à {$rua} , Nº {$numero} - {$bairro}, município de Camaçari estado da Bahia, AUTORIZO o uso de minha imagem em todo e qualquer material entre imagens de vídeo, fotos e documentos, para ser utilizada pela Unidade Escolar,  sejam  essas  destinadas à divulgação ao público em geral. A presente autorização é concedida a título gratuito, abrangendo o uso  da imagem acima mencionada em todo território nacional, das seguintes formas: (I) outdoor, (II) busdoor, folhetos em geral(encartes, maladireta, catálogo,etc), (III) folder de apresentação, (IV) anúncio sem revistas e jornais em geral,(V) home page, (VI) cartazes, (VII) back-light, (VIII) mídia eletrônica (painéis, vídeos, televisão, cinema, programa para rádio, entre outros). Fica ainda autorizada, de livre e espontânea vontade, para os mesmos fins, a cessão de direitos da veiculação das imagens não recebendo para tanto qualquer tipo de remuneração. Por está ser a expressão da minha vontade declaro que autorizo o uso acima descrito sem que nada haja a ser reclamado a título de direitos conexos à minha imagem ou a qualquer outro, e assino a presente autorização.";
$oPdf->SetFont('Arial', '', 10);
$oPdf->MultiCell(0, 5, $text, 0, 'J');
$oPdf->Ln(10);
//$oPdf->Line(10,137,150,137);
$oPdf->Ln(5);
$oPdf->SetFont('Arial', '', 8);
//$oPdf->Cell(100,10,'(Assinatura do requerente, de acordo com o documento de identidade apresentado)');
$oPdf->SetFont('Arial', '', 10);
$oPdf->Ln(24);
$oPdf->Line(10, 165, 100, 165);
$oPdf->Ln(5);
$oPdf->Cell(100, 10, 'Responsavel Legal:(' . $RepresentanteLegal . ')');
$oPdf->Ln(20);
$oPdf->Cell(100, 10, 'Camaçari - BA, ' . date('d-m-Y'));
$oPdf->Ln(20);
$oPdf->Line(10, 210, 100, 210);
$oPdf->Ln(5);
//$oPdf->Cell(100, 10, substr(ucwords(strtolower('$emissor')), 0, 30) . ' ' . strtolower($usuario['funcao']) . ' (matrícula:' . $usuario['matricula'] . ')');

//////////header
//Position at 1.5 cm from bottom
$oPdf->SetFont('Arial', '', 5);
//$oPdf->text(10,$oPdf->h-8,'Base: '.db_base_ativa()); TAVA
$oPdf->SetFont('Arial', 'I', 6);
$oPdf->SetY(-10);
//$nome_h = @$GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"];
//$nome_h = substr($nome_h, strrpos($nome_h, "/") + 1);
//$result_nomeusu = db_query("select nome as nomeusu from db_usuarios where id_usuario =".db_getsession("DB_id_usuario"));
//if (pg_numrows($result_nomeusu)>0){
//    $nomeusu = pg_result($result_nomeusu,0,0);
//}TAVA
//if (isset($nomeusu) && $nomeusu != "") {
//    $emissor = $nomeusu;
//} else {
//    $emissor = @$GLOBALS["DB_login"];
//}
//$oPdf->Cell(0,10,$nome_h.'     Emissor: '.substr(ucwords(strtolower($emissor)),0,30).'     Exercício: '.db_getsession("DB_anousu").'    Data: '.date("d-m-Y",db_getsession("DB_datausu"))." - ".date("H:i:s"),"T",0,'C');
//$oPdf->Cell(0, 10, 'Página ' . $oPdf->PageNo() . ' de {nb}', 0, 1, 'R');
$oPdf->Cell(0, 10, 'comprovante_matricula.php         Emissor: '.$nome_escola_cab.'                     Exercício: '.date('Y',strtotime('today')).'        Data: '.date("d-m-Y     H:i:s",strtotime('now')).'      Página ' . $oPdf->PageNo().' de {nb}', "T", 0, 'C');
$oPdf->Output();
die();

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
