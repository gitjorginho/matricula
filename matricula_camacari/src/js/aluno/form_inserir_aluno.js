$('#vch_telefone').mask('(00)0 0000-0000');
$('#vch_cpf').mask('000.000.000-00', {reverse: true});
$('#sdt_nascimento').mask('00/00/0000');



$('#cp_texto').keydown(function () {
    let resposta = $('#resposta');
    let url = "app/aluno/ajax/localidade_AJAX.php";
    let data = {
        endereco: $('#cp_texto').val(),
        funcao: "localizar_endereco",
    };
    $.get(url, data, function (data) {
        resposta.html(data);
        resposta.css('display', 'block');
    });

});

//function pegarValores(cp) {
//    let component = $(cp);
//    let localidade = component.find(':selected').val();
//    let url = "app/aluno/ajax/localidade_AJAX.php";
//    let data = {
//        endereco: localidade,
//        funcao: "carregar_endereco",
//    };
//    $.get(url, data, function (data) {
//        let endereco = JSON.parse(data);

//        $('#vch_bairro').val(endereco.bairro);
//        $('#vch_cidade').val(endereco.cidade);
//        $('#vch_cep').val(endereco.cep);
//        $('#ender').val(endereco.endereco);
//        loadLocalidade(endereco.codigo_bairro);
//    });/
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

function validaForm() {
    if ($('#vch_nome').val().trim() == '') {
        $('#vch_nome').css('border', 'red 1px solid');
        return false;
    } else {
        $('#vch_nome').css('border', 'gray 1px solid');
    }

    if ($('#sdt_nascimento').val().trim() == '') {
        $('#sdt_nascimento').css('border', 'red 1px solid');
        return false;
    } else {
        $('#sdt_nascimento').css('border', 'gray 1px solid');
    }

    if ($('#cp_sexo').val().trim() == '') {
        $('#cp_sexo').css('border', 'red 1px solid');
        return false;
    } else {
        $('#cp_sexo').css('border', 'gray 1px solid');
    }

    if ($('#vch_mae').val().trim() == '') {
        $('#vch_mae').css('border', 'red 1px solid');
        return false;
    } else {
        $('#vch_mae').css('border', 'gray 1px solid');
    }

    if ($('#cp_sexo').val().trim() == '') {
        $('#cp_sexo').css('border', 'red 1px solid');
        return false;
    } else {
        $('#cp_sexo').css('border', 'gray 1px solid');
    }
    if ($('#vch_responsavel').val().trim() == '') {
        $('#vch_responsavel').css('border', 'red 1px solid');
        return false;
    } else {
        $('#vch_responsavel').css('border', 'gray 1px solid');
    }

    if ($('#ender').val().trim() == '') {
        $('#ender').css('border', 'red 1px solid');
        return false;
    } else {
        $('#ender').css('border', 'gray 1px solid');
    }

    if ($('#numero').val().trim() == '') {
        $('#numero').css('border', 'red 1px solid');
        return false;
    } else {
        $('#numero').css('border', 'gray 1px solid');
    }

    if ($('#cp_localidades').val().trim() == '') {
        $('#cp_localidades').css('border', 'red 1px solid');
        return false;
    } else {
        $('#cp_localidades').css('border', 'gray 1px solid');
    }

}


function TestaCPF(elemento) {
    cpf = elemento.value;
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
      
      return alert("CPF Inválido");
    // Valida 1o digito 
    add = 0;
    for (i = 0; i < 9; i++)
      add += parseInt(cpf.charAt(i)) * (10 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
      rev = 0;
    if (rev != parseInt(cpf.charAt(9)))
       
        return alert("CPF Invalido");
        
        
    // Valida 2o digito 
    add = 0;
    for (i = 0; i < 10; i++)
      add += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
      rev = 0;
    if (rev != parseInt(cpf.charAt(10)))
    return alert("CPF Invalido");
    return true;
  }

function TestaNome(){

        nome = $("#vch_nome").val();
        
        if(nome.match(/\w+\s+\w/) && nome.trim().split(' ').length > 1){
          return true;
        }else{
            alert("Nome Inválido");
            return false;
        }
    }
function TestaMae(){

            nome = $("#vch_mae").val();
            
            if(nome.match(/\w+\s+\w/) && nome.trim().split(' ').length > 1){
                return true;
              }else{
                  alert("Nome da Mãe Inválido");
                  return false;
              }
}

function TestaResponsavel(){

    nome = $("#vch_responsavel").val();
    
    if(nome.match(/\w+\s+\w/) && nome.trim().split(' ').length > 1){
        return true;
      }else{
          alert("Nome do Responsável Inválido");
          return false;
      }

}

function TestaData(obj){

    var data = obj.value;
    var dia = data.substring(0,2)
    var mes = data.substring(3,5)
    var ano = data.substring(6,10)

    //Criando um objeto Date usando os valores ano, mes e dia.
    var novaData = new Date(ano,(mes-1),dia);

    var mesmoDia = parseInt(dia,10) == parseInt(novaData.getDate());
    var mesmoMes = parseInt(mes,10) == parseInt(novaData.getMonth())+1;
    var mesmoAno = parseInt(ano) == parseInt(novaData.getFullYear());

    if (!((mesmoDia) && (mesmoMes) && (mesmoAno)))
    {
        alert('Data informada é inválida!');   
        obj.focus();    
        return false;
    }  
    return true;
}

