<?php



?>
<script>
    title('Comprovante de Matricula')
    subTitle1('Aluno');
    subTitle2('Comprovante Matricula');
</script>
<div class="row">
    <div class="col"></div>
    <div class="col">
        <iframe src="app/aluno/comprovante_matricula.php?codigo_aluno=<?php echo $_GET['codigo_aluno']?>&codigo_escola=<?php echo $_GET['codigo_escola']?>" height="1000" width="900">
        </iframe>
    </div>
    <div class="col"></div>
</div>





