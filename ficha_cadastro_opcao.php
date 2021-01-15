<?php
session_start();
$banner_passo = 1;
$img_banner_passo = 'img/guia_03.jpg';
require_once('header.php');
include_once("conexao.php");

define('PASSO_ATERIOR', 2);
define('PASSO_ATUAL', 3);

$passo = $_SESSION['passo'];
if (!in_array(PASSO_ATERIOR, $passo)) {
    header('Location:index.php');
}

if (isset($_POST['escola'])) {

    if (!in_array(PASSO_ATUAL, $passo)) {
        array_push($passo, PASSO_ATUAL);
        $_SESSION['passo'] = $passo;
    }
    $_SESSION = array_merge($_SESSION, $_POST);

    header('Location: ficha_proc.php');
}

$c = new Conexao();

$serie  = $_SESSION['vch_serie'];
$data = $_SESSION['sdt_nascimento'];
//$data = date("d/m/Y", strtotime($data_session));

// Separa em dia, mï¿½s e ano
list($dia, $mes, $ano) = explode('/', $data);
$mescorte = 03;
$diacorte = 31;
$anocorte = 2020;

// Descobre que dia ï¿½ hoje e retorna a unix timestamp
$hoje = mktime(0, 0, 0, $mescorte, $diacorte, $anocorte);
// Descobre a unix timestamp da data de nascimento do fulano
$nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

// Depois apenas fazemos o calculo já citado :)
$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
//grupo 1
if ($idade == 1) {
    $msg_avaliado = "Idade do aluno: $idade anos, avaliado para a serie: GRUPO 1";
    $serie = 26; //codigo da serie GRUPO 1
    $_SESSION['vch_serie'] = $serie;
} elseif ($idade == 2) {
    $msg_avaliado = "Idade do aluno: $idade anos, avaliado para a serie: GRUPO 2";
    $serie = 27; //codigo da serie GRUPO 2
    $_SESSION['vch_serie'] = $serie;
} elseif ($idade == 3) {
    $msg_avaliado = "Idade do aluno: $idade anos, avaliado para a serie: GRUPO 3";
    $serie  = 28;
    $_SESSION['vch_serie'] = $serie;
} elseif ($idade == 4) {
    $msg_avaliado = "Idade do aluno: $idade anos, avaliado para a serie: GRUPO 4";
    $serie  = 31;
    $_SESSION['vch_serie'] = $serie;
} elseif ($idade == 5) {
    $msg_avaliado = "Idade do aluno: $idade anos, avaliado para a serie: GRUPO 5";
    $serie  = 32;
    $_SESSION['vch_serie'] = $serie;
}

/*$sql = "
select ed18_i_codigo as codigo , ed18_c_nome as escola
from escola
join turma on ed57_i_escola = ed18_i_codigo
join turmaserieregimemat on ed220_i_turma = ed57_i_codigo
join serieregimemat on ed220_i_serieregimemat = ed223_i_codigo
join serie on ed223_i_serie = ed11_i_codigo
join calendario on ed52_i_codigo = ed57_i_calendario
-- serie de codigo 29 e turma do calendario 2020
where ed11_i_codigo = $serie and ed52_i_ano ='2020'
group by ed18_i_codigo, ed18_c_nome
having (
  (select sum(ed336_vagas) from turma  join turmaturnoreferente on ed336_turma = ed57_i_codigo join calendario on ed52_i_codigo = ed57_i_calendario where ed52_i_ano = 2020 and ed57_i_escola = ed18_i_codigo)
 - (select count(*) from turma join matricula on ed60_i_turma = ed57_i_codigo join calendario on ed52_i_codigo = ed57_i_calendario where ed57_i_escola = ed18_i_codigo and ed52_i_ano = 2020 and ed60_c_situacao = 'MATRICULADO' and ed60_d_datamatricula >= '2020-01-01')
) >= 1
order by ed18_c_nome ";*/


$sql = "
        select ed18_i_codigo as codigo , ed18_c_nome as escola
        from escola.escola
        join escola.turma on ed57_i_escola = ed18_i_codigo
        join escola.turmaserieregimemat on ed220_i_turma = ed57_i_codigo
        join escola.serieregimemat on ed220_i_serieregimemat = ed223_i_codigo
        join escola.serie on ed223_i_serie = ed11_i_codigo
        join escola.calendario on ed52_i_codigo = ed57_i_calendario
        join configuracoes.db_depart DD 
        on ed18_i_codigo = DD.coddepto 
        where (ed11_i_codigo = $serie and ed52_i_ano ='2020' and (limite >= now() or limite is null))
        group by ed18_i_codigo, ed18_c_nome
        order by ed18_c_nome ";

//ed18_i_codigo not in (133,26,129,137,134,135,130,138,131,127,132,136,140,142,143,144,145)

