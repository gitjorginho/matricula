
function letras() {
    tecla = event.keyCode;
    if (tecla >= 48 && tecla <= 57) {
        return false;
    } else {
        return true;
    }
}

// $('#cp_texto').keydown(function () {
//     let resposta = $('#resposta');
//     let url = "app/aluno/ajax/localidade_AJAX.php";
//     let data = {
//         endereco : $('#cp_texto').val(),
//         localidade : $('#cp_localidade').val()
//         funcao: "localizar_endereco",
//     };
//     $.get(url, data, function (data) {
//         resposta.html(data);
//         resposta.css('display', 'block');
//     });
// });


function lastList(paginacao) {

    $('#modalConfirmaAlteracao').modal('hide');
    $('#msg_secesso_modal').modal('hide');
    getForm('app/aluno/lista_matricular.php?paginacao='+paginacao+'&voltaredicao=true');

}

function onlynumber(evt) {
   var theEvent = evt || window.event;
   var key = theEvent.keyCode || theEvent.which;
   key = String.fromCharCode( key );
   //var regex = /^[0-9.,]+$/;
   var regex = /^[0-9.]+$/;
   if( !regex.test(key) ) {
      theEvent.returnValue = false;
      if(theEvent.preventDefault) theEvent.preventDefault();
   }
}

//function pegarValores(cp) {
//   let component = $(cp);
//    let localidade = component.find(':selected').val();
//    let endereco_2 = component.attr('data-endereco-2');
//    let url = "app/aluno/ajax/localidade_AJAX.php";
//   let data = {
//        endereco: localidade,
//        endereco_2: endereco_2,
//        funcao: "carregar_endereco",
//    };
//    $.get(url, data, function (data) {
//        let endereco = JSON.parse(data);

//        $('#vch_bairro').val(endereco.bairro);
//        $('#vch_cidade').val(endereco.cidade);
//        $('#vch_cep').val(endereco.cep);
//        $('#ender').val(endereco.endereco);
//        loadLocalidade(endereco.codigo_bairro);
//    });
//}

function loadLocalidade(codigo) {

    let url = "app/aluno/ajax/localidade_AJAX.php";
    let data = {
        codigo_localidade: codigo,
        funcao: "carregar_localidade",
    };
    $.get(url, data, function (data) {
        $('#cp_localidades').html(data);
    });

}

function mudarCorCampo(nomeDoLabel, nomeDoCampo) {
    document.getElementById(nomeDoLabel).style.color = 'black';
    document.getElementById(nomeDoCampo).style.borderColor = '#ced4da';
}

