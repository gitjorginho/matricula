<?php
/**
 * Created by PhpStorm.
 * User: JCL-Tecnologia
 * Date: 03/10/2020
 * Time: 09:39
 */
require_once('Conn.php');

class Escola extends Conn
{

    function __construct()
    {
        self::conect();
    }
    //carrega todas as escolas
    function allEscola(){
        $sql = "
            select 
                ed18_i_codigo as codigo, 
                ed18_c_nome as escola
            from escola.escola
            inner join configuracoes.db_depart DD 
                on ed18_i_codigo = DD.coddepto 
            where 
                limite >= now() or limite is null 
            order by 
            ed18_c_nome 
            ";
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $escolas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $escolas;
    }
    /*
     *Carrega as escolas considerando a idade do aluno
     */
    function loadEscolas($data_nascimneto, $cp_serie)
    {
        //die (var_dump($_GET));
        //$data = $_SESSION['sdt_nascimento'];
        //$data = date("d/m/Y", strtotime($data_session));
        // Separa em dia, mês e ano
        list($dia, $mes, $ano) = explode('/', $data_nascimneto);
        $mescorte = 03;
        $diacorte = 31;
        $anocorte = 2020;

        // Descobre que dia é hoje e retorna a unix timestamp
        $hoje = mktime(0, 0, 0, $mescorte, $diacorte, $anocorte);
        // Descobre a unix timestamp da data de nascimento do fulano
        $nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

        // Depois apenas fazemos o cálculo já citado :)
        $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

       /* if ($idade == 1) {
            $sql = ("
                select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
                inner join turma on ed57_i_escola = ed18_i_codigo
                where ed57_c_descr ilike '%G1%' or ed57_c_descr ilike '%GRUPO 01%'
                order by ed18_c_nome");
        } elseif ($idade == 2) {
            $sql = ("
                select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
                inner join turma on ed57_i_escola = ed18_i_codigo
                where ed57_c_descr ilike '%G2%' or ed57_c_descr ilike '%GRUPO 02%'
                order by ed18_c_nome");
        } elseif ($idade == 3) {
            $sql = ("
                select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
                inner join turma on ed57_i_escola = ed18_i_codigo
                where ed57_c_descr ilike '%G3%' or ed57_c_descr ilike '%GRUPO 03%'
                order by ed18_c_nome");
        } elseif ($idade == 4) {
            $sql = ("
                select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
                inner join turma on ed57_i_escola = ed18_i_codigo
                where ed57_c_descr ilike '%G4%' or ed57_c_descr ilike '%GRUPO 04%'
                order by ed18_c_nome");
        } elseif ($idade == 5) {
            $sql = ("
                select distinct ed18_c_nome as escola ,ed18_i_codigo as codigo from escola
                inner join turma on ed57_i_escola = ed18_i_codigo
                where ed57_c_descr ilike '%G5%' or ed57_c_descr ilike '%GRUPO 05%'
                order by ed18_c_nome");
        } else {*/
        
        $sql = ("
            select distinct 
                ed18_c_nome as escola,
                ed18_i_codigo as codigo 
            from escola
            inner join turma 
                on ed57_i_escola = ed18_i_codigo
            left join calendario c 
                on ed57_i_calendario = c.ed52_i_codigo
            left join turmaserieregimemat tsrm 
                on tsrm.ed220_i_turma = ed57_i_codigo
            left join serieregimemat srm 
                on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
            left join serie s 
                on srm.ed223_i_serie = s.ed11_i_codigo
            left join turno tuo 
                on ed57_i_turno = tuo.ed15_i_codigo
            where 
                ed11_i_codigo =  $cp_serie and 
                ed18_i_codigo NOT IN (101,45,20,92,87,47,19,70,73,84,40,32,17,63,18,60)
                and c.ed52_i_ano = 2020 
            order 
            by ed18_c_nome
            ");
            //}
            //die ($sql);
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $escolas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $escolas;
    }

    function loadEscolasSerie($cp_serie)
    {
        $sql = "
            select distinct 
                ed18_c_nome as escola,
                ed18_i_codigo as codigo,
                ed11_i_codigo 
            from escola
            inner join turma 
                on ed57_i_escola = ed18_i_codigo
            left join calendario c 
                on ed57_i_calendario = c.ed52_i_codigo
            left join turmaserieregimemat tsrm 
                on tsrm.ed220_i_turma = ed57_i_codigo
            left join serieregimemat srm 
                on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
            left join serie s 
                on srm.ed223_i_serie = s.ed11_i_codigo
            left join turno tuo 
                on ed57_i_turno = tuo.ed15_i_codigo
            inner join configuracoes.db_depart DD 
                on ed18_i_codigo = DD.coddepto 
            where 
                ed11_i_codigo = {$cp_serie} and 
                (limite >= now() or limite is null) and 
                c.ed52_i_ano = 2020 
                order by ed18_c_nome
            ";
        //echo  $sql;       
        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $escolas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $escolas;
    }
    
    function loadTurmas($data_nascimento, $codigo_escola, $cp_serie)
    {
        //$data = $_SESSION['sdt_nascimento'];
        //$data = date("d/m/Y", strtotime($data_session));
        // Separa em dia, mês e ano
        list($dia, $mes, $ano) = explode('/', $data_nascimento);
        $mescorte = 03;
        $diacorte = 31;
        $anocorte = 2019;

        // Descobre que dia é hoje e retorna a unix timestamp
        $hoje = mktime(0, 0, 0, $mescorte, $diacorte, $anocorte);
        // Descobre a unix timestamp da data de nascimento do fulano
        $nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

        // Depois apenas fazemos o cálculo já citado :)
        $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
        //echo ($data."-".$data_session);
        $escola = $codigo_escola;

        /*if ($idade == 1) {

            $sql_turma = "
                select 
                    ed57_i_codigo, 
                    trim(ed57_c_descr) as turma, 
                    trim(ed11_c_descr) as serie,
                    case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
                    end
                    as turno
		from escola e
		inner join turma t 
                    on e.ed18_i_codigo = t.ed57_i_escola
		inner join calendario c 
                    on t.ed57_i_calendario = c.ed52_i_codigo
		inner join turmaserieregimemat tsrm 
                    on tsrm.ed220_i_turma = t.ed57_i_codigo
		inner join serieregimemat srm 
                    on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
		inner join serie s 
                    on srm.ed223_i_serie = s.ed11_i_codigo
		inner join turno tuo 
                    on t.ed57_i_turno = tuo.ed15_i_codigo
		where 
                    ed18_i_codigo = $escola and 
                    (ed57_c_descr ilike '%G1%' or ed57_c_descr ilike '%GRUPO 01%')
		and c.ed52_i_ano = 2019 
                ";
        } elseif ($idade == 2) {
            //$escola = $_GET['codigo'];
            $sql_turma = "
                select 
                    ed57_i_codigo, 
                    trim(ed57_c_descr) as turma,
                    trim(ed11_c_descr) as serie,
                    case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
                    end
                    as turno
                from escola e
                inner join turma t 
                    on e.ed18_i_codigo = t.ed57_i_escola
                inner join calendario c 
                    on t.ed57_i_calendario = c.ed52_i_codigo
		inner join turmaserieregimemat tsrm 
                    on tsrm.ed220_i_turma = t.ed57_i_codigo
		inner join serieregimemat srm 
                    on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
		inner join serie s 
                    on srm.ed223_i_serie = s.ed11_i_codigo
		inner join turno tuo 
                    on t.ed57_i_turno = tuo.ed15_i_codigo
		where 
                    ed18_i_codigo = $escola and 
                    (ed57_c_descr ilike '%G2%' or ed57_c_descr ilike '%GRUPO 02%')
		and c.ed52_i_ano = 2019 
		";

        } elseif ($idade == 3) {
            //    $escola = $_GET['codigo'];
            $sql_turma = "
                select 
                    ed57_i_codigo, 
                    trim(ed57_c_descr) as turma, 
                    trim(ed11_c_descr) as serie,
                    case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
                    end
                    as turno
		from escola e
		inner join turma t 
                    on e.ed18_i_codigo = t.ed57_i_escola
		inner join calendario c 
                    on t.ed57_i_calendario = c.ed52_i_codigo
		inner join turmaserieregimemat tsrm 
                    on tsrm.ed220_i_turma = t.ed57_i_codigo
		inner join serieregimemat srm 
                    on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
		inner join serie s 
                    on srm.ed223_i_serie = s.ed11_i_codigo
		inner join turno tuo 
                    on t.ed57_i_turno = tuo.ed15_i_codigo
		where 
                    ed18_i_codigo = $escola and 
                    (ed57_c_descr ilike '%G3%' or ed57_c_descr ilike '%GRUPO 03%')
		and c.ed52_i_ano = 2019 
		";

        } elseif ($idade == 4) {
            //   $escola = $_GET['codigo'];
            $sql_turma = "select ed57_i_codigo, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
			case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
			end
			as turno
			from escola e
			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
			where ed18_i_codigo = $escola and (ed57_c_descr ilike '%G4%' or ed57_c_descr ilike '%GRUPO 04%')
			and c.ed52_i_ano = 2019 
			";

        } elseif ($idade == 5) {
            //   $escola = $_GET['codigo'];
            $sql_turma = "select ed57_i_codigo, trim(ed57_c_descr) as turma, trim(ed11_c_descr) as serie,
			case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
			end
			as turno
			from escola e
			inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
			inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
			inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
			inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
			inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
			inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
			where ed18_i_codigo = $escola and (ed57_c_descr ilike '%G5%' or ed57_c_descr ilike '%GRUPO 05%')
			and c.ed52_i_ano = 2019 
			";

        } elseif (($idade > 5) && ($idade < 16)) {
            // $escola = $_GET['codigo'];
            $sql_turma = "
                select 
                    ed57_i_codigo, 
                    trim(ed57_c_descr) as turma, 
                    trim(ed11_c_descr) as serie,
                    case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
                    end
                    as turno
		from escola e
		inner join turma t on e.ed18_i_codigo = t.ed57_i_escola
		inner join calendario c on t.ed57_i_calendario = c.ed52_i_codigo
		inner join turmaserieregimemat tsrm on tsrm.ed220_i_turma = t.ed57_i_codigo
		inner join serieregimemat srm on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
		inner join serie s on srm.ed223_i_serie = s.ed11_i_codigo
		inner join turno tuo on t.ed57_i_turno = tuo.ed15_i_codigo
		where 
                    ed18_i_codigo = $escola and 
                    ed11_c_descr not like '%GRUPO%' and
                    ed11_c_descr not like '%EIXO%' and 
                    c.ed52_i_ano = 2019 and 
                    ed11_i_codigo =  $cp_serie
                ";

        } else {*/
            // $escola = $_GET['codigo'];
            $sql_turma = "
		select 
                    ed336_vagas, 
                    ed57_i_codigo, 
                    count (distinct (aluno.ed47_v_nome)) as alunos, 
                    trim(ed57_c_descr) as turma, 
                    trim(ed11_c_descr) as serie,
                    case
			when ed15_c_nome='TARDE 1' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 1' then 'MATUTINO'
			when ed15_c_nome='NOITE 1' then 'NOTURNO'
			when ed15_c_nome='TARDE 2' then 'VESPERTINO'
			when ed15_c_nome='MANHÃ 2' then 'MATUTINO'
			when ed15_c_nome='NOITE 2' then 'NOTURNO'
			else ed15_c_nome
                    end
                    as turno
		from escola e
		inner join turma t 
                    on e.ed18_i_codigo = t.ed57_i_escola
		left join turmaturnoreferente 
                    on turmaturnoreferente.ed336_turma = t.ed57_i_codigo
		left join matricula 
                    on t.ed57_i_codigo = ed60_i_turma
		left join aluno 
                    on ed47_i_codigo = ed60_i_aluno
		inner join calendario c 
                    on t.ed57_i_calendario = c.ed52_i_codigo
		inner join turmaserieregimemat tsrm 
                    on tsrm.ed220_i_turma = t.ed57_i_codigo
		inner join serieregimemat srm 
                    on srm.ed223_i_codigo = tsrm.ed220_i_serieregimemat
		inner join serie s 
                    on srm.ed223_i_serie = s.ed11_i_codigo
		inner join turno tuo 
                    on t.ed57_i_turno = tuo.ed15_i_codigo
		where 
                    ed18_i_codigo = $escola and 
                    c.ed52_i_ano = 2020 and 
                    ed11_i_codigo =  $cp_serie and 
                    (ed60_c_situacao like '%MATRICULADO%' or ed60_c_situacao like '%TRANSFERIDO REDE%' OR ed60_c_situacao like '%RECLASSIFICADO%')   			
		group by 
                    ed57_i_codigo, 
                    ed57_c_descr, 
                    ed11_c_descr, 
                    ed15_c_nome, 
                    ed336_vagas
		having 
                    count (distinct(aluno.ed47_v_nome)) < ed336_vagas
		";
        //}
	//die ($sql_turma);
        $stmt = self::$conexao->prepare($sql_turma);
        $stmt->execute();
        $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $turmas;
    }

    function carregarDadosTurma($codigo_turma, $codigo_escola)
    {
        $sql = " 
            select DISTINCT 
                turma.ed57_i_codigo,
                turma.ed57_c_descr, 
                fc_nomeetapaturma(ed57_i_codigo) as ed11_c_descr, 
                calendario.ed52_c_descr,
                calendario.ed52_i_ano, 
                cursoedu.ed29_c_descr, 
                base.ed31_c_descr, 
                turno.ed15_c_nome, 
                fc_codetapaturma(ed57_i_codigo) as ed11_i_codigo, 
                fc_seqetapaturma(ed57_i_codigo) as ed11_i_sequencia,
                calendario.ed52_i_codigo , 
                cursoedu.ed29_i_codigo,
                base.ed31_i_codigo, 
                turno.ed15_i_codigo, 
                calendario.ed52_d_inicio, 
                calendario.ed52_d_fim 
            from turma
            inner join escola 
                on escola.ed18_i_codigo = turma.ed57_i_escola
            inner join turno 
                on turno.ed15_i_codigo = turma.ed57_i_turno
            inner join sala 
                on sala.ed16_i_codigo = turma.ed57_i_sala
            inner join calendario 
                on calendario.ed52_i_codigo = turma.ed57_i_calendario
            inner join base 
                on base.ed31_i_codigo = turma.ed57_i_base
            inner join regimemat 
                on regimemat.ed218_i_codigo = base.ed31_i_regimemat
            inner join bairro 
                on bairro.j13_codi = escola.ed18_i_bairro
            inner join ruas 
                on ruas.j14_codigo = escola.ed18_i_rua
            inner join db_depart 
                on db_depart.coddepto = escola.ed18_i_codigo
            inner join tiposala 
                on tiposala.ed14_i_codigo = sala.ed16_i_tiposala
            inner join duracaocal 
                on duracaocal.ed55_i_codigo = calendario.ed52_i_duracaocal
            inner join cursoedu 
                on cursoedu.ed29_i_codigo = base.ed31_i_curso
            inner join ensino 
                on ensino.ed10_i_codigo = cursoedu.ed29_i_ensino
            inner join tipoensino 
                on tipoensino.ed36_i_codigo = ensino.ed10_i_tipoensino
            inner join turmaserieregimemat 
                on turmaserieregimemat.ed220_i_turma = turma.ed57_i_codigo
            inner join procedimento 
                on procedimento.ed40_i_codigo = turmaserieregimemat.ed220_i_procedimento
            inner join formaavaliacao 
                on formaavaliacao.ed37_i_codigo = procedimento.ed40_i_formaavaliacao
            inner join serieregimemat 
                on serieregimemat.ed223_i_codigo = turmaserieregimemat.ed220_i_serieregimemat
            inner join serie 
                on serie.ed11_i_codigo = serieregimemat.ed223_i_serie
            inner join turmacensoetapa 
                on turmacensoetapa.ed132_turma = turma.ed57_i_codigo
            left join censocursoprofiss 
                on censocursoprofiss.ed247_i_codigo = turma.ed57_i_censocursoprofiss
            where 
                ed52_c_passivo = 'N' AND 
                ed57_i_escola = {$codigo_escola} AND 
                ed57_i_codigo = {$codigo_turma}
                --and ed57_i_tipoturma <> 6 and ed31_i_curso = 6
            order by 
                ed57_c_descr
            ";

        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $dados_turmas = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dados_turmas;

    }

    function getVagasTurma($codturma)
    {
        $sql = " 
            select * 
            from turma 
            inner join calendario 
                on ed57_i_calendario = ed52_i_codigo
            inner join escola 
                on ed57_i_escola = ed18_i_codigo
            inner join turmaserieregimemat 
                on ed57_i_codigo = ed220_i_turma
            inner join serieregimemat 
                on ed220_i_serieregimemat = ed223_i_codigo
            inner join serie 
                on ed223_i_serie = ed11_i_codigo
            inner join turmaturnoreferente 
                on turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
            inner join turno 
                on turno.ed15_i_codigo = turmaturnoreferente.ed336_turnoreferente
            WHERE 
                ed57_i_codigo = $codturma and 
                calendario.ed52_i_ano = 2020 
            ORDER BY 
                escola.ed18_c_nome";

        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $vagas_turma = $stmt->fetch(PDO::FETCH_ASSOC);
        return $vagas_turma['ed336_vagas'];
    }


    function getAMatriculadoTurma($codturma)
    {
        $sql = "
            SELECT  
                turno.ed15_c_nome as turno,
                trim(turma.ed57_c_descr) as Turma,
                count (distinct (aluno.ed47_v_nome)) as qtd_alunos_matr,
                trim(ed11_c_descr) as etapa,ed336_vagas as vagas
            FROM matricula
            LEFT JOIN aluno 
                on aluno.ed47_i_codigo = matricula.ed60_i_aluno
            LEFT JOIN turma 
                on turma.ed57_i_codigo = matricula.ed60_i_turma
            LEFT JOIN turmaturnoreferente 
                on turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
            LEFT JOIN turno 
                on turno.ed15_i_codigo = turmaturnoreferente.ed336_turnoreferente
            LEFT JOIN sala 
                on sala.ed16_i_codigo = turma.ed57_i_sala
            LEFT JOIN matriculaserie 
                on matriculaserie.ed221_i_matricula = matricula.ed60_i_codigo
            LEFT JOIN serie 
                on serie.ed11_i_codigo = matriculaserie.ed221_i_serie
            LEFT JOIN escola 
                on escola.ed18_i_codigo = turma.ed57_i_escola
            LEFT JOIN calendario 
                on calendario.ed52_i_codigo = turma.ed57_i_calendario
            WHERE 
                ed57_i_codigo = $codturma
            GROUP BY 
                turma.ed57_c_descr, 
                sala.ed16_i_capacidade, 
                turma.ed57_i_codigo,
                escola.ed18_c_nome,
                turno.ed15_c_nome,
                calendario.ed52_c_descr,
                turmaturnoreferente.ed336_vagas, 
                ed11_c_descr
            --having count (distinct (aluno.ed47_v_nome)) > 0 and  count (distinct (aluno.ed47_v_nome)) < turmaturnoreferente.ed336_vagas
            ORDER BY 
                escola.ed18_c_nome
            ";

        $stmt = self::$conexao->prepare($sql);
        $stmt->execute();
        $matriculado_turma = $stmt->fetch(PDO::FETCH_ASSOC);
        return $matriculado_turma['qtd_alunos_matr'];
    }


}