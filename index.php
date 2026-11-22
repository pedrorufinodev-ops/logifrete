<?php
require_once __DIR__ . '/includes/funcoes.php';

$pdo = conexao();

// Totais para os cartões do painel
$totais = [
    'produtos'        => $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn(),
    'enderecos'       => $pdo->query('SELECT COUNT(*) FROM enderecos')->fetchColumn(),
    'transportadores' => $pdo->query('SELECT COUNT(*) FROM transportadores')->fetchColumn(),
    'fretes'          => $pdo->query('SELECT COUNT(*) FROM fretes')->fetchColumn(),
];

// Últimos fretes cadastrados
$ultimos = $pdo->query(
    'SELECT f.id, f.data_frete, f.status, f.valor_frete, t.nome AS transportador,
            o.cidade AS origem, d.cidade AS destino
       FROM fretes f
       JOIN transportadores t ON t.id = f.transportador_id
       JOIN enderecos o ON o.id = f.origem_id
       JOIN enderecos d ON d.id = f.destino_id
      ORDER BY f.id DESC
      LIMIT 5'
)->fetchAll();

$cartoes = [
    ['Produtos', 'produtos', 'bi-box-seam', 'primary'],
    ['Endereços', 'enderecos', 'bi-geo-alt', 'success'],
    ['Transportadores', 'transportadores', 'bi-person-badge', 'warning'],
    ['Fretes', 'fretes', 'bi-truck', 'danger'],
];

$titulo = 'Painel';
$base = '';
require __DIR__ . '/includes/cabecalho.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 titulo-pagina mb-0">Painel de controle</h1>
        <p class="text-muted mb-0">Resumo da operação logística</p>
    </div>
    <a href="fretes/form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo frete</a>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($cartoes as [$rotulo, $chave, $icone, $cor]): ?>
        <div class="col-6 col-lg-3">
            <a href="<?= $chave ?>/index.php" class="text-decoration-none">
                <div class="card card-resumo h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small"><?= $rotulo ?></div>
                            <div class="numero text-dark"><?= (int) $totais[$chave] ?></div>
                        </div>
                        <i class="bi <?= $icone ?> icone text-<?= $cor ?>"></i>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-clock-history"></i> Últimos fretes</strong>
        <a href="fretes/index.php" class="btn btn-sm btn-outline-primary">Ver todos</a>
    </div>
    <div class="card-body p-0">
        <?php if (!$ultimos): ?>
            <p class="text-muted p-3 mb-0">Nenhum frete cadastrado ainda.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                    <tr><th>#</th><th>Data</th><th>Transportador</th><th>Rota</th><th>Valor</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ultimos as $f): ?>
                        <tr>
                            <td><a href="fretes/ver.php?id=<?= $f['id'] ?>">#<?= $f['id'] ?></a></td>
                            <td><?= data_br($f['data_frete']) ?></td>
                            <td><?= e($f['transportador']) ?></td>
                            <td><?= e($f['origem']) ?> <i class="bi bi-arrow-right"></i> <?= e($f['destino']) ?></td>
                            <td><?= moeda($f['valor_frete']) ?></td>
                            <td><span class="badge text-bg-<?= cor_status($f['status']) ?> badge-status"><?= e($f['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/rodape.php'; ?>
