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
?>


<script>
    title('Relatório Matriculados no Portal ');
    subTitle1('Alunos');
    subTitle2('Relatório Matriculados no Portal');
</script>
<div class="card col-md-8 offset-md-2">
    <div class="card-body">
        <div class="form-group">
           <br>
           <div class="row" style="text-align: center">
                <div class="col-sm-12">
                    <button id="btn_gerar_rel" class="btn btn-outline-success" onclick="gerarRelatorioPDF()">Gerar Relatório - PDF</button>
                    <button id="btn_gerar_rel" class="btn btn-outline-success" onclick="gerarRelatorioExcel()">Gerar Relatório - EXCEL</button>
                    <img id="loading_gera_rel" style="display: none" src="img/loading.gif" width="30">
                    <input class="form-control" type="hidden" name="variavel" id="variavel"  value="1"/>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="relatorio"></div>
<script>
    function gerarRelatorioPDF() {
        let formato = 'pdf'
        let route = 'app/aluno/resource/rel_aluno_matriculado_div_pdf.php';
        let data = {
            formato :formato
        };
        $.post(route, data, (response) => {
            $('#relatorio').html(response).fadeIn();
        }).fail((response) => {
            alert('Não foi possivel conectar ao servidor');
        });
    }
    function gerarRelatorioExcel() {
        let formato = 'excel'
        let route = 'app/aluno/resource/rel_aluno_matriculado_div_excel.php';
        let data = {
             formato :formato
        };
        $.post(route, data, (response) => {
            //alert('Arquivo gerado com sucesso!');
            $('#relatorio').html(response).fadeIn();
        }).fail((response) => {
            alert('Não foi possivel conectar ao servidor');
        });
    }
</script>