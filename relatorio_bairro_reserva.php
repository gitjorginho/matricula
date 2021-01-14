<?php
   require_once('header.php');
   require_once('conexao.php');
   
   $conexao = new Conexao();
   $conn = $conexao->conn();
   
  /* $sql_turmas_aluno = "
   select ed47_v_bairro, j13_codi, ed47_i_localidade from matriculareserva
   inner join aluno on  ed47_i_codigo = reserva_aluno
   inner join alunoruasbairrocep on j76_i_aluno = reserva_aluno
   inner join cadastro.ruasbairrocep as rbc on rbc.j32_i_codigo = j76_i_ruasbairrocep
   inner join ruascep rc on rbc.j32_ruascep = rc.j29_codigo
   inner join ruasbairro rb on rbc.j32_ruasbairro = rb.j16_codigo
   inner join ruas r on rb.j16_lograd = r.j14_codigo
   inner join bairro b on rb.j16_bairro = b.j13_codi
   inner join turma on  ed57_i_codigo = reserva_turma
   inner join escola on ed18_i_codigo = ed57_i_escola 
   group by ed47_v_bairro, j13_codi, ed47_i_localidade
   order by j13_descr 
   ";*/
   $sql_turmas_aluno = "
   select ed47_v_bairro from matriculareserva
   inner join aluno on  ed47_i_codigo = reserva_aluno   
   group by ed47_v_bairro
   order by ed47_v_bairro 
   ";
   $result = pg_query($conn, $sql_turmas_aluno);
   //echo $sql_turmas_aluno;
   if (pg_num_rows($result) == 0) {
       $escolas = 0;
   } else {
       $escolas = pg_fetch_all($result);
   }   
   ?>
<!doctype html>
<html lang="en">
   <head>
      <meta charset="ISO-8859-1">
      <meta name="viewport"
         content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Document</title>
   </head>
   <body>
      <div class="centr">
      <br>
      <h2 class="text-center">Lista de Alunos Inscritos por Bairro</h2>
      <?php if ($escolas == 0) {
         echo "<br><br><br><br><h4 class='text-center'>Não existe registro</h4> ";
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
                  <?php echo $escola['ed47_v_bairro'] ?>
                  </button>
               </h5>
            </div>
            <div id="collapseOne<?php echo $id; ?>" class="collapse hide"
               aria-labelledby="headingOne<?php echo $id; ?>" data-parent="#accordion">
               <div class="card-body">
                  <!--Acordion interno-->
                  <div id="accordion<?php echo $id; ?>">
                     <?php					 
                        $turmas = loadTurmas($conn, $escola['ed47_v_bairro']);
                        
                        
                        if ($turmas != 0) {
                        
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
                              <?php echo "<b>Localidade:</b> {$turma['loc_v_nome']} " ?>
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
                                          <th>Aluno</th>
										  <th>Série</th>
                                       </tr>
                                       <?php
                                          $alunos = loadAlunos($conn, $turma['loc_i_cod']);
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
                                             <small><?php echo $aluno['ed47_v_nome'] ?></small>
                                          </td>
										  <td>
                                             <small><?php echo $aluno['ed11_c_descr'] ?></small>
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
<?php
   function loadTurmas($conn, $codigo_escola)
   {
   
   $sql_turmas_escola = "
   select ed47_i_localidade, loc_i_cod, loc_v_nome from matriculareserva
   inner join aluno on ed47_i_codigo = reserva_aluno
   inner join territorio.localidade as l on l.loc_i_cod = ed47_i_localidade 
   where ed47_v_bairro = '$codigo_escola'
   group by ed47_i_localidade, loc_i_cod, loc_v_nome
   ";
   
       $result = pg_query($conn, $sql_turmas_escola);
       $turmas = pg_fetch_all($result);
   
       if (pg_num_rows($result) == 0) {
           return 0;
       }
   	//echo $sql_turmas_escola;
       return $turmas;
   }
   
   function loadAlunos($conn, $codigoTurma)
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
       $sql = "
   select ed47_v_nome, loc_i_bairro, loc_i_cod, loc_v_nome, ed47_i_localidade, reserva_turma, ed11_c_descr from matriculareserva
   inner join aluno on ed47_i_codigo = reserva_aluno
   inner join serie on reserva_turma = ed11_i_codigo
   inner join territorio.localidade as l on l.loc_i_cod = ed47_i_localidade 
   where ed47_i_localidade = '$codigoTurma'
   group by loc_i_bairro, loc_i_cod, loc_v_nome, ed47_v_nome, ed47_i_localidade, reserva_turma, ed11_c_descr
   order by ed11_c_descr, ed47_v_nome
   ";   
       //echo($sql);
       $result = pg_query($conn, $sql);
       $alunos = pg_fetch_all($result);
   
       if (pg_num_rows($result) == 0) {
           return 0;
       }
       return $alunos;
   }
   
   require_once('footer.php');
   ?>