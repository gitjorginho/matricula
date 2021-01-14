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


?>
<script>
    title('Relatorio Alunos Por Bairro');
    subTitle1('Aluno');
    subTitle2('Relatório');
</script>
<?php
//$sql_turmas_aluno = "
//select ed47_v_bairro from matriculareserva
//inner join aluno on  ed47_i_codigo = reserva_aluno
//group by ed47_v_bairro
//order by ed47_v_bairro
//limit 10
//";

$sql_turmas_aluno = "
select ed47_v_bairro from reserva.alunoreserva
group by ed47_v_bairro
order by ed47_v_bairro
";


$stmt = Conn::$conexao->prepare($sql_turmas_aluno);
$stmt->execute();
$escolas = $stmt->fetchALL();

//$result = pg_query($conn, $sql_turmas_aluno);
//echo $sql_turmas_aluno;
if (count($escolas) == 0) {
    $escolas = 0;
}
//} else {
//$escolas = pg_fetch_all($result);
//}
?>
<!--<!doctype html>-->
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header" style="background:green">
                    <h4 style="color: white">Relatório Alunos Por Bairro </h4>
                </div>
              <div class="card-body">
                  <html lang="en">
                  <head>
                      
                      <meta name="viewport"
                            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                      <meta http-equiv="X-UA-Compatible" charset="" content="ie=edge">
                      <title>Document</title>
                  </head>
                  <body>
                  <div class="centr">
                      <br>
                      <!--    <h2 class="text-center">Lista de Alunos Inscritos por Bairro</h2>-->
                      <?php if ($escolas == 0) {
                          echo "<br><br><br><br><h4 class='text-center'>NÃ£o existe registro</h4> ";
                      } ?>
                      <div id="accordion">
                          <?php
                          if ($escolas != 0) {
                              $id = 0;
                              foreach ($escolas as $escola) {
                                  $id++;
                                  ?>
                                  <div class="card">
                                      <div class="card-header" id="headingOne<?php echo $id; ?>">
                                          <h5 class="mb-0">
                                              <button class="btn btn-link" data-toggle="collapse"
                                                      data-target="#collapseOne<?php echo $id; ?>"
                                                      aria-expanded="true"
                                                      aria-controls="collapseOne<?php echo $id; ?>">
                                                  <?php echo trim($escola['ed47_v_bairro']) ?>
                                              </button>
                                          </h5>
                                      </div>
                                      <div id="collapseOne<?php echo $id; ?>" class="collapse hide"
                                           aria-labelledby="headingOne<?php echo $id; ?>" data-parent="#accordion">
                                          <div class="card-body">
                                              <!--Acordion interno-->
                                              <div id="accordion<?php echo $id; ?>">
                                                  <?php
                                                  $turmas = loadTurmas($escola['ed47_v_bairro']);
                                                  if ($turmas != 0) {
                                                      //$id=0;
                                                      $id_interno = 0;
                                                      foreach ($turmas as $turma) {
                                                          $id_interno++;
                                                          $id_ac_interno = $id . $id_interno;
                                                          ?>
                                                          <div class="card">
                                                              <div class="card-header" id="headingOne<?php echo $id_ac_interno ?>">
                                                                  <h5 class="mb-0">
                                                                      <button class="btn btn-link" data-toggle="collapse"
                                                                              data-target="#collapseOne<?php echo $id_ac_interno ?>"
                                                                              aria-expanded="true"
                                                                              aria-controls="collapseOne<?php echo $id_ac_interno ?>">
                                                                          <?php echo "<b>Localidade:</b> ".trim($turma['loc_v_nome']) ?>
                                                                      </button>
                                                                  </h5>
                                                              </div>
                                                              <div id="collapseOne<?php echo $id_ac_interno ?>" class="collapse"
                                                                   aria-labelledby="headingOne<?php echo $id_ac_interno ?>"
                                                                   data-parent="#accordion<?php echo $id; ?>">
                                                                  <div class="card-body">


                                                                      <!--Corpo do card do acordion 1 TURMAS -->
                                                                      <table class="table">
                                                                          <tr>
                                                                              <th></th>
                                                                              <th><small>Aluno</small></th>
																			  <th><small>Data de Nascimento</small></th>
                                                                              <th><small>Série</small></th>
																			  <th><small>Mãe</small></th>
																			  <th><small>Responsável</small></th>
																			  <th><small>CPF Responsável</small></th>
																			  <th><small>Telefone</small></th>
																			  <th><small>Orgão Publico</small></th>																			  
																			  
                                                                          </tr>
                                                                          <?php
                                                                          $alunos = loadAlunos($turma['loc_i_cod']);
                                                                          if ($alunos != 0) {

                                                                              $num_aluno = 0;
                                                                              foreach ($alunos as $aluno) {
                                                                                  $num_aluno++;
                                                                                  ?>
                                                                                  <tr>
                                                                                      <th>
                                                                                          <small><?php echo $num_aluno ?></small>
                                                                                      </th>
                                                                                      <td>
                                                                                          <small><?php echo trim($aluno['ed47_v_nome']) ?></small>
                                                                                      </td>
																					    <td>
                                                                                          <small><?php 																					  
																						  $nas = date('d-m-Y', strtotime($aluno['ed47_d_nasc']));	
																						  echo trim($nas) ?></small>
                                                                                      </td>	
                                                                                      <td>
                                                                                          <small><?php echo trim($aluno['ed11_c_descr']) ?></small>
                                                                                      </td>
																					   <td>
                                                                                          <small><?php echo trim($aluno['ed47_v_mae']) ?></small>
                                                                                      </td>
																					   <td>
                                                                                          <small><?php echo trim($aluno['ed47_c_nomeresp']) ?></small>
                                                                                      </td>
																					   <td>
                                                                                          <small><?php echo trim($aluno['ed47_v_cpf']) ?></small>
                                                                                      </td>
																					  <td>
                                                                                          <small><?php echo trim($aluno['ed47_v_telef']) ?></small>
                                                                                      </td>
																					  <td>
                                                                                          <small><?php echo trim($aluno['vch_orgaopublico']) ?></small>
                                                                                      </td>
																					  
																					  
																																										  
                                                                                  </tr>
                                                                              <?php }
                                                                          } ?>
                                                                      </table>
                                                                      <!--END TURMAS-->
                                                                  </div>
                                                              </div>
                                                          </div>
                                                          <!--fim card turmas-->
                                                      <?php }
                                                  } ?>
                                                  <div>
                                                  </div>
                                                  <div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              <?php }
                          } ?>
                      </div>
                  </body>
                  </html>
              </div>
            </div>
        </div>
    </div>

