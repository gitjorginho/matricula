<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../../classe/Conn.php');
Conn::conect();


if (isset($_GET['com_periodo'])) {
    $where_peiod = "and adr_d_data between '{$_GET['date_initial']}' and '{$_GET['date_end']}'";
} else {
    $where_peiod = '';
}

$where_status = "";
$espessura_da_barra = '70%';
if ($_GET['status'] != '') {
    $where_status = "where id in ({$_GET['status']})";
    $espessura_da_barra = '20%';
}


if ($_GET['escola'] != 0) {
    $where_escola = "and ed56_i_escola = {$_GET['escola']}";
    //pega o nome da escola
    $sql_nome_escolas = " select trim(ed18_c_nome) as ed18_c_nome from escola where ed18_i_codigo = {$_GET['escola']} ";
    $stmt = Conn::$conexao->prepare($sql_nome_escolas);
    $stmt->execute();
    $nome_escola = $stmt->fetch(PDO::FETCH_ASSOC);
    $title_escola = $nome_escola['ed18_c_nome'];
} else {
    $where_escola = '';
    $title_escola = 'TODAS AS ESCOLAS';
}

$sql_escolas = " select 
(select count(*) as qtd 
from reserva.alunoreserva 
join reserva.escolareserva on escolareserva.id_alunoreserva = alunoreserva.id_alunoreserva   
join reserva.auditoriareserva on auditoriareserva.id_alunoreserva = alunoreserva.id_alunoreserva
where alunostatusreserva_id  = reserva.alunostatusreserva.id $where_escola $where_peiod),
reserva.alunostatusreserva.status_descr,
reserva.alunostatusreserva.status_abrev  
from reserva.alunostatusreserva
$where_status 
order by status_abrev
";
//die($sql_escolas);
$stmt = Conn::$conexao->prepare($sql_escolas);
$stmt->execute();
$arr_status = $stmt->fetchALL();

?>

<div class="card-body" style="height: 1100px;">
    <div class="row">   
        <div class="col-md-5 offset-md-5">
            <button class="btn btn-outline-secondary btn-small" onclick="PrintImage()">Imprimir</button>
        </div>  
    </div>    
    <div class="row">   
        <br>
    </div>    
    <div id="page_print" style="position: relative;" >
         <h3 style=" width:100%; position: absolute; top:70px; z-index:9999; text-align:center;  ">Relatório por status: <?php echo $title_escola ?></h3>
        <div id="barchart_values" style="width: 1000px; height: 800px;"></div>
        <div  style="position:absolute; top:700px; z-index:9999; width: 1000px; background:white ; box-sizing: border-box; padding-left:400px">
            <h5>Legenda:</h5>
            <?php
            foreach ($arr_status as $status) {
                echo "<b style='font-size:10px'>" . $status['status_abrev'] . '</b>' . ' - ' . "<i style='font-size:10px' >" . $status['status_descr'] . '</i>' . '<br>';
            }
            ?>
        </div>
    </div>
</div>
<?php

?>
<script>
    function PrintImage() {
        var conteudo = document.getElementById('page_print').innerHTML,
            tela_impressao = window.open('about:blank');
        tela_impressao.document.write(conteudo);
        tela_impressao.print();
    }
</script>

<script>
    google.charts.load("current", {
        packages: ["corechart"]
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ["Element", "Density", {
                role: "style"
            }],
            <?php

            $cores = array(
                '#008000',
                '#469536',
                '#6eaa5e',
                '#93bf85',
                '#b7d5ac',
                '#dbead5',
                '#008000',
                '#469536',
                '#6eaa5e',
                '#93bf85',
                '#b7d5ac',
                '#dbead5',
                '#008000',
                '#469536',
                '#6eaa5e',
                '#93bf85',
                '#b7d5ac',
                '#dbead5',
                '#008000',
                '#469536',
                '#6eaa5e',
                '#93bf85',
                '#b7d5ac',
                '#dbead5',
            );

            $i = 0;
            foreach ($arr_status as $status) {
                $i++;
                echo "['{$status['status_abrev']}', {$status['qtd']}, '{$cores[$i]}'],";
            }
            ?>
        ]);

        var view = new google.visualization.DataView(data);
        view.setColumns([0, 1,
            {
                calc: "stringify",
                sourceColumn: 1,
                type: "string",
                role: "annotation"
            },
            2
        ]);

        options = {
            title: "Relatório por status - ",
            titlePosition:'none',
            titleTextStyle: {
                color: '333333',
                fontName: 'Arial',
                fontSize: 20
            },
            width: 1000,
            height: 800,
            bar: {
                groupWidth: "<?php echo $espessura_da_barra ?>"
            },
            legend: "none",
            chartArea: {
                width: '70%'
            }
        };
        var chart = new google.visualization.BarChart(document.getElementById("barchart_values"));
        chart.draw(view, options);
    }
</script>