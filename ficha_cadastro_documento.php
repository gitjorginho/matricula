<?php
session_start();
require_once('header.php');

$passo = $_SESSION['passo'];
if (!in_array(1, $passo)) {
    header('Location:index.php');
}

$nome_aluno     = strtoupper($_SESSION['vch_nome']);
$data_nascimento= $_SESSION['sdt_nascimento'];
$nome_mae       = $_SESSION['vch_mae'];

include_once("conexao.php");
$conexao = new Conexao();
$conn = $conexao->conn();

if (isset($_POST['vch_endereco'])) {
    if (!in_array(2, $passo)) {
        array_push($passo, 2);
        $_SESSION['passo'] = $passo;
    }
    $_SESSION = array_merge($_SESSION, $_POST);
    header('Location: ficha_cadastro_opcao.php');
}

$sql_documento = "select id_documentoreserva, TRIM(ed02_c_descr) AS ed02_c_descr,obrigatorio,frenteverso  from reserva.documentoreserva dr
join documentacao as ds on dr.ed02_i_codigo = ds.ed02_i_codigo";
$result = pg_query($conn,$sql_documento);
$documentos =  pg_fetch_all($result);

?>
<div>
    <br>
    <h2 class="text-center">Documentos Necessários</h2>
    <form id="ficha" enctype="multipart/form-data" method="post" action="ficha_cadastro_upload_imagem_doc_proc.php">
          <?php 
            foreach($documentos as $documento){?>
          
            <?php if($documento['frenteverso'] == 'S'){?>
                <br>
                <div class="card card-body">
                <div class="form-row">
                        <div class="col-md-6">
                            <label for=""><?php echo $documento['ed02_c_descr'].' (FRETE)'  ?></label>
                            <input type="file" name="<?php echo $documento['id_documentoreserva'].'-'.$documento['ed02_c_descr'].'-FRENTE-'?>" <?php echo ($documento['obrigatorio'] == 'S' ? 'required' : '')?> class="form-control" accept="image/*,.pdf" data-max-size="32154">
                        </div>  
                        <div class="col-md-6">
                            <label for=""><?php echo $documento['ed02_c_descr'].' (VERSO)'  ?></label>
                            <input type="file" name="<?php echo $documento['id_documentoreserva'].'-'.$documento['ed02_c_descr'].'-VERSO-'?>" <?php echo ($documento['obrigatorio'] == 'S' ? 'required' : '')?> class="form-control" accept="image/*,.pdf" data-max-size="32154">            
                        </div>  
                        
                </div> 
                </div>
                

            <?php }else{?>
                <br>
                <div class="card card-body">
                    <div class="form-row">
                            <div class="col-md-12">
                                <label for=""><?php echo $documento['ed02_c_descr']  ?></label>
                                <input type="file" name="<?php echo $documento['id_documentoreserva'].'-'.$documento['ed02_c_descr'].'-UNICO-'?>" <?php echo ($documento['obrigatorio'] == 'S' ? 'required' : '')?> class="form-control" accept="image/*,.pdf" data-max-size="32154">            
                            </div>  
                            
                    </div> 
                </div>
            <?php } ?>

           

           <?php } ?>


           
      <div class="form-group">
            <div class="card-body">
                <a class="btn btn-secondary col-md-2" href="ficha_cadastro_endereco.php">Voltar</a>
                <div class="d-md-none" style="margin:10px;"></div>
                <button type="button" id="ProsseguirEndereco" class="btn btn-success col-md-2" onClick="Javascript:GravarForm(document.Form);"> Prosseguir</button>
            </div>
      </div>
    </form>

<script type="text/javascript">
    $(document).ready(function () {
        $("#btndn").removeClass("disabled").addClass("active");
    });

    function GravarForm(frm)
    {
        $result = valida();
        if ($result != false) {
            
            
            
            
            
            
            
            document.forms["ficha"].submit();
        }
    }

    jQuery(function ($) {
        $("#vch_telefone").mask("(99) 9 9999-9999");
    });

    function valida()
    {
        var isOk = true;
        $('input[type=file]').each(function(index){
            if(typeof this.files[index] !== 'undefined'){
                alert('entrei');
                var maxSize = parseInt($(this).attr('max-size'),10),
                size = this.files[0].size;
                isOk = maxSize > size;
                if(isOk == true){
                    alert('teste');
                    $("#msg").trigger("click");
                    $("#msg_text").text("Tamanho do documento maior que 4Mb");
                    $(this).style.borderColor = 'red';
                }
                return isOk;
            }
        });
        return isOk;
    }
</script>


<?php
require_once('footer.php');
?>