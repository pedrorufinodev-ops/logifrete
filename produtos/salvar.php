<?php
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('index.php');
}

// Lê os dados enviados pelo formulário (método POST)
$id = (int) ($_POST['id'] ?? 0);
$dados = [
    ':nome'      => trim($_POST['nome'] ?? ''),
    ':descricao' => trim($_POST['descricao'] ?? ''),
    ':peso_kg'   => para_decimal($_POST['peso_kg'] ?? 0),
    ':valor'     => para_decimal($_POST['valor'] ?? 0),
];

// Validação simples no servidor
if ($dados[':nome'] === '') {
    flash('Informe o nome do produto.', 'danger');
    redirecionar('form.php' . ($id ? "?id=$id" : ''));
}

if ($id) {
    $dados[':id'] = $id;
    $sql = conexao()->prepare(
        'UPDATE produtos SET nome = :nome, descricao = :descricao, peso_kg = :peso_kg, valor = :valor WHERE id = :id'
    );
    $sql->execute($dados);
    flash('Produto atualizado com sucesso!');
} else {
    $sql = conexao()->prepare(
        'INSERT INTO produtos (nome, descricao, peso_kg, valor) VALUES (:nome, :descricao, :peso_kg, :valor)'
    );
    $sql->execute($dados);
    flash('Produto cadastrado com sucesso!');
}

redirecionar('index.php');
