<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once('../classe/Conn.php');
Conn::conect();


//$sql_seg = "select trim(ed31_c_descr) as ed31_c_descr, sum (ed336_vagas) as qtde_vagas
//from turma
//inner join base on ed31_i_codigo = ed57_i_base
//inner join calendario on ed52_i_codigo = ed57_i_calendario
//inner join cursoedu on ed29_i_codigo = ed31_i_curso
//inner join escola on ed18_i_codigo = ed57_i_escola
//--inner join turmaserieregimemat on ed57_i_codigo = ed220_i_turma
//		 --inner join serieregimemat on ed220_i_serieregimemat = ed223_i_codigo
//		 --inner join serie on ed223_i_serie = ed11_i_codigo
//		 inner join turmaturnoreferente on turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
//		 --inner join turno on turno.ed15_i_codigo = turmaturnoreferente.ed336_turnoreferente
//		 where calendario.ed52_i_ano = 2020 and ed18_i_codigo NOT IN (101,45,20,92,87,47,19,70,73,84,40,32,17,63,18,60)
//		 group by trim(ed31_c_descr)
//		 order by trim(ed31_c_descr)";
//
//$stmt = Conn::$conexao->prepare($sql_seg);
//$stmt->execute();
//$segmentos = $stmt->fetchALL();
//

//$sql_seg_inter = "select trim(ed31_c_descr) as segmento ,count (distinct (ed60_i_aluno)) as n
//		from turma
//		inner join base on ed31_i_codigo = ed57_i_base
//		inner join matricula on ed60_i_turma = ed57_i_codigo
//		inner join calendario on ed52_i_codigo = ed57_i_calendario
//		inner join cursoedu on ed29_i_codigo = ed31_i_curso
//		inner join escola on ed18_i_codigo = ed57_i_escola
//  		--inner join turmaserieregimemat on ed57_i_codigo = ed220_i_turma
//		 --inner join serieregimemat on ed220_i_serieregimemat = ed223_i_codigo
//		 --inner join serie on ed223_i_serie = ed11_i_codigo
//		 inner join turmaturnoreferente on turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
//		 --inner join turno on turno.ed15_i_codigo = turmaturnoreferente.ed336_turnoreferente
//		 where calendario.ed52_i_ano = 2020
//		 and ed60_c_situacao in ('MATRICULADO', 'TROCA DE TURMA', 'TRANSFERENCIA FORA', 'TRANSFERENCIA REDE')
//		 and ed18_i_codigo NOT IN (101,45,20,92,87,47,19,70,73,84,40,32,17,63,18,60)
//		 group by trim(ed31_c_descr)
//		 order by trim(ed31_c_descr)";
//
//                 $stmt = Conn::$conexao->prepare($sql_seg_inter);
//                 $stmt->execute();
//                 $segmento_interno = $stmt->fetchALL();
//
//                    $arr_seg_interno = [];
//                    foreach ($segmento_interno as $seg_i){
//                          $arr_seg_interno[$seg_i['segmento']] = $seg_i['n'];
//                    }


$sql_seg = "
   select 
ed31_c_descr as segmento,
( 
select coalesce(sum(ed336_vagas),0) from turma
join turmaturnoreferente on ed336_turma = ed57_i_codigo 
join calendario on ed57_i_calendario = ed52_i_codigo
join base as b on b.ed31_i_codigo = ed57_i_base
where b.ed31_c_descr = base.ed31_c_descr and  ed52_i_ano = '2020'  
) as vagas, 
(
select count(*) as matriculados from turma
join matricula on ed60_i_turma = ed57_i_codigo
join calendario on ed57_i_calendario = ed52_i_codigo
join base as b on b.ed31_i_codigo = ed57_i_base
where b.ed31_c_descr = base.ed31_c_descr and  ed52_i_ano = '2020'  and ed60_c_situacao = 'MATRICULADO' and ed60_d_datamatricula >= '2020-01-01'
) as matriculados
from escola 
join escolabase on ed77_i_escola = ed18_i_codigo
join base on ed31_i_codigo = ed77_i_base
where   ed31_c_descr <> 'AEE' 
group by ed31_c_descr 
order by ed31_c_descr
";
//ed18_i_codigo NOT IN (101,45,20,92,87,47,19,70,73,84,40,32,17,63,18,60) and
$stmt = Conn::$conexao->prepare($sql_seg);
$stmt->execute();
$segmentos = $stmt->fetchALL();