// $sql = "
// select 
//        ed18_i_codigo as codigo , 
//        ed18_c_nome as escola 
// from escola 
// join turma on ed57_i_escola = ed18_i_codigo
// join turmaserieregimemat on ed220_i_turma = ed57_i_codigo
// join serieregimemat on ed220_i_serieregimemat = ed223_i_codigo
// join serie on ed223_i_serie = ed11_i_codigo
// join calendario on ed52_i_codigo = ed57_i_calendario
// where  ed11_i_codigo = $serie and ed52_i_ano ='2020' and ed18_i_codigo not in (133,26,129,137,134,135,130,138,131,127,132,136,140,142,143,144,145)
// group by ed18_i_codigo, ed18_c_nome
// having 
//  (((
// 	select
// 		sum(ed336_vagas)
// 	from turma	join turmaturnoreferente on ed336_turma = ed57_i_codigo
// 	join turmaserieregimemat on	ed220_i_turma = ed57_i_codigo
// 	join serieregimemat on	ed220_i_serieregimemat = ed223_i_codigo
// 	join serie on	ed223_i_serie = ed11_i_codigo
// 	join calendario on 	ed52_i_codigo = ed57_i_calendario
// 	where ed52_i_ano = 2020	and ed57_i_tipoturma = 1 and ed11_i_codigo = $serie and ed57_i_escola = escola.ed18_i_codigo) - 
// 	(select	count(*) from turma
// 	join matricula on ed60_i_turma = ed57_i_codigo
// 	join turmaserieregimemat on	ed220_i_turma = ed57_i_codigo
// 	join serieregimemat on	ed220_i_serieregimemat = ed223_i_codigo
// 	join serie on	ed223_i_serie = ed11_i_codigo
// 	join calendario on	ed52_i_codigo = ed57_i_calendario
// 	where	ed57_i_escola = ed18_i_codigo  and ed11_i_codigo = $serie	and ed57_i_tipoturma = 1 and ed52_i_ano = 2020	and ed60_c_situacao = 'MATRICULADO'	and ed60_d_datamatricula >= '2020-01-01')) 
// 	- (
// 	select	count(*) from	reserva.alunoreserva
// 	join reserva.escolareserva on	escolareserva.id_alunoreserva = alunoreserva.id_alunoreserva
// 	where	ed56_i_escola = ed18_i_codigo	and ed221_i_serie = $serie )) >= 1 
// order by ed18_c_nome
// ";
// die($sql);


$result = pg_query($c->conn(), $sql);
$escolas = pg_fetch_all($result);
$nome_aluno = strtoupper($_SESSION['vch_nome']);

$data = $_SESSION['sdt_nascimento'];

// Separa em dia, mï¿½s e ano
list($dia, $mes, $ano) = explode('/', $data);
$mescorte = 03;
$diacorte = 31;
$anocorte = 2019;

// Descobre que dia ï¿½ hoje e retorna a unix timestamp
$hoje = mktime(0, 0, 0, $mescorte, $diacorte, $anocorte);
// Descobre a unix timestamp da data de nascimento do fulano
$nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

// Depois apenas fazemos o cï¿½lculo jï¿½ citado :)
$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);


$escola_selecionada = isset($_SESSION['escola']) ? $_SESSION['escola'] : '';
$turma_selecionada  = isset($_SESSION['turma']) ? $_SESSION['turma'] : '';

?>
<script>
    function GravarForm(frm)
    {
        escola = document.getElementById('escola').value;
        if (escola==0) {
            $("#msg").trigger("click");
            $("#msg_text").text("Selecione a Escola!");
            document.getElementById('escola').style.color = 'red';
            document.getElementById('escola').style.borderColor = 'red';;
            return false;
        }else{
            document.getElementById("ProsseguirOpcao").disabled = true;  
            document.forms["opcao"].submit();        
        }
            
    }

</script>            
<div class="centr">
    <div class="form-group">
        <div class="row">
            <div class="col-md-10" style="text-align: center">

                <br>
                <h2 class="text-center">Opções de Matricula</h2>
            </div>
        </div>
    </div>
    <form name="opcao" id="opcao"  method="post" action="#">
        <div class="form-group">
            <div class="row">
                <div class="col-md-10" style="text-align: center">
                    <div class="card-body">
                        <label for="">Olá, responsável por <b><?php echo $nome_aluno ?></b>, escolha a
                            <b>UNIDADE ESCOLAR:</b></label>
                        <label style="color: red"><?php echo @$msg_avaliado ?></label>
                        <select required id="escola" name="escola" class="custom-select">
                            <option value="">Selecione uma escola</option>
                            <?php foreach ($escolas as $escola) { ?>
                                <option value="<?php echo $escola['codigo'] ?>"><?php echo $escola['escola'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
       
        <!--        <div class="form-group">-->
        <!--            <div class="row">-->
        <!--                <div class="col-9">-->
        <!--                    <label for="">Escolha uma turma para o ano letivo 2020:</label>-->
        <!--                    <select required id="cp_turmas" name="turma" class="custom-select">-->
        <!--                        <option></option>-->
        <!--                    </select>-->
        <!---->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
       
        <div class="form-group">
            <div class="card-body">
                <input class="btn btn-secondary col-md-2" type="button" onclick="window.location.href='ficha_cadastro_documento.php'" value="Voltar">
                <div class="d-md-none" style="margin:10px;"></div>
                <button type="button" id="ProsseguirOpcao" class="btn btn-success col-md-2" onClick="Javascript:GravarForm(document.Form);"> Prosseguir</button>
            </div>
    </form>
</div>
<div style="min-height: 100px; width:100%;"></div>
<script>
    // function pesq_turmas() {
    //     let escola = $('#escola :selected').val();

    //     $.ajax({
    //             url: "pesq.php",
    //             type: 'get',
    //             data: {
    //                 codigo: escola,
    //                 funcao: 'select_turma'
    //             },
    //             beforeSend: function() {}
    //         })
    //         .done(function(msg) {
    //             let cp_turmas = $('#cp_turmas');
    //             cp_turmas.html(msg);
    //             // let turma_session = "<?php echo $turma_selecionada ?>";

    //             // if (turma_session != ''){
    //             cp_turmas.val(<?php echo $turma_selecionada ?>);
    //             //}

    //         })
    //         .fail(function(jqXHR, textStatus, msg) {
    //             alert('Busca de turmas falhou !');
    //         });

    // }

    let cp_escola = $('#escola');
    // cp_escola.change(function() {
    //     pesq_turmas();
    // });


    cp_escola.val(<?php echo $escola_selecionada ?>);
    //pesq_turmas();
    $('#cp_turmas').val(<?php echo $turma_selecionada ?>);
</script>
<?php



require_once('footer.php');

?>