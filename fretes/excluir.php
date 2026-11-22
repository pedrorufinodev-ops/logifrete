<?php
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    // Os itens do frete são apagados juntos (ON DELETE CASCADE no banco)
    $sql = conexao()->prepare('DELETE FROM fretes WHERE id = ?');
    $sql->execute([(int) $_POST['id']]);
    flash('Frete excluído.');
}

redirecionar('index.php');
