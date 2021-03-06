<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../classe/Conn.php');
Conn::conect();

//verifica se usuario ta logado
if (!isset($_SESSION['id_usuario'])) {
    echo 'expirou';
    die();
}

$sql_escolas = "
    select 
        ed18_i_codigo, 
        trim(ed18_c_nome) as escola 
    from reserva.escolareserva ER
    inner join escola E
        on ER.ed56_i_escola = E.ed18_i_codigo
    inner join configuracoes.db_depart DD 
        on E.ed18_i_codigo = DD.coddepto 
    where limite >= now() or limite is null 
    group by ed18_i_codigo ,ed18_c_nome
    order by ed18_c_nome";

$stmt = Conn::$conexao->prepare($sql_escolas);
$stmt->execute();
$escolas = $stmt->fetchALL();

$sql_status = "select * from reserva.alunostatusreserva order by status_descr";

$stmt = Conn::$conexao->prepare($sql_status);
$stmt->execute();
$arr_status = $stmt->fetchALL();

?>

<script>
    title('Relat�rio Alunos Agendados');
    subTitle1('Aluno');
    subTitle2('Relat�rio Alunos Agendados');
</script>

<div class="card col-md-8 offset-md-2">
    <div class="card-body">
        <div class="form-group">
            <div class="row">     
                <div class="col-md-3 offset-md-3">
                <label for="">Escola</label>    
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 offset-md-3">
                   <select class="custom-select" style="max-width: 40.0em; width: 40.0em;" id="cp_escola" multiple>
                    <?php foreach ($escolas as $escola) { ?>
                        <option value="<?php echo $escola['ed18_i_codigo'] ?>"><?php echo $escola['escola'] ?></option>

                    <?php } ?>
                </select>
                </div>    
            </div>     
        </div>
        <div class="form-group">
            <div class="row">   
                <div class="col-md-6 offset-md-3">
                    <input type="checkbox" onclick="showInputDate()" id="cp_with_period">    
                    <label for="cp_with_period">Com per�odo de agendamento</label>
                </div>
            </div>    
            <div id='show_input_date' style="display: none;">
                <div class="row">     
                    <div class="col-md-3 offset-md-3">
                        <label for="">Data Inicial</label>
                        <input class="form-control date " type="text" id="date_initial">
                    </div>
                    <div class="col-md-3">
                        <label for="">Data Final</label>
                        <input class="form-control date " type="text" id="date_end">
                    </div>
                </div>    
            </div>
        </div>
        <div class="form-group">
            <div class="row">     
                <div class="col-md-3 offset-md-3">
                    <button id="btn_gerar_rel" class="btn btn-outline-success" onclick="gerarRelatorio()">Gerar Relat�rio</button>
                </div>    
                <div class="col-md-3">
                    <button id="btn_gerar_rel" class="btn btn-outline-success" onclick="getForm('app/aluno/rel_aluno_agendado.php')">Limpar</button>
                </div>    

                <img id="loading_gera_rel" style="display: none" src="img/loading.gif" width="30">
            </div>
        </div>
    </div>    
</div>

<div id="relatorio"></div>
<script type="text/javascript" src="js/jquery.multi-select.js"></script>
<script>
    $('.date').daterangepicker({
            "locale": {
        "format": "DD/MM/YYYY",
        "separator": " - ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "daysOfWeek": [
          "Dom",
          "Seg",
          "Ter",
          "Qua",
          "Qui",
          "Sex",
          "Sab"
        ],
        "monthNames": [
          "Janeiro",	
          "Fevereiro",
          "Mar�o",
          "Abril",
          "Maio",
          "Junho",
          "Julho",
          "Agosto",
          "Setembro",
          "Outubro",
          "Novembro",
          "Dezembro"
        ],
        "firstDay": 1
        },
        singleDatePicker: true,
        //showDropdowns: true,
        minYear: 2019,
        maxYear: parseInt(moment().format('YYYY'),10)
    });
    
    function showInputDate(){
        if($('#cp_with_period:checked').val() === 'on'){
           $('#show_input_date').show();                      
        }else{
            $('#show_input_date').hide();
        }
    }
    
    function gerarRelatorio() {

        let route = 'app/aluno/resource/rel_aluno_agendado_div.php';
        let data = {
            escola: String($('#cp_escola').val()),
            com_periodo: $('#cp_with_period:checked').val(),
            date_initial : $('#date_initial').val(),
            date_end : $('#date_end').val(),
              
        };
        $.post(route, data, (response) => {
            $('#relatorio').html(response).fadeIn();
        }).fail((response) => {
            alert('N�o foi possivel conectar ao servidor');
        });
    }

    <!-- Multi-select - In�cio--> 
    $(function(){
        $('#cp_escola').multiSelect();
    });
    $('#cp_escola').multiSelect({
            noneText: 'Todos',
            presets: [
                {
                    name: 'Todos',
                    options: []
                },
        ]
        });
<!-- Multi-select - Tela de administra��o  Fim--> 
</script>