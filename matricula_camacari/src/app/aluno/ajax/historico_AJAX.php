<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            <label for="vch_codigo_aba2" id="labelCodigo">Código:</label>
            <input class="form-control" type="text" name="vch_codigo_aba2" id="vch_codigo_aba2" readonly value="<?php echo trim(@$aluno['id_alunoreserva']) ?>" />
        </div>
        <div class="col">
            <label for="vch_nome" id="labelNome_aba2">Nome:</label>
            <input class="form-control" type="text" name="vch_nome_aba2" id="vch_nome_aba2" readonly value="<?php echo @$aluno['ed47_v_nome'] ?>" onkeyup="this.value = this.value.toUpperCase();" onKeyPress="mudarCorCampo('labelNome', 'vch_nome')" />
        </div>
    </div>
</div>
<br>
<h6> Histórico do Cadastro</h6>
<table class="table table-striped">
    <thead class="thead-inverse">
        <tr>
            <th>Usuário</th>
            <th>Ação</th>
            <th>Data/Hora</th>
        </tr>
    </thead>
    <tbody>
        <?php
        //var_dump($auditoriasCadastrados);
        // Lista a auditoria do cadastro do registro da lista de espera
        foreach ($auditoriasCadastrados as $auditoriasCadastrado) {
            
            echo "<tr>";
            echo "<td>" . $auditoriasCadastrado['ed47_v_nome'] . "</td>";
            echo "<td>" . strtoupper($auditoriasCadastrado['adr_v_acao']) . "</td>";
            echo "<td>" . date('d-m-Y H:i:s', strtotime($auditoriasCadastrado['adr_d_data'])) . "</td>";
            echo "</tr>";
        }
        // Lista a auditoria da alteração do registro da lista de espera 
        foreach ($auditorias as $auditoria) {
            echo "<tr>";
            echo "<td>" . $auditoria['nome_usuario'] . "</td>";
            echo "<td>" . strtoupper($auditoria['descricao']) . "</td>";
            echo "<td>" . date('d-m-Y H:i:s', strtotime($auditoria['data_modificacao'])) . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
