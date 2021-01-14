<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 29/01/2020
 * Time: 09:42
 */
session_start();

// Inicializa a paginação
$paginacao ='0';
if (isset($_GET['paginacao'])){
    $paginacao = $_GET['paginacao'];
}
if (isset($_GET['voltaredicao'])){
   $_SESSION['voltaredicao'] = $_GET['voltaredicao'];
}

//var_dump($_GET['voltaredicao']);

//verifica se usuario ta logado
 if(!isset($_SESSION['id_usuario'])){
     echo 'expirou';              
     die();
}

?>
<?php
require('../classe/Aluno.php');
$Status = new Aluno();
$status = $Status->allStatus();
?>
<script>
    title('Pesquisar Aluno')
    subTitle1('Aluno');
    subTitle2('Pesquisar Aluno');
</script>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class=" offset-md-4 col-md-4">
                        <div class="form-group">
                            <div class="row">     
                                <label for="">Código</label>
                            </div>
                            <div class="row"> 
                                <input type="text" id="cp_cod_aluno" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row"> 
                                <label for="">Aluno</label>
                            </div>
                            <div class="row"> 
                                <input type="text" id="cp_filtrar_aluno" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row"> 
                                <label for="">Data de Nascimento</label>
                            </div>
                            <div class="row"> 
                                <input type="text" id="cp_data_nascimento_matricula" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                            </div>    
                        </div>
                        <div class="form-group">
                            <div class="row"> 
                                <label for="">Responsável</label>
                            </div>
                            <div class="row"> 
                                <input type="text" id="cp_responsavel_matricula" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row"> 
                                <label for="">Status do aluno</label>
                            </div>
                            <div class="row"> 
                            <select class="custom-selectd" id="alunostatusreserva_id" multiple >
                                <!--<option value="0" selected >Todos</option>-->
                                <?php
                                foreach ($status as $sta) {
                                    echo "<option value='{$sta['id']}'>{$sta['status_descr']}</option>";
                                }
                                ?>
                            </select>
                            </div>    
                        </div>
                        <div class="form-group" style="text-align: center">
                            <button onclick="findAlunoMatricular(0)" type="button" class="btn btn-outline-success col-md-4 col-sm-12">Pesquisar</button>
                            <button onclick="getForm('app/aluno/lista_matricular.php')" type="button" class="btn btn-outline-success col-md-4 col-sm-12">Limpar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" style="background:dimgray">
                  <h4 style="color: white">Lista de Alunos</h4>
            </div>
            <div id="div_table">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/jquery.multi-select.js"></script>
<script>
    if (screen.width <= 582) {
        $('.smart-phone').hide();
    }
    findAlunoMatricular(<?php echo $paginacao ?>);
    <!-- Multi-select - Tela de administração  Início--> 
    $(function(){
        $('#alunostatusreserva_id').multiSelect();
    });
    $('#alunostatusreserva_id').multiSelect({
            noneText: 'Todos',
            presets: [
                {
                    name: 'Todos',
                    options: []
                },
                {
                    name: 'Matriculado',
                    options: ['8']
                },
                {
                    name: 'Não Matriculado',
                    options: ['1','2','3','4','5','6','7','2','9','10','11','12']
                }
            ]
        });
<!-- Multi-select - Tela de administração  Fim--> 

</script>