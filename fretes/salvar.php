<?php
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('index.php');
}

$pdo = conexao();
$id = (int) ($_POST['id'] ?? 0);
$voltar = 'form.php' . ($id ? "?id=$id" : '');

$frete = [
    ':transportador_id' => (int) ($_POST['transportador_id'] ?? 0),
    ':origem_id'        => (int) ($_POST['origem_id'] ?? 0),
    ':destino_id'       => (int) ($_POST['destino_id'] ?? 0),
    ':data_frete'       => $_POST['data_frete'] ?? '',
    ':valor_frete'      => para_decimal($_POST['valor_frete'] ?? 0),
    ':status'           => $_POST['status'] ?? 'Pendente',
    ':observacao'       => trim($_POST['observacao'] ?? ''),
];

// Junta os arrays produto_id[] e quantidade[] enviados pelos campos dinâmicos.
// Se o mesmo produto aparecer duas vezes, soma as quantidades.
$itens = [];
foreach ($_POST['produto_id'] ?? [] as $i => $produtoId) {
    $produtoId = (int) $produtoId;
    $quantidade = max(1, (int) ($_POST['quantidade'][$i] ?? 1));
    if ($produtoId > 0) {
        $itens[$produtoId] = ($itens[$produtoId] ?? 0) + $quantidade;
    }
}

// Validações
$erro = null;
if (!$frete[':transportador_id'] || !$frete[':origem_id'] || !$frete[':destino_id'] || !$frete[':data_frete']) {
    $erro = 'Preencha transportador, origem, destino e data.';
} elseif ($frete[':origem_id'] === $frete[':destino_id']) {
    $erro = 'A origem e o destino precisam ser diferentes.';
} elseif (!$itens) {
    $erro = 'Adicione pelo menos um produto ao frete.';
} elseif (!isset(STATUS_FRETE[$frete[':status']])) {
    $erro = 'Status inválido.';
}
if ($erro) {
    flash($erro, 'danger');
    redirecionar($voltar);
}

// Transação: o frete e seus itens são gravados juntos (ou nada é gravado)
try {
    $pdo->beginTransaction();

    if ($id) {
        $frete[':id'] = $id;
        $sql = $pdo->prepare(
            'UPDATE fretes SET transportador_id = :transportador_id, origem_id = :origem_id, destino_id = :destino_id,
                    data_frete = :data_frete, valor_frete = :valor_frete, status = :status, observacao = :observacao
              WHERE id = :id'
        );
        $sql->execute($frete);
        // Remove os itens antigos para regravar a lista atual
        $pdo->prepare('DELETE FROM frete_itens WHERE frete_id = ?')->execute([$id]);
    } else {
        $sql = $pdo->prepare(
            'INSERT INTO fretes (transportador_id, origem_id, destino_id, data_frete, valor_frete, status, observacao)
             VALUES (:transportador_id, :origem_id, :destino_id, :data_frete, :valor_frete, :status, :observacao)'
        );
        $sql->execute($frete);
        $id = (int) $pdo->lastInsertId();
    }

    $sqlItem = $pdo->prepare('INSERT INTO frete_itens (frete_id, produto_id, quantidade) VALUES (?, ?, ?)');
    foreach ($itens as $produtoId => $quantidade) {
        $sqlItem->execute([$id, $produtoId, $quantidade]);
    }

    $pdo->commit();
    flash('Frete #' . $id . ' salvo com sucesso!');
    redirecionar('ver.php?id=' . $id);
} catch (PDOException $e) {
    $pdo->rollBack();
    flash('Erro ao salvar o frete: ' . $e->getMessage(), 'danger');
    redirecionar($voltar);
}
