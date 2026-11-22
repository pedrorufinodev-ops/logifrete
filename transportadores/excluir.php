<?php
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    try {
        $sql = conexao()->prepare('DELETE FROM transportadores WHERE id = ?');
        $sql->execute([(int) $_POST['id']]);
        flash('Transportador excluído.');
    } catch (PDOException $e) {
        flash('Não é possível excluir: este transportador possui fretes cadastrados.', 'danger');
    }
}

redirecionar('index.php');
