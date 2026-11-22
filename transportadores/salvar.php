<?php
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('index.php');
}

$id = (int) ($_POST['id'] ?? 0);
$dados = [
    ':nome'     => trim($_POST['nome'] ?? ''),
    ':cpf_cnpj' => trim($_POST['cpf_cnpj'] ?? ''),
    ':telefone' => trim($_POST['telefone'] ?? ''),
    ':email'    => trim($_POST['email'] ?? ''),
    ':veiculo'  => trim($_POST['veiculo'] ?? ''),
    ':placa'    => strtoupper(trim($_POST['placa'] ?? '')),
];

if ($dados[':nome'] === '' || $dados[':cpf_cnpj'] === '') {
    flash('Nome e CPF/CNPJ são obrigatórios.', 'danger');
    redirecionar('form.php' . ($id ? "?id=$id" : ''));
}

if ($dados[':email'] !== '' && !filter_var($dados[':email'], FILTER_VALIDATE_EMAIL)) {
    flash('Informe um e-mail válido.', 'danger');
    redirecionar('form.php' . ($id ? "?id=$id" : ''));
}

if ($id) {
    $dados[':id'] = $id;
    $sql = conexao()->prepare(
        'UPDATE transportadores SET nome = :nome, cpf_cnpj = :cpf_cnpj, telefone = :telefone, email = :email,
                veiculo = :veiculo, placa = :placa
          WHERE id = :id'
    );
    $sql->execute($dados);
    flash('Transportador atualizado com sucesso!');
} else {
    $sql = conexao()->prepare(
        'INSERT INTO transportadores (nome, cpf_cnpj, telefone, email, veiculo, placa)
         VALUES (:nome, :cpf_cnpj, :telefone, :email, :veiculo, :placa)'
    );
    $sql->execute($dados);
    flash('Transportador cadastrado com sucesso!');
}

redirecionar('index.php');
