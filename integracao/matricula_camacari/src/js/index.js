
function showComprovanteAluno(cp) {
    let url = "app/aluno/ajax/aluno_AJAX.php";
    let data = {
        funcao: "showComprovanteAluno",
        codigo: $(cp).attr('data-aluno-id'),
    }

    $.get(url,data, (response) => {
        if (IsSessionExpired(response)){
            return null;
        }  
        
        window.open(response, '_blank');
    });

}
function showAutorizaçãoMatricula(cp) {
    let url = "app/aluno/ajax/aluno_AJAX.php";
    let data = {
        funcao: "showAutorizacaoMatricula",
        codigo: $(cp).attr('data-aluno-id'),
    }

    $.get(url,data, (response) => {
        if (IsSessionExpired(response)){
            return null;
        }  
        
        window.open(response, '_blank');
    });

}
function find_ender() {
    ender = $('#cp_texto').val();
    $('#resposta').show();
    $('#loading').show();
    $('#cp_localidades').attr('disabled','disabled');
    $('#btn_submit').prop('disabled',true);
    $.ajax({
            url: "app/aluno/pesq.php",
            type: 'get',
            data: {
                funcao: 'carregar_autocomplete',
                endereco: ender
            },
            beforeSend: function() {}
        })
        .done(function(msg) {
            $('#loading').hide();
            $('#resposta').html(msg);
        })
        .fail(function(jqXHR, textStatus, msg) {
            $('#loading').show();
            alert(msg);
        });
}

function pegarValores() {
    let valor = $('#resposta :selected').val();
    $('#loading').show();
    $('#cp_localidades').attr('disabled','disabled');
    $('#btn_submit').prop('disabled',true);
    $.ajax({
            url: "app/aluno/pesq.php",
            type: 'get',
            data: {
                codigo: valor,
                funcao: 'carregar_endereco'
            },
            beforeSend: function() {}
        })
        .done(function(msg) {
            $('#btn_submit').prop('disabled',false);
            $('#cp_localidades').removeAttr('disabled');
            $('#loading').hide();
            let endereco = JSON.parse(msg);
            $('#vch_bairro').val(endereco.bairro);
            $('#vch_cidade').val(endereco.cidade);
            $('#vch_cep').val(endereco.cep);
            $('#ender').val(endereco.endereco);
            let codigo_bairro = endereco.codigo_bairro;
            carregar_localidade(codigo_bairro)

        })
        .fail(function(jqXHR, textStatus, msg) {
            $('#loading').hide();
            alert('Requisição Falhou!');
        });

}

function carregar_localidade(codigo_bairro) {
    $.ajax({
            url: "app/aluno/pesq.php",
            type: 'get',
            data: {
                codigo: codigo_bairro,
                funcao: 'carregar_localidade'
            },
            beforeSend: function() {}
        })
        .done(function(msg) {
            $('#cp_localidades').html(msg);

        })
        .fail(function(jqXHR, textStatus, msg) {
            alert('RequisiÃ§Ã£o Falhou!');
        });
}

// function pegarValores(cp) {
//     let component = $(cp);
//     let localidade = component.find(':selected').val();
//     let endereco_2 = component.find(':selected').attr('data-endereco');
//     let url = "app/aluno/ajax/localidade_AJAX.php";
//     let data = {
//         endereco: localidade,
//         endereco_2: endereco_2,
//         funcao: "carregar_endereco",
//     };
//     $.get(url, data, function (data) {
//         let endereco = JSON.parse(data);

//         $('#vch_bairro').val(endereco.bairro);
//         $('#vch_cidade').val(endereco.cidade);
//         $('#vch_cep').val(endereco.cep);
//         $('#ender').val(endereco.endereco);
//         loadLocalidade(endereco.codigo_bairro);
//     });
// }

