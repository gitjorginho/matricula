<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../../classe/Conn.php');
Conn::conect();


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
where alunostatusreserva_id  = reserva.alunostatusreserva.id $where_escola),
reserva.alunostatusreserva.status_descr 
from reserva.alunostatusreserva
order by id
";

$stmt = Conn::$conexao->prepare($sql_escolas);
$stmt->execute();
$arr_status = $stmt->fetchALL();

?>
<div class="card-body">
    <button class="btn btn-outline-secondary btn-small" onclick="PrintImage()">Imprimir</button>


    <canvas id="myChart" width="400" height="200"></canvas>



</div>
<?php
// string representando array de titulos
$label = '[';
foreach ($arr_status as $key => $value) {

    if ($key == '0') {
        $label .=  "'" . $value['status_descr'] . "'";
    } else {
        $label .=  ',' . "'" . $value['status_descr'] . "'";
    }
}
$label .= ']';


//string representado array de valores

$data = '[';
foreach ($arr_status as $key => $value) {
    if ($key == '0') {
        $data .=  $value['qtd'];
    } else {
        $data .=  ',' . $value['qtd'];
    }
}
$data .= ']';


?>
<script>
    function PrintImage() {
        var canvas = document.getElementById("myChart");
        var win = window.open();
        win.document.write("<br><img src='" + canvas.toDataURL() + "'/>");
        win.print();
    }
</script>




<script>
    labels = <?php echo $label; ?>;
    data = <?php echo $data; ?>;

    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: "Status",
                data: data,
                backgroundColor: [
                    'green',
                    '#00a000',
                    '#009000',
                    '#0d730d',
                    '#0d730d',
                    '#006400',
                    '#00a000',
                    '#2e8b57',
                    '228b22',
                    '#0d730d',

                ],
                borderColor: [
                    'green',
                    '#00a000',
                    '#009000',
                    '#0d730d',
                    '#0d730d',
                    'green',
                    '#00a000',
                    '#009000',
                    '#0d730d',
                    '#0d730d',

                ],
                borderWidth: 1
            }]
        },
        options: {
            showAllTooltips: true,
            title: {
                display: true,
                text: '<?php echo  $title_escola ?>'
            },
            legend: {
                "display": true
            },
            tooltips: {
                "intersect" : true,
                "enabled": true
            },
            scales: {
                yAxes: [{
                    ticks: {
                        stepSize: 1,
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    myChart.defaults.global.tooltips.intersect = false;
    myChart.defaults.global.tooltips.enabled = true;
</script>