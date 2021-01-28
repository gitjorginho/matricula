<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('conexao.php');
$conexao = new Conexao();
$conn = $conexao->conn();

if (isset($_POST['vch_nome_aluno'])) {

    $nome_aluno = trim($_POST['vch_nome_aluno']);
    $data_nasc = dateToDatabase(trim($_POST['vch_data_nasc']));
    $vch_nome_resp = trim($_POST['vch_nome_resp']);
	$cod_aluno = $_POST['cod_aluno'];
}else{
	$cod_aluno = $_POST['cod_aluno'];
}

//$sql_matricula = "
//select *
//from aluno
//inner join matricula on ed60_i_aluno = ed47_i_codigo
//inner join matriculareserva on ed47_i_codigo = reserva_aluno
//where aluno.ed47_v_nome ilike '%$nome_aluno%'
//and aluno.ed47_d_nasc = '$data_nasc'
//and aluno.ed47_c_nomeresp = '$vch_nome_resp'
//";

if ($cod_aluno != ''){
	$sql_matricula = "
    select (select true from confirmacaorematricula where edu01_aluno = ed47_i_codigo) as confirmacao_rematricula, reserva.alunoreserva.*, ap.ed79_i_serie as idserie,ac.ed56_i_escola as idescola, ac.ed56_i_calendario as idcalendario, ac.ed56_i_base as idbase from reserva.alunoreserva left join escola.alunocurso ac on ed47_i_codigo = ac.ed56_i_aluno join escola.alunopossib ap on ac.ed56_i_codigo = ap.ed79_i_alunocurso
    where ed47_i_codigo  = '$cod_aluno' limit 1 ";

    $result = pg_query($conn, $sql_matricula);
    $aluno = pg_fetch_assoc($result);

    

    if (pg_num_rows($result) == 0) {

        //header('Location:index.php?not_found=1');
        //SQL para buscar alunos no banco do SGE (alunos sem código no reserva matrícula)
        $sql_aluno_sge = "select (select true 
                                    from confirmacaorematricula 
                                   where edu01_aluno = ed47_i_codigo) as confirmacao_rematricula, 
                                 a.ed47_i_codigo,
                                 ap.ed79_i_serie as idserie,
                                 ac.ed56_i_escola as idescola, 
                                 ac.ed56_i_calendario as idcalendario, 
                                 ac.ed56_i_base as idbase 
                            from escola.aluno a 
                            left join escola.alunocurso ac on a.ed47_i_codigo = ac.ed56_i_aluno 
                            join escola.alunopossib ap on ac.ed56_i_codigo = ap.ed79_i_alunocurso
                           where ed47_i_codigo  = '$cod_aluno' 
                           limit 1;";
        //Buscando
        $result = pg_query($conn, $sql_aluno_sge);

        //Caso não exista, redirecionar para header('Location:index.php?rematricula=1');
        if (pg_num_rows($result) == 0) {
            //header('Location:index.php?not_found=1');
          $_SESSION['not_found'] = true;
          header('Location:index.php');
        }
        //Caso exista, realizar as etapas seguintes e redirecionar para uma nova página que é igual a página de formulário de solicitação de rematrícula. (rematricula_update.php -> rematricula_update_SGE.php)
        //TO-DO - melhorar esse método para não existir o mesmo código em dois pontos diferentes do script.
        else
        {
            $aluno = pg_fetch_assoc($result);

            //Caso o aluno esteja tentando realizar rematrícula novamente
            if ($aluno['confirmacao_rematricula'] == true ){
                //header('Location:index.php?rematricula=1');           
                $_SESSION['codigo'] = $aluno['ed47_i_codigo'];
                //$_SESSION['codigo'] = $aluno['ed47_i_codigo'];
                $_SESSION['rematricula'] = true;
                header('Location:index.php');
                //exit(var_dump($aluno));
            }
            else
            {
                $sql_etapaescola = "select s2.ed11_i_codigo as idserie
                                  from escola.turma t,
                                       escola.turmaserieregimemat t2,
                                       escola.serieregimemat s,
                                       escola.serie s2
                                 where t.ed57_i_escola = {$aluno['idescola']}
                                   and t.ed57_i_calendario = {$aluno['idcalendario']}
                                   and t.ed57_i_base = {$aluno['idbase']}
                                   and t2.ed220_i_turma = t.ed57_i_codigo
                                   and t2.ed220_i_serieregimemat = s.ed223_i_codigo
                                   and s.ed223_i_serie = s2.ed11_i_codigo
                                 order by s2.ed11_c_descr desc
                                limit 1;";

                $result = pg_query($conn, $sql_etapaescola);

                $etapaescola = pg_fetch_assoc($result);
                if($aluno['idserie'] == $etapaescola['idserie']){
                    //header('Location:index.php?ultimaetapa=1');
                    $_SESSION['ultimaetapa'] = true; 
                    header('Location:index.php');
                }
                else
                {
                    //$_SESSION['codigo'] = $aluno['id_alunoreserva']; -> removido pois não existe código de reserva
                    $_SESSION['codigo_sge'] = $aluno['ed47_i_codigo'];
                    $_SESSION['matriculado'] = 'false';
                    $_SESSION['escola'] = 'true';
                    header('Location:rematricula_update_sge.php');
                }

            }            

        }   

    }
    
    else{
        if ($aluno['confirmacao_rematricula'] == true ){
            //header('Location:index.php?rematricula=1');           
            $_SESSION['codigo'] = $aluno['id_alunoreserva'];
            //$_SESSION['codigo'] = $aluno['ed47_i_codigo'];
            $_SESSION['rematricula'] = true;
            header('Location:index.php');

        }
        else{
            $sql_etapaescola = "
            select
                s2.ed11_i_codigo as idserie
            from
                escola.turma t,
                escola.turmaserieregimemat t2,
                escola.serieregimemat s,
                escola.serie s2
            where
                t.ed57_i_escola = {$aluno['idescola']}
                and t.ed57_i_calendario = {$aluno['idcalendario']}
                and t.ed57_i_base = {$aluno['idbase']}
                and t2.ed220_i_turma = t.ed57_i_codigo
                and t2.ed220_i_serieregimemat = s.ed223_i_codigo
                and s.ed223_i_serie = s2.ed11_i_codigo
            order by
                s2.ed11_c_descr desc
            limit 1;";
        
            $result = pg_query($conn, $sql_etapaescola);
            $etapaescola = pg_fetch_assoc($result);
            if($aluno['idserie'] == $etapaescola['idserie']){
               $_SESSION['ultimaetapa'] = true; 
               header('Location:index.php');
            }
            else{
                $_SESSION['codigo'] = $aluno['id_alunoreserva'];
                $_SESSION['matriculado'] = 'false';
                $_SESSION['escola'] = 'true';
                header('Location:rematricula_update.php');
            }   
        }
    }
}
else{//Caso o formulário seja enviado sem o código do aluno. Somente com os campos de nome, nome da mãe e data de nascimento
  //header('Location:index.php?not_found=1');
  //Mesmo select de cima ($sql_matricula) mas buscando do SGE.
  $sql_matricula_sge = "select a.ed47_i_codigo
                          from escola.aluno a
                         where a.ed47_v_nome ilike '%$nome_aluno%' 
                           and a.ed47_v_mae ilike '%$vch_nome_resp%' 
                           and a.ed47_d_nasc  = '$data_nasc' 
                         limit 1;";
                         //die($sql_matricula_sge);
  //Buscando
  $result = pg_query($conn, $sql_matricula_sge);

  //Caso encontre, levar para a página nova de edição (rematricula_update.php -> rematricula_update_SGE.php)
  if (pg_num_rows($result) >= 1)
  {
      $aluno = pg_fetch_assoc($result);

      $sql_aluno_sge = "select (select true 
                                  from confirmacaorematricula 
                                 where edu01_aluno = ed47_i_codigo) as confirmacao_rematricula, 
                               a.ed47_i_codigo,
                               a.ed47_v_nome,
                               ap.ed79_i_serie as idserie,
                               ac.ed56_i_escola as idescola, 
                               ac.ed56_i_calendario as idcalendario, 
                               ac.ed56_i_base as idbase 
                          from escola.aluno a 
                          left join escola.alunocurso ac on a.ed47_i_codigo = ac.ed56_i_aluno 
                          join escola.alunopossib ap on ac.ed56_i_codigo = ap.ed79_i_alunocurso
                         where ed47_i_codigo  = {$aluno['ed47_i_codigo']} 
                         limit 1;";
      //Buscando
      $result = pg_query($conn, $sql_aluno_sge);

      //Caso não exista, redirecionar para header('Location:index.php?rematricula=1');
      if (pg_num_rows($result) == 0) {
          //header('Location:index.php?not_found=1');
          $_SESSION['not_found'] = true;
          header('Location:index.php');
      }
      //Caso exista, realizar as etapas seguintes e redirecionar para uma nova página que é igual a página de formulário de solicitação de rematrícula. (rematricula_update.php -> rematricula_update_SGE.php)
      //TO-DO - melhorar esse método para não existir o mesmo código em dois pontos diferentes do script.
      else
      {
          $aluno = pg_fetch_assoc($result);


          //Caso o aluno esteja tentando realizar rematrícula novamente
          if ($aluno['confirmacao_rematricula'] == true ){  
              //exit(var_dump($aluno));    
              $_SESSION['codigo_sge'] = $aluno['ed47_i_codigo'];
              $_SESSION['rematricula'] = true;
              header('Location:index.php');
          }
          else
          {
            $sql_aluno_apto_para_rematricula = "select (case 
                                                        when ed60_i_codigo = null 
                                                        then false 
                                                          else true 
                                                        end) aptomatricula
                                                  from matricula 
                                                 inner join turma on ed57_i_codigo = ed60_i_turma 
                                                 inner join calendario on ed57_i_calendario = ed52_i_codigo
                                                 where ed60_i_aluno = {$aluno['ed47_i_codigo']}
                                                   and ed52_i_ano = 2020 
                                                   and ed60_c_situacao = 'MATRICULADO'
                                                   and ed60_c_concluida = 'N'
                                                 limit 1;";
            $result = pg_query($conn, $sql_aluno_apto_para_rematricula);
            $apto_para_matricula = pg_fetch_assoc($result);

            if ($apto_para_matricula['aptomatricula'] == true)
            {
              $sql_etapaescola = "select s2.ed11_i_codigo as idserie,
                                         s2.ed11_c_descr,
                                         e.ed18_c_nome
                                    from escola.turma t,
                                         escola.turmaserieregimemat t2,
                                         escola.serieregimemat s,
                                         escola.serie s2,
                                         escola.escola e
                                   where t.ed57_i_escola = {$aluno['idescola']}
                                     and t.ed57_i_calendario = {$aluno['idcalendario']}
                                     and t.ed57_i_base = {$aluno['idbase']}
                                     and t2.ed220_i_turma = t.ed57_i_codigo
                                     and t2.ed220_i_serieregimemat = s.ed223_i_codigo
                                     and s.ed223_i_serie = s2.ed11_i_codigo
                                     and e.ed18_i_codigo = t.ed57_i_escola
                                   order by s2.ed11_c_descr desc
                                  limit 1;";

              $result = pg_query($conn, $sql_etapaescola);

              $etapaescola = pg_fetch_assoc($result);
              if($aluno['idserie'] == $etapaescola['idserie']){
                  //header('Location:index.php?ultimaetapa=1');
                  $_SESSION['ultimaetapa'] = true;
                  $_SESSION['codigo_sge'] = $aluno['ed47_i_codigo'];//código do aluno
                  $_SESSION['nome_aluno'] = $aluno['ed47_v_nome'];//nome do aluno
                  $_SESSION['etapa'] = $etapaescola['ed11_c_descr'];//etapa identificada do aluno
                  $_SESSION['unidade_escolar'] = $etapaescola['ed18_c_nome'];//unidade escolar 
                  header('Location:index.php');
              }
              else
              {
                  //$_SESSION['codigo'] = $aluno['id_alunoreserva']; -> removido pois não existe código de reserva
                  $_SESSION['codigo_sge'] = $aluno['ed47_i_codigo'];
                  $_SESSION['matriculado'] = 'false';
                  $_SESSION['escola'] = 'true';
                  header('Location:rematricula_update_sge.php');
              }
            }
            else
            {
              $_SESSION['naoapto'] = true; 
              header('Location:index.php');
            }

            //$_SESSION['codigo_sge'] = $aluno['ed47_i_codigo'];
            //$_SESSION['matriculado'] = 'false';
            //$_SESSION['escola'] = 'true';
            //header('Location:rematricula_update_sge.php');            
          }
      }      
  }
  else
  {//aluno não encontrado na base
      $_SESSION['not_found'] = true;
      header('Location:index.php');
  }
    
    
}//fim else -> caso id do formulário seja vazio.

function dateToDatabase($date)
{
    $date = explode('/', $date);
    //$date_to_database = "$date[0]-$date[1]-$date[2]";
    $date_to_database = "$date[2]-$date[1]-$date[0]";
    //var_dump($date_to_database);
    return $date_to_database;
}