function validar() {

    //valida dados inseridos no formulario conforme regras de cadastro
    let nome = $('#vch_nome').val().trim();
    let nome_completo = nome.split(' ');

    if (nome === '') {
        $("#msg").trigger("click");
        $("#msg_text").text("Nome do aluno precisa ser preenchido!");
        document.getElementById('labelNome').style.color = 'red';
        document.getElementById('vch_nome').style.borderColor = 'red';
        return false;
    }

    let filter_nome = /^([a-zA-Zà-úÀ-Ú]|\s+)+$/;
    if (!filter_nome.test(nome)) {
        $("#msg").trigger("click");
        $("#msg_text").text("Nome do aluno é invalido!");
        document.getElementById('labelNome').style.color = 'red';
        document.getElementById('vch_nome').style.borderColor = 'red';
        return false;
    }




    if (nome_completo.length == 1) {
        $("#msg").trigger("click");
        $("#msg_text").text("Nome do aluno está incompleto!");
        document.getElementById('vch_nome').style.color = 'red';
        document.getElementById('vch_nome').style.borderColor = 'red';
        return false;
    }


    if ($('#sdt_nascimento').val().trim() === '') {
        $("#msg").trigger("click");
        $("#msg_text").text("Data de nascimento precisa ser preenchida!");
        document.getElementById('labelDataNascimento').style.color = 'red';
        document.getElementById('sdt_nascimento').style.borderColor = 'red';
        return false;
    }

    if (compareDates($('#sdt_nascimento').val())) {
        $("#msg").trigger("click");
        $("#msg_text").text("Data de nascimento não pode ser maior que a data atual!");
        document.getElementById('labelDataNascimento').style.color = 'red';
        document.getElementById('sdt_nascimento').style.borderColor = 'red';
        return false;
    }

    if (validaDat($('#sdt_nascimento').val())) {
        $("#msg").trigger("click");
        $("#msg_text").text("Data de nascimento está incorreta!");
        document.getElementById('labelDataNascimento').style.color = 'red';
        document.getElementById('sdt_nascimento').style.borderColor = 'red';
        return false;
    }

    //DATA DE NASCIMENTO MAIOR QUE 100
    datanasc = $('#sdt_nascimento').val();
    dianasc = datanasc.substr(0, 2);
    mesnasc = datanasc.substr(3, 2);
    anonasc = datanasc.substr(6, 4);

    if (anonasc < 1921) {
        $("#msg").trigger("click");
        $("#msg_text").text("Ano da data de nascimento deve ser maior que 1920!");
        document.getElementById('labelDataNascimento').style.color = 'red';
        document.getElementById('sdt_nascimento').style.borderColor = 'red';
        return false;
    }


    if ($('#sdt_nascimento').val().trim() === '') {
        $("#msg").trigger("click");
        $("#msg_text").text("Data de nascimento precisa ser preenchida!");
        document.getElementById('labelSdt_nascimento').style.color = 'red';
        document.getElementById('sdt_nascimento').style.borderColor = 'red';
        return false;
    }

    if ($('#cp_sexo').val().trim() === '') {
        $("#msg").trigger("click");
        $("#msg_text").text("Informe o sexo do aluno!");
        document.getElementById('labelSexo').style.color = 'red';
        document.getElementById('cp_sexo').style.borderColor = 'red';
        return false;
    }

    if ($('#cp_serie').val().trim() === '') {
        $("#msg").trigger("click");
        $("#msg_text").text("Informe a série desejada a cursar!");
        document.getElementById('labelSerie').style.color = 'red';
        document.getElementById('cp_serie').style.borderColor = 'red';
        return false;
    }

    nome = $('#vch_mae').val().trim();
    nome_completo = nome.split(' ');

    if ($('#vch_orgaopublico').val() === '') {
        if (nome === '') {
            $("#msg").trigger("click");
            $("#msg_text").text("Nome da mãe deve ser preenchido!");
            document.getElementById('labelNomeMae').style.color = 'red';
            document.getElementById('vch_mae').style.borderColor = 'red';
            return false;
        }
    }
    if (nome !== '') {
        if (nome_completo.length == 1) {
            $("#msg").trigger("click");
            $("#msg_text").text("Nome da mãe está incompleto!");
            document.getElementById('labelNomeMae').style.color = 'red';
            document.getElementById('vch_mae').style.borderColor = 'red';
            return false;
        }
    }


    if (!filter_nome.test(nome)) {
        $("#msg").trigger("click");
        $("#msg_text").text("Nome da mãe é invalido !");
        document.getElementById('labelNomeMae').style.color = 'red';
        document.getElementById('vch_nome').style.borderColor = 'red';
        return false;
    }
    /////////valida o nome da responsavel

    nome = $('#vch_responsavel').val().trim();
    nome_completo = nome.split(' ');

    // if (nome === '') {
    //     $("#msg").trigger("click");
    //     $("#msg_text").text("Nome do Responsï¿½vel deve ser preenchido!");
    //     document.getElementById('labelNomeResponsavel').style.color = 'red';
    //     document.getElementById('vch_responsavel').style.borderColor = 'red';
    //     return false;
    // }
    if (nome !== '') {
        if (nome_completo.length == 1) {
            $("#msg").trigger("click");
            $("#msg_text").text("Nome do Responsável está incompleto!");
            document.getElementById('labelNomeResponsavel').style.color = 'red';
            document.getElementById('vch_responsavel').style.borderColor = 'red';
            return false;
        }
    }

    if (!filter_nome.test(nome)) {
        $("#msg").trigger("click");
        $("#msg_text").text("Nome do responsavel é invalido !");
        document.getElementById('labelNomeResponsavel').style.color = 'red';
        document.getElementById('vch_responsavel').style.borderColor = 'red';
        return false;
    }



    let cpf_value = $('#vch_cpf').val();
    // if (cpf_value.trim() === '') {
    //     $("#msg").trigger("click");
    //     $("#msg_text").text("Informe o CPF!");
    //     document.getElementById('labelCpf').style.color = 'red';
    //     document.getElementById('vch_cpf').style.borderColor = 'red';
    //     return false;
    // }

    if (cpf_value != '') {

        if (!validarCPF(cpf_value)) {
            $("#msg").trigger("click");
            $("#msg_text").text("CPF inválido!");
            document.getElementById('labelCpf').style.color = 'red';
            document.getElementById('vch_cpf').style.borderColor = 'red';
            return false;
        }
    }
    let escola = $('#cp_escola').val();
    if (escola === '') {
        $("#msg").trigger("click");
        $("#msg_text").text("Informe a escola do aluno!");
        document.getElementById('labelEscola').style.color = 'red';
        document.getElementById('cp_escola').style.borderColor = 'red';
        return false;
    }

    let telefone = $('#vch_telefone').val();


    // if (telefone === '') {
    //     $("#msg").trigger("click");
    //     $("#msg_text").text("Digite o número de telefone no campo correspondente!");
    //     document.getElementById('labelTelefone').style.color = 'red';
    //     document.getElementById('vch_telefone').style.borderColor = 'red';
    //     return false;
    // }

    /*A validacao de endereco ocorre assim: se o campo de endereco nao estiver preenchido,
    * o campo de pesquisa do endereco ganha destaque vermelho pois
    * somente ele pode ser alterado, os demais estao bloqueados
    */
    endereco = $('#ender').val().trim();
    if (endereco === '') {
        $("#msg").trigger("click");
        $("#msg_text").text("Digite o endereção do aluno!");
        document.getElementById('labelEndereco').style.color = 'red';
        document.getElementById('vch_endereco').style.borderColor = 'red';
        return false;
    }


    if ($('#cp_localidades').val() === ''){
        $("#msg").trigger("click");
        $("#msg_text").text("Localidade é obrigatorio.");
        document.getElementById('cp_localidades').style.borderColor = 'red';
        return false;
    }


    let $padrao_numero = /^[0-9]*$/;
    let numero =  $('#vch_numero').val().trim();
    if (!$padrao_numero.test(numero)) {
        $("#msg").trigger("click");
        $("#msg_text").text("Numero de endereço invalido !");
        //document.getElementById('labelNome').style.color = 'red';
        document.getElementById('vch_numero').style.borderColor = 'red';
        return false;
    }
    return true;
}