function storeAluno() {

    if (validaForm() == false) {
        return false
    }

    let nome_aluno = $('#vch_nome').val().trim();
    let data_nascimento = $('#sdt_nascimento').val().trim();
    let serie = $('#cp_serie').val().trim();
    let sexo = $('#cp_sexo').val().trim();
    let mae = $('#vch_mae').val().trim();
    let responsavel = $('#vch_responsavel').val().trim();
    let reponsavel_cpf = $('#vch_cpf').val().trim();
    let telefone = $('#vch_telefone').val().trim();
    let endereco = $('#ender').val().trim();
    let bairro = $('#vch_bairro').val().trim();
    let numero = $('#numero').val().trim();
    let cep = $('#vch_cep').val().trim();
    let localidade = $('#cp_localidades').val().trim();
    //  cidade:$('#vch_cidade').attr('data-codigo-cidade').trim(),

    let url = 'app/aluno/ajax/aluno_AJAX.php?funcao=storeAluno';

    url += '&nome=' + nome_aluno;
    url += '&data_nascimento=' + data_nascimento;
    url += '&serie=' + serie;
    url += '&sexo=' + sexo;
    url += '&mae=' + mae;
    url += '&responsavel=' + responsavel;
    url += '&reponsavel_cpf=' + reponsavel_cpf;
    url += '&telefone=' + telefone;
    url += '&endereco=' + endereco;
    url += '&bairro=' + bairro;
    url += '&numero=' + numero;
    url += '&cep=' + cep;
    url += '&localidade=' + localidade;

    let data = "";

    $.get(url, data, function (data) {
        //alert(data);
        let response = JSON.parse(data);
        $('#status_modal').text(response.status);
        $('#msg_modal').text(response.msg);
        $('#msg_secesso_modal').modal('show');

    });
}
function getForm(form) {
    if (screen.width <= 582) {
        $('#btn-push_menu').trigger('click');
    }

    $('#cp_loading').show();
    $('#putForm').html('');
    $.get(form, function (data) {
        
        IsSessionExpired(data);
        $('#cp_loading').hide();
        $('#putForm').html(data);
    }).fail(() => {
        $('#cp_loading').hide();
        alert('Erro ao conectar o servidor.');
    });

    ;
}

function IsSessionExpired(data){
    if(data.trim() == 'expirou'){
        alert('Sessao expirada, faça login novamente.');
        history.go(-1);
        return true; 
    }
    return false;
}

function getRegistros() {
    let url = 'app/aluno/ajax/aluno_AJAX.php';
    let data = {
        funcao: 'atualizar_label_registro',
    };

    $.get(url, data, function (data) {
        $('#registros').html(data);
    });

}

function title($title) {
    $('#title').text($title);
}

function subTitle1($subtitle) {
    $('#subtitle1').text($subtitle);
}

function subTitle2($subtitle) {
    $('#subtitle2').text($subtitle);
}

function findAlunoMatricular(paginacao) {
    
    if (paginacao == ''){
        paginacao =0; 
    }
    let funcao = 'find_aluno_matricula';
    let codigo_aluno = ($('#cp_cod_aluno').val().trim() || '');
    let aluno = ($('#cp_filtrar_aluno').val().trim() || '');
    let data_nascimento = ($('#cp_data_nascimento_matricula').val().trim() || '');
    let responsavel = ($('#cp_responsavel_matricula').val().trim() || '');
    let status_id = ($('#alunostatusreserva_id').val() || '');
    let offset      = paginacao;
    
    let url = 'app/aluno/table_lista_matricular.php?';
    url += '&funcao=' + funcao;
    url += '&aluno=' + aluno;
    url += '&cod_aluno=' + codigo_aluno;
    url += '&data_nascimento=' + data_nascimento;
    url += '&responsavel=' + responsavel;
    url += '&status_id=' + status_id;
    url += '&offset=' + offset;

    let data = [];

    let url_loading = 'app/aluno/loading.php';
    $.get(url_loading, function (data) {
        IsSessionExpired(data);
        $('#div_table').html(data);
    });

    $.get(url, data, function (data) {
        IsSessionExpired(data);
        $('#div_table').html(data);
    });
    getRegistros();
}

