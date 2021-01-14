$('#cp_data_nascimento_editar').mask('00/00/0000');



function findAluno() {

    let url= 'app/aluno/ajax/aluno_AJAX.php';
    let data = {
        funcao:'findAluno',
        aluno: $('#cp_filtrar_aluno').val(),
        data_nascimento: $('#cp_data_nascimento_editar').val(),
        responsavel: $('#cp_responsavel_editar').val(),
        status_id: $('#alunostatusreserva_id').val(),
    };
    $.get(url,data,function(data){
        $('#table_alunos').html(data);
    });
    getRegistros();
}

