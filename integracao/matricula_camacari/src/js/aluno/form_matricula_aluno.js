function matricular() {

    let url = "app/aluno/ajax/matricula_AJAX.php?funcao=matricular";
    let data = {
        codigo_aluno: $('#vch_codigo').val(),
        nome_aluno: $('#vch_nome').val(),

        cp_escolas: $('#cp_escolas').val(),
        cp_turmas: $('#cp_turmas').val(),

        cp_curso: $('#cp_curso').val(),
        cp_base: $('#cp_base').val(),
        cp_calendario: $('#cp_calendario').val(),

        cp_etapa: $('#cp_etapa').val(),
        cp_turno: $('#cp_turno').val(),
        cp_vagas_turma: $('#cp_vagas_turma').val(),

        cp_matriculado: $('#cp_matriculado').val(),
        cp_vagas_disp: $('#cp_vagas_disp').val(),

    };

    $('#cp_btn_matricular').attr('disabled', 'disabled');
    $('#cp_text_btn').text('Processando');
    $('#loading_btn_matricula').show();
    $.post(url, data, function (data) {
        let response =  JSON.parse(data);
        $('#msg_modal_matricula').text(response.msg);
        $('#modal_msg_matricula').modal('show');
        if (response.status === 'erro'){
            $('#cp_btn_matricular').show();
            $('#cp_btn_comprovante').hide();
            $('#cp_btn_matricular').removeAttr('disabled');
            $('#cp_text_btn').text('Matricular');
            $('#loading_btn_matricula').hide();
        }else{
            $('#cp_btn_matricular').hide();
            $('#cp_btn_comprovante').show();
        }

    });
}

function validacao() {



    if ($('#cp_escolas').val() === '') {
        $('#cp_turmas').attr('disabled', 'disabled');
        $('#cp_btn_matricular').attr('disabled', 'disabled');
    } else {
        $('#cp_turmas').removeAttr('disabled');
    }


     if ($('#cp_escolas').val() === null || $('#cp_turmas').val() === null || $('#cp_turmas').val() === '') {
         $('#cp_btn_matricular').attr('disabled', 'disabled');
     } else {
         $('#cp_btn_matricular').removeAttr('disabled');
     }

    let vagas = $('#cp_vagas_turma').val();

    if(vagas === '' || vagas==='0' || vagas===null ){
        $('#cp_vagas_turma').css('boder','solid 1px red');

        $('#cp_btn_matricular').attr('disabled', 'disabled');
    }
}

function loadEscolas() {

    let url = "app/aluno/ajax/escola_AJAX.php";
    let data = {
        data_nascimento: $('#sdt_nascimento').val(),
        cp_serie: $('#cp_serie').val(),
        funcao: "carregar_escolas",
    };

    $.get(url, data, function (data) {
        $('#cp_escolas').html(data);
    });

}

function loadTurmas() {

    lispaDadosTurma();
    if ($('#cp_escolas').val() != '' || $('#cp_escolas').val() != null) {
        let url = "app/aluno/ajax/escola_AJAX.php";
        let data = {
            data_nascimento: $('#sdt_nascimento').val(),
            codigo_escola: $('#cp_escolas').val(),
            funcao: "carregar_turmas",
        };
        $('#loading_turma').show();
        $('#cp_turmas').attr('disabled', 'disabled');
        $.get(url, data, function (data) {
            $('#cp_turmas').html(data);
            $('#cp_turmas').removeAttr('disabled');
            $('#loading_turma').hide();
        });
    }else{
        $('#cp_turmas').attr('disabled', 'disabled');
    }
}

function loadDadosTurma() {
    let url = "app/aluno/ajax/escola_AJAX.php";
    let data = {
        codigo_turma: $('#cp_turmas').val(),
        codigo_escola: $('#cp_escolas').val(),
        funcao: "carregar_dados_turmas",
    };
    $('#cp_btn_matricular').attr('disabled', 'disabled');
    $('#cp_turmas').attr('disabled','disabled');
    $('#cp_escolas').attr('disabled','disabled');
    $('.loading_dados_turma').show();
    $.get(url, data, function (data) {
        let dados_turma = JSON.parse(data);
        $('#cp_curso').val(dados_turma.curso);
        $('#cp_base').val(dados_turma.base);
        $('#cp_calendario').val(dados_turma.calendario);
        $('#cp_etapa').val(dados_turma.etapa);
        $('#cp_turno').val(dados_turma.turno);
        $('#cp_vagas_turma').val(dados_turma.vagas_turma);
        $('#cp_matriculado').val(dados_turma.alunos_matriculado);
        $('#cp_vagas_disp').val(dados_turma.vagas_disp);
        $('#cp_escolas').removeAttr('disabled');
        $('#cp_turmas').removeAttr('disabled');
        $('.loading_dados_turma').hide();
        validacao();
    });

}

function loadDadosTurmaVisualizar() {

    let url = "app/aluno/ajax/escola_AJAX.php";
    let data = {
        codigo_turma: $('#cp_visu_cod_turma').val(),
        codigo_escola: $('#cp_visu_cod_escola').val(),
        funcao: "carregar_dados_turmas",
    };

    $('#cp_btn_matricular').attr('disabled', 'disabled');
    $('#cp_turmas').attr('disabled','disabled');
    $('#cp_escolas').attr('disabled','disabled');
    $('.loading_dados_turma').show();
    $.get(url, data, function (data) {
        let dados_turma = JSON.parse(data);
        $('#cp_curso').val(dados_turma.curso);
        $('#cp_base').val(dados_turma.base);
        $('#cp_calendario').val(dados_turma.calendario);
        $('#cp_etapa').val(dados_turma.etapa);
        $('#cp_turno').val(dados_turma.turno);
        $('#cp_vagas_turma').val(dados_turma.vagas_turma);
        $('#cp_matriculado').val(dados_turma.alunos_matriculado);
        $('#cp_vagas_disp').val(dados_turma.vagas_disp);
        $('#cp_escolas').removeAttr('disabled');
        $('#cp_turmas').removeAttr('disabled');
        $('.loading_dados_turma').hide();
        validacao();
    });

}

function lispaDadosTurma(){
    $('#cp_curso').val('');
    $('#cp_base').val('');
    $('#cp_calendario').val('');
    $('#cp_etapa').val('');
    $('#cp_turno').val('');
    $('#cp_vagas_turma').val('');
    $('#cp_matriculado').val('');
    $('#cp_vagas_disp').val('');
}