function findAlunoProximo() {
    let url = 'app/aluno/table_lista_matricular.php';
    let data = {
        funcao: 'findAlunoProximo',
        aluno: ($('#cp_filtrar_aluno').val().trim() || ''),
        data_nascimento: ($('#cp_data_nascimento_matricula').val().trim() || ''),
        responsavel: ($('#cp_responsavel_matricula').val().trim() || ''),
        status_id: String($('#alunostatusreserva_id').val()),
        offset: 40 + parseInt($('#cp_next_page').attr('data-page')),
    };
    let url_loading = 'app/aluno/loading.php';
    $.get(url_loading, function (data) {
        $('#div_table').html(data);
    });
    $.get(url, data, function (data){
        $('#div_table').html(data);
    });
    //getRegistros();
}
function findAlunoInicio() {

    let url = 'app/aluno/table_lista_matricular.php';
    let data = {
        funcao: 'findAlunoInicio',
        aluno: ($('#cp_filtrar_aluno').val().trim() || ''),
        data_nascimento: ($('#cp_data_nascimento_matricula').val().trim() || ''),
        responsavel: ($('#cp_responsavel_matricula').val().trim() || ''),
        status_id: String($('#alunostatusreserva_id').val()),
        offset: 0,
    };
    let url_loading = 'app/aluno/loading.php';
    $.get(url_loading, function (data) {
        $('#div_table').html(data);
    });
    $.get(url, data, function (data) {
        $('#div_table').html(data);
    });
    //getRegistros();
}
function findAlunoFim() {
    
    let totalRegistro = parseInt($('#cp_end_page').attr('data-page')/40); 
    totalRegistro = totalRegistro * 40;  
    let url = 'app/aluno/table_lista_matricular.php';
    let data = {
        funcao: 'findAlunoFim',
        aluno: ($('#cp_filtrar_aluno').val().trim() || ''),
        data_nascimento: ($('#cp_data_nascimento_matricula').val().trim() || ''),
        responsavel: ($('#cp_responsavel_matricula').val().trim() || ''),
        status_id: String($('#alunostatusreserva_id').val()),
        offset: totalRegistro,
    };
    let url_loading = 'app/aluno/loading.php';
    $.get(url_loading, function (data) {
        $('#div_table').html(data);
    });
    $.get(url, data, function (data) {
        $('#div_table').html(data);
    });
    //getRegistros();
}
function findAlunoAnterior() {

    let url = 'app/aluno/table_lista_matricular.php';
    let data = {
        funcao: 'findAlunoAnterior',
        aluno: ($('#cp_filtrar_aluno').val().trim() || ''),
        data_nascimento: ($('#cp_data_nascimento_matricula').val().trim() || ''),
        responsavel: ($('#cp_responsavel_matricula').val().trim() || ''),
        status_id: String($('#alunostatusreserva_id').val()),
        offset: parseInt($('#cp_next_page').attr('data-page')) - 40,
    };
    let url_loading = 'app/aluno/loading.php';
    $.get(url_loading, function (data) {
        $('#div_table').html(data);
    });
    $.get(url, data, function (data) {
        $('#div_table').html(data);
    });
    //getRegistros();
}

function viewComprovante() {
    //  let codigo_aluno = $('#vch_codigo').val();
    //  let codigo_escola = $('#cp_escolas').val();
    // // getForm('app/aluno/view_comprovante_matricula.php?codigo_aluno='+codigo_aluno+'codigo_escola'+codigo_escola);
    let url = 'app/aluno/view_comprovante_matricula.php';
    let data = {
        codigo_aluno: $('#vch_codigo').val(),
        codigo_escola: $('#cp_escolas').val()
    };
    $.get(url, data, function (data) {
        $('#putForm').html(data);
    });
}
