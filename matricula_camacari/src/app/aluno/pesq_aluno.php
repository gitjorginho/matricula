<?php
session_start();
require_once('../classe/Aluno.php');
$aluno = new Aluno();
$alunos = $aluno->all();
?>
<script>
    title('Pesquisar Aluno Edição')
    subTitle1('Aluno');
    subTitle2('Pesquisar Aluno Edição');
</script>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" style="background:green">
                <h5 style="color: white">Pesquisar Aluno</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class=" offset-md-4 col-md-4">
                        <div class="form-group">
                            <label for="">Aluno</label>
                            <input type="text" id="cp_filtrar_aluno" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="form-group">
                            <label for="">Data Nascimento</label>
                            <input type="text" id="cp_data_nascimento_editar" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="form-group">
                            <label for="">Responsavel</label>
                            <input type="text" id="cp_responsavel_editar" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="form-group">
                            <button onclick="findAluno()" type="button" class="btn btn-outline-success col-md-4 col-sm-12">Pesquisar</button>
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
            <table class="table text-center ">
                <thead style="background: lightgray">
                <tr>
                    <th>Matricula</th>
                    <th>Aluno</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="table_alunos">
                <?php
                $qtd_aluno =0;
                foreach ($alunos as $aluno) {
                    $qtd_aluno++;
                    ?>
                    <tr>
                        <td><?php echo $aluno['ed47_i_codigo'] ?></td>
                        <td><?php echo $aluno['ed47_v_nome'] ?></td>
                        <td>
                            <button class="btn btn-outline-info" onclick="getForm('app/aluno/form_alterar_aluno.php?codigo=<?php echo $aluno['ed47_i_codigo'] ?>')">Editar</button>
                        </td>
                    </tr>
                <?php } $_SESSION['registros']= $qtd_aluno ?>
                </tbody>
                <tfoot style="background: lightgray">
                <tr>
                    <td colspan="3"><i><b>( <span id="registros"><?php  echo $_SESSION['registros']; ?></span> )Registros</b></i></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/aluno/lista_editar_aluno.js"></script>
<script>
    getRegistros();
</script>