<?php
function loadTurmas($bairro)
{
    global $conn;
//    $sql_turmas_escola = "
//   select ed47_i_localidade, loc_i_cod, loc_v_nome from matriculareserva
//   inner join aluno on ed47_i_codigo = reserva_aluno
//   inner join territorio.localidade as l on l.loc_i_cod = ed47_i_localidade
//   where ed47_v_bairro = '$codigo_escola'
//   group by ed47_i_localidade, loc_i_cod, loc_v_nome
//   ";
    $sql_turmas_escola = "
   select ed47_i_localidade, loc_i_cod, loc_v_nome from reserva.alunoreserva
   inner join territorio.localidade as l on l.loc_i_cod = ed47_i_localidade 
   where ed47_v_bairro = '$bairro'
   group by ed47_i_localidade, loc_i_cod, loc_v_nome
   ";



    $stmt = Conn::$conexao->prepare($sql_turmas_escola);
    $stmt->execute();
    $turmas = $stmt->fetchALL();

    if (count($turmas) == 0) {
        return 0;
    }
    //echo $sql_turmas_escola;
    return $turmas;
}

function loadAlunos($codigoTurma)
{
    //    $sql="
    //select
    //
    //(select mmr_dataregistro from monitoramentomatriculareserva
    //where mmr_acao = 'Inseriu' and mmr_idaluno = ed47_i_codigo
    //order by mmr_dataregistro desc
    //limit 1
    //) as dataregistro,
    //
    //(select mmr_dataregistro from monitoramentomatriculareserva
    //where mmr_acao = 'Atualizou' and mmr_idaluno = ed47_i_codigo
    //order by mmr_dataregistro desc
    //limit 1
    //) as dataatualizacao,
    //
    //ed60_c_situacao, trim(ed47_v_nome) as aluno
    //from matriculareserva
    //inner join aluno on ed47_i_codigo = reserva_aluno
    //left join matricula on ed60_i_aluno = ed47_i_codigo
    //where reserva_turma = $codigoTurma order by reserva_id
    //";

    //die($sql);
    //    $sql = "
    //        select ed60_c_situacao, trim(ed47_v_nome) as aluno from matriculareserva
    //    inner join aluno on ed47_i_codigo = reserva_aluno
    //    left join matricula on ed60_i_aluno = ed47_i_codigo
    //    where reserva_turma = $codigoTurma
    //    order by reserva_id
    //    ";
//    $sql = "
//   select ed47_v_nome, loc_i_bairro, loc_i_cod, loc_v_nome, ed47_i_localidade, reserva_turma, ed11_c_descr from matriculareserva
//   inner join aluno on ed47_i_codigo = reserva_aluno
//   inner join serie on reserva_turma = ed11_i_codigo
//   inner join territorio.localidade as l on l.loc_i_cod = ed47_i_localidade
//   where ed47_i_localidade = '$codigoTurma'
//   group by loc_i_bairro, loc_i_cod, loc_v_nome, ed47_v_nome, ed47_i_localidade, reserva_turma, ed11_c_descr
//   order by ed11_c_descr, ed47_v_nome
//   ";


    $sql = "
    select ed47_v_nome, ed47_d_nasc, vch_orgaopublico, ed47_v_sexo, ed47_v_mae, ed47_v_telef, ed47_c_nomeresp, ed47_v_cpf, loc_i_bairro, loc_i_cod, loc_v_nome, ed47_i_localidade, ed221_i_serie, ed11_c_descr from reserva.alunoreserva 
    inner join reserva.escolareserva on escolareserva.id_alunoreserva =  alunoreserva.id_alunoreserva 
    inner join serie on ed11_i_codigo = ed221_i_serie
    inner join territorio.localidade as l on l.loc_i_cod = ed47_i_localidade 
    where ed47_i_localidade = '$codigoTurma'
    group by ed47_d_nasc, vch_orgaopublico, ed47_v_sexo, ed47_v_mae, ed47_v_telef, ed47_c_nomeresp, ed47_v_cpf, loc_i_bairro, loc_i_cod, loc_v_nome, ed47_v_nome, ed47_i_localidade, ed221_i_serie, ed11_c_descr
    order by ed11_c_descr, ed47_v_nome
    ";

     //die($sql);
    //echo($sql);
//    $result = pg_query($conn, $sql);
//    $alunos = pg_fetch_all($result);


    $stmt = Conn::$conexao->prepare($sql);
    $stmt->execute();
    $alunos = $stmt->fetchALL();

    if (count($alunos) == 0) {
        return 0;
    }
    return $alunos;
}