function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf == '') return false;
    // Elimina CPFs invalidos conhecidos
    if (cpf.length != 11 ||
        cpf == "00000000000" ||
        cpf == "11111111111" ||
        cpf == "22222222222" ||
        cpf == "33333333333" ||
        cpf == "44444444444" ||
        cpf == "55555555555" ||
        cpf == "66666666666" ||
        cpf == "77777777777" ||
        cpf == "88888888888" ||
        cpf == "99999999999")
        return false;
    // Valida 1o digito
    add = 0;
    for (i = 0; i < 9; i++)
        add += parseInt(cpf.charAt(i)) * (10 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(9)))
        return false;
    // Valida 2o digito
    add = 0;
    for (i = 0; i < 10; i++)
        add += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(10)))
        return false;
    return true;
}


function compareDates(date) {
    let parts = date.split('/') // separa a data pelo caracter '/'
    let today = new Date() // pega a data atual

    date = new Date(parts[2], parts[1] - 1, parts[0]) // formata 'date'

    // compara se a data informada ? maior que a data atual
    // e retorna true ou false
    return date >= today ? true : false;
}

function lengthDate(date) {
    if (date.length != 10) {
        return true;
    }
    return false;
}


function validaDat(valor) {
    var date = valor;
    var ardt = new Array;
    var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
    ardt = date.split("/");
    erro = false;
    if (date.search(ExpReg) == -1) {
        erro = true;
    } if (((ardt[1] == 4) || (ardt[1] == 6) || (ardt[1] == 9) || (ardt[1] == 11)) && (ardt[0] > 30))
        erro = true;
    if (ardt[1] == 2) {
        if ((ardt[0] > 28) && ((ardt[2] % 4) != 0))
            erro = true;
        if ((ardt[0] > 29) && ((ardt[2] % 4) == 0))
            erro = true;
    }
    return erro;
}


// // function loadEscolas() {
// //
// //     let url = "app/aluno/ajax/escola_AJAX.php";
// //     let data = {
// //         data_nascimento: $('#sdt_nascimento').val(),
// //         funcao: "carregar_escolas",
// //     };
// //
// //     $.get(url, data, function (data) {
// //         $('#cp_escolas').html(data);
// //     });
// // }
//
// // function loadTurmas() {
// //     let url = "app/aluno/ajax/escola_AJAX.php";
// //     let data = {
// //         data_nascimento: $('#sdt_nascimento').val(),
// //         codigo_escola: $('#cp_escolas').val(),
// //         funcao: "carregar_turmas",
// //     };
// //
// //     $.get(url, data, function (data) {
// //         $('#cp_turmas').html(data);
// //     });
// //
// //
// // }
//
// // function loadDadosTurma() {
// //     let url = "app/aluno/ajax/escola_AJAX.php";
// //     let data = {
// //         codigo_turma: $('#cp_turmas').val(),
// //         codigo_escola: $('#cp_escolas').val(),
// //         funcao: "carregar_dados_turmas",
// //     };
// //
// //     $.get(url, data, function (data) {
// //         let dados_turma = JSON.parse(data);
// //         $('#cp_curso').val(dados_turma.curso);
// //         $('#cp_base').val(dados_turma.base);
// //         $('#cp_calendario').val(dados_turma.calendario);
// //         $('#cp_etapa').val(dados_turma.etapa);
// //         $('#cp_turno').val(dados_turma.turno);
// //         $('#cp_vagas_turma').val(dados_turma.vagas_turma);
// //         $('#cp_matriculado').val(dados_turma.alunos_matriculado);
// //         $('#cp_vagas_disp').val(dados_turma.vagas_disp);
// //     });
// //
// // }

