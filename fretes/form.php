<?php
require_once __DIR__ . '/../includes/funcoes.php';

$pdo = conexao();

// Listas para preencher os selects
$transportadores = $pdo->query('SELECT id, nome, veiculo FROM transportadores ORDER BY nome')->fetchAll();
$enderecos = $pdo->query('SELECT * FROM enderecos ORDER BY apelido')->fetchAll();
$produtos = $pdo->query('SELECT id, nome, peso_kg, valor FROM produtos ORDER BY nome')->fetchAll();

$frete = [
    'id' => '', 'transportador_id' => '', 'origem_id' => '', 'destino_id' => '',
    'data_frete' => date('Y-m-d'), 'valor_frete' => '', 'status' => 'Pendente', 'observacao' => '',
];
$itens = [];

if (!empty($_GET['id'])) {
    $sql = $pdo->prepare('SELECT * FROM fretes WHERE id = ?');
    $sql->execute([(int) $_GET['id']]);
    $frete = $sql->fetch();
    if (!$frete) {
        flash('Frete não encontrado.', 'danger');
        redirecionar('index.php');
    }
    $sql = $pdo->prepare('SELECT produto_id, quantidade FROM frete_itens WHERE frete_id = ?');
    $sql->execute([$frete['id']]);
    $itens = $sql->fetchAll();
}

// Só é possível gerar frete se já existirem os cadastros básicos
$faltando = [];
if (!$transportadores) $faltando[] = '<a href="../transportadores/form.php">transportador</a>';
if (count($enderecos) < 2) $faltando[] = '<a href="../enderecos/form.php">dois endereços (origem e destino)</a>';
if (!$produtos) $faltando[] = '<a href="../produtos/form.php">produto</a>';

$editando = !empty($frete['id']);
$titulo = $editando ? 'Editar frete #' . $frete['id'] : 'Novo frete';
$base = '../';
$scripts = $faltando ? [] : ['assets/js/frete.js'];
require __DIR__ . '/../includes/cabecalho.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Fretes</a></li>
        <li class="breadcrumb-item active"><?= e($titulo) ?></li>
    </ol>
</nav>

<?php if ($faltando): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        Antes de gerar um frete, cadastre pelo menos: <?= implode(', ', $faltando) ?>.
    </div>
<?php else: ?>

<form action="salvar.php" method="post" id="form-frete">
    <input type="hidden" name="id" value="<?= e($frete['id']) ?>">

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <h1 class="h4 titulo-pagina mb-4"><?= e($titulo) ?></h1>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="transportador_id" class="form-label">Transportador *</label>
                            <select id="transportador_id" name="transportador_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($transportadores as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= $frete['transportador_id'] == $t['id'] ? 'selected' : '' ?>>
                                        <?= e($t['nome'] . ($t['veiculo'] ? ' — ' . $t['veiculo'] : '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="origem_id" class="form-label"><i class="bi bi-box-arrow-up-right text-primary"></i> Origem *</label>
                            <select id="origem_id" name="origem_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($enderecos as $end): ?>
                                    <option value="<?= $end['id'] ?>" <?= $frete['origem_id'] == $end['id'] ? 'selected' : '' ?>>
                                        <?= e($end['apelido'] . ' (' . $end['cidade'] . '/' . $end['uf'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="destino_id" class="form-label"><i class="bi bi-geo-alt-fill text-danger"></i> Destino *</label>
                            <select id="destino_id" name="destino_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($enderecos as $end): ?>
                                    <option value="<?= $end['id'] ?>" <?= $frete['destino_id'] == $end['id'] ? 'selected' : '' ?>>
                                        <?= e($end['apelido'] . ' (' . $end['cidade'] . '/' . $end['uf'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="data_frete" class="form-label">Data do frete *</label>
                            <input type="date" id="data_frete" name="data_frete" class="form-control" required
                                   value="<?= e($frete['data_frete']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="valor_frete" class="form-label">Valor do frete (R$) *</label>
                            <input type="number" id="valor_frete" name="valor_frete" class="form-control" step="0.01" min="0"
                                   required value="<?= e($frete['valor_frete']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <?php foreach (STATUS_FRETE as $nome => $cor): ?>
                                    <option <?= $frete['status'] === $nome ? 'selected' : '' ?>><?= $nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="observacao" class="form-label">Observação</label>
                            <textarea id="observacao" name="observacao" class="form-control" rows="2" maxlength="255"><?= e($frete['observacao']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0"><i class="bi bi-box-seam"></i> Produtos da carga</h2>
                        <button type="button" id="adicionar-item" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-lg"></i> Adicionar produto
                        </button>
                    </div>

                    <!-- As linhas de produto são criadas pelo JavaScript (assets/js/frete.js) -->
                    <div id="lista-itens" class="d-grid gap-2" data-itens="<?= e(json_encode($itens)) ?>"></div>

                    <div class="border-top mt-3 pt-3 d-flex justify-content-between">
                        <span>Peso total: <strong id="total-peso">0,00 kg</strong></span>
                        <span>Valor da carga: <strong id="total-valor">R$ 0,00</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg"></i> Salvar frete</button>
        <a href="index.php" class="btn btn-outline-secondary btn-lg">Cancelar</a>
    </div>
</form>

<!-- Modelo de linha de produto, clonado pelo JavaScript -->
<template id="modelo-item">
    <div class="item-produto">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small fw-semibold text-muted">Item <span class="numero-item">1</span></span>
            <button type="button" class="btn btn-sm btn-outline-danger remover-item" title="Remover produto">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="row g-2">
            <div class="col-8">
                <select name="produto_id[]" class="form-select form-select-sm" required>
                    <option value="">Selecione o produto...</option>
                    <?php foreach ($produtos as $p): ?>
                        <option value="<?= $p['id'] ?>" data-peso="<?= $p['peso_kg'] ?>" data-valor="<?= $p['valor'] ?>">
                            <?= e($p['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-4">
                <input type="number" name="quantidade[]" class="form-control form-control-sm" min="1" value="1"
                       required aria-label="Quantidade">
            </div>
        </div>
    </div>
</template>

<?php endif; ?>

<?php require __DIR__ . '/../includes/rodape.php'; ?>
