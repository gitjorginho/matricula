<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../classe/Conn.php');
Conn::conect();

//verifica se usuario ta logado
if(!isset($_SESSION['id_usuario'])){
    echo 'expirou';              
    die();
}

$sql_atualiza = 'select reserva.atualizaCodigoGAluno()'; 
$stmt = Conn::$conexao->prepare($sql_atualiza);
$stmt->execute();


/* 
$sql_escolas = "
  select ed18_i_codigo, trim(ed18_c_nome) as escola 
  from escola 
  where ed18_i_codigo NOT IN (101,45,20,92,87,47,19,70,73,84,40,32,17,63,18,60)
  order by ed18_c_nome 

";
*/ 
$sql_escolas = "select ed18_i_codigo, trim(ed18_c_nome) as escola
                from escola E
                inner join configuracoes.db_depart DD on 
                E.ed18_i_codigo = DD.coddepto 
                where limite >= now() or limite is null 
                order by ed18_c_nome";

$stmt = Conn::$conexao->prepare($sql_escolas);
$stmt->execute();
$escolas = $stmt->fetchALL();

//die(var_dump($segmentos))

?>


<script>
    title('Relatorio Por Segmento');
    subTitle1('Aluno');
    subTitle2('Relatório Por Segmento');
</script>

<div class="card col-md-8 offset-md-2">
    <div class="card-body">
        <div class="form-group">
            <label for="">Escola</label>
            <select class="custom-select" id="cp_escola">
               <option value="0">Todos</option>
            <?php foreach ($escolas as $escola){ ?>
                <option value="<?php echo $escola['ed18_i_codigo']?>"><?php echo $escola['escola']?></option>
            <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <button  id="btn_gerar_rel" class="btn btn-outline-success" onclick="gerarRelatorio()">Gerar Relatório</button>
            <img id="loading_gera_rel" style="display: none" src="img/loading.gif" width="30">
        </div>
    </div>
</div>

<div id="relatorio"></div>



<script>
    function gerarRelatorio() {

        let route = 'app/aluno/resource/rel_escola_segmento_disponiveis_div.php';
        let data = {
            escola : $('#cp_escola').val(),
        };
        $('#loading_gera_rel').show();
        $('#btn_gerar_rel').attr('disabled','disabled');
        $('#relatorio').hide()
        $.get(route, data, (response)=>{
            $('#loading_gera_rel').hide();
            $('#btn_gerar_rel').removeAttr('disabled');
            $('#relatorio').html(response).fadeIn();
        }).fail((response)=>{
            $('#loading_gera_rel').hide();
            $('#btn_gerar_rel').removeAttr('disabled');
           alert('Não foi possivel conectar ao servidor');
        });


    }
</script>
