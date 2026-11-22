<?php
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    try {
        $sql = conexao()->prepare('DELETE FROM enderecos WHERE id = ?');
        $sql->execute([(int) $_POST['id']]);
        flash('Endereço excluído.');
    } catch (PDOException $e) {
        flash('Não é possível excluir: este endereço é origem ou destino de um frete.', 'danger');
    }
}

redirecionar('index.php');
