<?php
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    try {
        $sql = conexao()->prepare('DELETE FROM produtos WHERE id = ?');
        $sql->execute([(int) $_POST['id']]);
        flash('Produto excluído.');
    } catch (PDOException $e) {
        // Não deixa excluir produto que já está em algum frete (chave estrangeira)
        flash('Não é possível excluir: este produto está vinculado a um frete.', 'danger');
    }
}

redirecionar('index.php');