//die(var_dump($segmentos))

?>



<script>
    title('Relatorio Por Segmento');
    subTitle1('Aluno');
    subTitle2('Relatório Por Segmento');
</script>
<div id="accordion">

    <?php
     $acordion = 0;

     foreach ($segmentos as $segmento) {  ?>

         <?php
         $aluno_reservado = 0;
         switch (trim($segmento['segmento'])){
             case'ANOS INICIAIS':

                 $sql_reservado ="
                 select count(*) as reservado from reserva.alunoreserva
                    join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                    where ed221_i_serie in (24,23,25,29,30)
                 ";
                 $stmt = Conn::$conexao->prepare($sql_reservado);
                 $stmt->execute();
                 $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
                 $aluno_reservado = $aluno_reservado['reservado'];
                 break;
             case'ANOS FINAIS':

                 $sql_reservado ="
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where ed221_i_serie in (33,34,35,36)
                 ";
                 $stmt = Conn::$conexao->prepare($sql_reservado);
                 $stmt->execute();
                 $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
                 $aluno_reservado = $aluno_reservado['reservado'];
                 break;

             case'PRE-ESCOLA':
//                 $sql_reservado ="
//                 select count(*) as reservado from reserva.alunoreserva
//                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva
//                 where ed221_i_serie in (31,32)
//                 ";
//                 $stmt = Conn::$conexao->prepare($sql_reservado);
//                 $stmt->execute();
//                 $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
//                 $aluno_reservado = $aluno_reservado['reservado'];
                 $aluno_reservado = 0;
                 break;

             case'PRÉ-ESCOLA':
                 $sql_reservado ="
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where ed221_i_serie in (31,32)
                 ";
                 $stmt = Conn::$conexao->prepare($sql_reservado);
                 $stmt->execute();
                 $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
                 $aluno_reservado = $aluno_reservado['reservado'];
                 break;
             case'CRECHE':
                 $sql_reservado ="
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where ed221_i_serie in (26,27,28)
                 ";
                 $stmt = Conn::$conexao->prepare($sql_reservado);
                 $stmt->execute();
                 $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
                 $aluno_reservado = $aluno_reservado['reservado'];
                 break;

             case'EJA - ANOS INICIAIS':
                 $sql_reservado ="
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where ed221_i_serie in (37,38,39)
                 ";
                 $stmt = Conn::$conexao->prepare($sql_reservado);
                 $stmt->execute();
                 $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
                 $aluno_reservado = $aluno_reservado['reservado'];
                 break;
             case'EJA - ANOS FINAIS':
                 $sql_reservado ="
                 select count(*) as reservado from reserva.alunoreserva
                 join reserva.escolareserva  on reserva.escolareserva.id_alunoreserva = reserva.alunoreserva.id_alunoreserva    
                 where ed221_i_serie in (40,41)
                 ";
                 $stmt = Conn::$conexao->prepare($sql_reservado);
                 $stmt->execute();
                 $aluno_reservado = $stmt->fetch(PDO::FETCH_ASSOC);
                 $aluno_reservado = $aluno_reservado['reservado'];
                 break;
         }


         ?>






        <div class="card">
            <div class="card-header" id="headingTwo<?php echo $acordion ?>">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo<?php echo $acordion ?>" aria-expanded="false" aria-controls="collapseTwo">
                        <?php echo trim($segmento['segmento']); ?>
                    </button>
                </h5>
            </div>
            <div id="collapseTwo<?php echo $acordion ?>" class="collapse" aria-labelledby="headingTwo<?php echo $acordion ?>" data-parent="#accordion">
                <div class="card-body">
                   <table class="table text-center">
                        <thead>
                        <tr>
							<td><b>Vagas Ofertadas</b></td>
                            <td><b>Matrículas</b></td>                            
                            <td><b>Alunos Reservados</b></td>
                            <td><b>Vagas Disponíveis</b></td>
                        </tr>

                        </thead>
                        <tbody>
                        <tr>                            
                            <td><?php echo $segmento['vagas'] ?></td>
							<td><?php echo $segmento['matriculados'] ?></td>
							<td><?php echo $aluno_reservado ?></td>
                            <td><?php echo $segmento['vagas'] - ($segmento['matriculados'] + $aluno_reservado ) ; ?> </td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php $acordion++; } ?>

</div>
