<?php
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('index.php');
}

$id = (int) ($_POST['id'] ?? 0);
$campos = ['apelido', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf'];
$dados = [];
foreach ($campos as $campo) {
    $dados[":$campo"] = trim($_POST[$campo] ?? '');
}
$dados[':uf'] = strtoupper($dados[':uf']);

// Todos os campos são obrigatórios, exceto complemento
foreach ($campos as $campo) {
    if ($campo !== 'complemento' && $dados[":$campo"] === '') {
        flash('Preencha todos os campos obrigatórios.', 'danger');
        redirecionar('form.php' . ($id ? "?id=$id" : ''));
    }
}

if ($id) {
    $dados[':id'] = $id;
    $sql = conexao()->prepare(
        'UPDATE enderecos SET apelido = :apelido, cep = :cep, logradouro = :logradouro, numero = :numero,
                complemento = :complemento, bairro = :bairro, cidade = :cidade, uf = :uf
          WHERE id = :id'
    );
    $sql->execute($dados);
    flash('Endereço atualizado com sucesso!');
} else {
    $sql = conexao()->prepare(
        'INSERT INTO enderecos (apelido, cep, logradouro, numero, complemento, bairro, cidade, uf)
         VALUES (:apelido, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf)'
    );
    $sql->execute($dados);
    flash('Endereço cadastrado com sucesso!');
}

redirecionar('index.php');
