<?php
   require_once ('header.php');
   header("Content-Type: text/html;  charset=ISO-8859-1", true);
   
   include_once("conexao.php");
   $conexao = new Conexao();
   $conn = $conexao->conn();
   $sql_escola = ("SELECT ed18_i_codigo,ed18_c_nome FROM escola");
   
   $result = pg_query($conn, $sql_escola);
   $escolas = pg_fetch_all($result);
   
   $sql_qtd_turmas = "select count(reserva_turma)qtd_aluno_reserva , reserva_turma from matriculareserva 
   inner join aluno on ed47_i_codigo = reserva_aluno 
   group by reserva_turma";
   
   ?>
<br/>
<br/>
<p class="welcome"><b>Seja bem vindo!</b></p>
<p class="welcome">Abaixo você encontrará a lista de vagas disponíveis por escola/turma:</p>
<br/>
<div class="row">
   <div class="col-2"></div>
   <div class="col-8">
      <div id="accordion">
         <!--INICIO DO CARD  -->
         <div class="card">
            <?php
               $cont_name = 0;
               foreach ($escolas as $escola) {
                   $cont_name++;                       
               
               $sql_turma = "select turma.ed57_i_codigo, turma.ed57_c_descr, string_agg(cast(trim(ed11_c_descr) as text),',') AS ed11_c_descr,escola.ed18_c_nome from turma 
               inner join calendario on ed57_i_calendario = ed52_i_codigo
               inner join escola on ed57_i_escola = ed18_i_codigo
               inner join turmaserieregimemat on ed57_i_codigo = ed220_i_turma
               inner join serieregimemat on ed220_i_serieregimemat = ed223_i_codigo
               inner join serie on ed223_i_serie = ed11_i_codigo
               WHERE ed18_i_codigo = {$escola['ed18_i_codigo']} and calendario.ed52_i_ano = 2020 
			   group by turma.ed57_i_codigo, turma.ed57_c_descr, escola.ed18_c_nome
               ORDER BY escola.ed18_c_nome";               
               
                   $result = pg_query($conn, $sql_turma);
                   $turmas = pg_fetch_all($result);
                              
                   if (pg_num_rows($result) != 0) { ?>
            <div class="card-header" id="<?php echo 'heading' . $cont_name ?> ">
               <h5 class="mb-0">
                  <button class="btn btn-link" data-toggle="collapse"
                     data-target="<?php echo '#collapse' . $cont_name ?>"
                     aria-expanded="false" aria-controls="<?php echo 'collapse' . $cont_name ?>">
                  <?php
                     echo $escola['ed18_c_nome'];
                     ?>
                  </button>
               </h5>
            </div>
            <div id="<?php echo 'collapse' . $cont_name ?>" class="collapse hide"
               aria-labelledby="<?php echo 'heading' . $cont_name ?>"
               data-parent="#accordion">
               <?php
                  ?>
               <table class="table">
                  <thead>
                     <tr>
                        <th scope="col">Serviços</th>
                        <th scope="col" class="text-center">Vagas Disponíveis</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                        $total_vagas_disponiveis = 0;
                        
                        foreach ($turmas as $turma) {
                        
                        $rec = pg_fetch_assoc($result);
                        $codturma = $rec['ed57_i_codigo'];	
                        
                        $sql_teste = "
                        SELECT  turno.ed15_c_nome as turno, trim(turma.ed57_c_descr) AS Turma, count (distinct (aluno.ed47_v_nome)) AS qtd_alunos_matr,trim(ed11_c_descr) as etapa,ed336_vagas as vagas
                        FROM matricula
                        LEFT JOIN aluno ON aluno.ed47_i_codigo = matricula.ed60_i_aluno
                        LEFT JOIN turma ON turma.ed57_i_codigo = matricula.ed60_i_turma
                        LEFT JOIN turmaturnoreferente ON turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
                        LEFT JOIN turno ON turno.ed15_i_codigo = turmaturnoreferente.ed336_turnoreferente
                        LEFT JOIN sala ON sala.ed16_i_codigo = turma.ed57_i_sala
                        LEFT JOIN matriculaserie ON matriculaserie.ed221_i_matricula = matricula.ed60_i_codigo
                        LEFT JOIN serie ON serie.ed11_i_codigo = matriculaserie.ed221_i_serie
                        LEFT JOIN escola ON escola.ed18_i_codigo = turma.ed57_i_escola
                        LEFT JOIN calendario ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                        WHERE ed57_i_codigo = $codturma
                        GROUP BY turma.ed57_c_descr, sala.ed16_i_capacidade, turma.ed57_i_codigo,escola.ed18_c_nome,
                        turno.ed15_c_nome,calendario.ed52_c_descr,turmaturnoreferente.ed336_vagas, ed11_c_descr
                        having count (distinct (aluno.ed47_v_nome)) > 0 and  count (distinct (aluno.ed47_v_nome)) < turmaturnoreferente.ed336_vagas
                        ORDER BY escola.ed18_c_nome";
                           //echo $sql_teste;                     
                        $resultinho = pg_query($conn, $sql_teste);
                        $recla = pg_fetch_assoc($resultinho);
                        $codgeral = $recla['turno'];
                                                
                        $sql_turno = "
                        select * from turma 
                        inner join calendario on ed57_i_calendario = ed52_i_codigo
                        inner join escola on ed57_i_escola = ed18_i_codigo
                        inner join turmaserieregimemat on ed57_i_codigo = ed220_i_turma
                        inner join serieregimemat on ed220_i_serieregimemat = ed223_i_codigo
                        inner join serie on ed223_i_serie = ed11_i_codigo
                        inner join turmaturnoreferente on turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
                        inner join turno on turno.ed15_i_codigo = turmaturnoreferente.ed336_turnoreferente
                        WHERE ed57_i_codigo = $codturma and calendario.ed52_i_ano = 2020 
                        ORDER BY escola.ed18_c_nome";
                        //echo $sql_turno;
                        $rs = pg_query($conn, $sql_turno);
                        $rsla = pg_fetch_assoc($rs);
						
                        if (trim($rsla['ed15_c_nome']) == "MANHÃ 1") {
                        $turno = "Matutino";
                        }
                        if (trim($rsla['ed15_c_nome']) == "TARDE 1") {
                        $turno = "Vespertino";
                        }
                        if (trim($rsla['ed15_c_nome']) == "NOITE 1"){
                        $turno = "Noturno";
                        } 
                        if (pg_num_rows($resultinho) == 2) {
                        $turno = "Integral";
                        }
						//die (pg_num_rows($rs));
                       /* if (pg_num_rows($rs) == 2) {
                        $turno = "Integral";
                        }*/
						//die (var_dump($rsla));
                        
                        $vagas = $recla['vagas'];
						
                        if (pg_num_rows($resultinho) == 0) {
                        
                        $vagas = $rsla['ed336_vagas'];
                        }
                            $vagas_disponiveis = $vagas - $recla['qtd_alunos_matr'];
                            $total_vagas_disponiveis += $vagas_disponiveis;
                        
                        if (pg_num_rows($resultinho) == 0) {								
                        $vagas_disponiveis = $rsla['ed336_vagas'];
                        }
                            ?>
                     <tr>
                        <td style="font-size: 13px"><?php echo ' <b>Turma:</b> ' . $turma['ed57_c_descr'] . ' <b>Serie:</b> ' . $turma['ed11_c_descr'] . ' <b>Turno:</b> ' . $turno?></td>
                        <td class="text-center"><?php echo $vagas_disponiveis ?></td>
                     </tr>
                     <?php } ?>
                  <tfoot>
                     <tr>
                        <td><b>Total Vagas Disponiveis:</b></td>
                        <td class="text-center"><b><?php echo $total_vagas_disponiveis ?></b></td>
                     </tr>
                  </tfoot>
                  </tbody>
               </table>
            </div>
            <?php
               }
               }
               ?>
         </div>
         <!--FIM DO CARD  -->
      </div>
   </div>
   <div class="col-2"></div>
</div>
<?php
   require_once ('footer.php');
   ?>