function showModalUpdateConfirmation() {
    $("#modalConfirmaAlteracao").modal('show');
}


function updateAluno() {
    //alert($('#alunostatusreserva_id').val().trim());
    if (validar() == true) {
        // $("#modal-btn-sim").on("click", function () {
        //$("#modalConfirmaAlteracao").modal('hide');
        let url = 'app/aluno/ajax/aluno_AJAX.php?funcao=update_aluno'
            + '&nome_aluno=' + $('#vch_nome').val().trim()
            + '&codigo=' + $('#vch_codigo').val().trim()
            + '&data_nascimento=' + $('#sdt_nascimento').val().trim()
            + '&sexo=' + $('#cp_sexo').val().trim()
            + '&mae=' + $('#vch_mae').val().trim()
            + '&responsavel=' + $('#vch_responsavel').val().trim()
            + '&reponsavel_cpf=' + $('#vch_cpf').val().trim()
            + '&telefone=' + $('#vch_telefone').val().trim()
            + '&endereco=' + $('#ender').val().trim()
            + '&bairro=' + $('#vch_bairro').val().trim()
            + '&numero=' + $('#vch_numero').val().trim()
            + '&cep=' + $('#vch_cep').val().trim()
            + '&localidade=' + $('#cp_localidades').val().trim()
            + '&cidade=' + $('#vch_cidade').attr('data-codigo-cidade').trim()
            + '&ed47_v_compl=' + $('#compl').val().trim()
            + '&serie=' + $('#cp_serie').val().trim()
            + '&vch_orgaopublico=' + $('#vch_orgaopublico').val().trim()
            + '&email_resp=' + $('#emailResponsavel').val().trim()
            + '&alunostatusreserva_id=' + $('#alunostatusreserva_id').val().trim()
            + '&observacao=' + $('#observacao').val().trim()
            + '&escola=' + $('#cp_escola').val();

        $("#modal-btn-sim").attr('disabled', 'disabled');
        $("#modal-btn-sim").text('Salvando...');
        $.get(url, function (data) {
            $("#modal-btn-sim").removeAttr('disabled');
            $("#modalConfirmaAlteracao").modal('hide');
            $("#modal-btn-sim").text('Sim');
            let response = JSON.parse(data);
            if (response.status == 'ok') {
                MandaID('','AjaxGetNotificacaoEscola','');
                $('#msg_secesso_modal').modal('show');
                if ($('#alunostatusreserva_id').val() !== '7'){
                    $('#DataAgendamento').hide();
                }
            }
            if (response.status == 'expirou') {
                alert('Esta sessao expirou. Faça login novamente.');
                history.go(-1);
            }
        }).fail((response) => {
            $("#modal-btn-sim").removeAttr('disabled');
            $("#modal-btn-sim").removeAttr('disabled');
            $("#modalConfirmaAlteracao").modal('hide');
            $("#modal-btn-sim").text('Sim');

            alert('Erro inesperado erro:(100)');
        });
        // });

        // $("#modal-btn-nao").on("click", function () {
        //     $("#msg").trigger("click");
        //     $("#msg_text").text("Alteraï¿½ï¿½o cancelada!");
        //     $("#modalConfirmaAlteracao").modal('hide');
        // });
    } else {
        $("#modalConfirmaAlteracao").modal('hide');
    }

}

function NotificaEscola(paginacao) {
    var checkboxNotificaEscola = document.getElementById('NotificarEscola').checked;
    var radioboxConfirmacaoImpressao = document.getElementById('ConfirmacaoImpressaoSim').checked;
    var dataAgendamento = $('#date_agendamento').val();
    var horarioAgendamento = $('#vch_horario_agenda').val();
        $('#modalConfirmaAlteracao').modal('hide');
        $('#msg_secesso_modal').modal('hide');

    if ((checkboxNotificaEscola == true) || (radioboxConfirmacaoImpressao==true)){
        //alert("checkboxNotificaEscola = true ou radioboxConfirmacaoImpressao = true");
        MandaID('notificarEscola','AjaxSetNotificacaoEscola',checkboxNotificaEscola+"&confirmacaoImpressao="+radioboxConfirmacaoImpressao+"&paginacao="+paginacao+"&dataAgendamento="+dataAgendamento+"&horarioAgendamento="+horarioAgendamento);
    }else{
        getForm('app/aluno/lista_matricular.php?paginacao='+paginacao+'&voltaredicao=true');
    }

}

