<?php
require_once __DIR__ . '/../includes/funcoes.php';

$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {
    $sql = conexao()->prepare('SELECT * FROM produtos WHERE nome LIKE :busca OR descricao LIKE :busca ORDER BY nome');
    $sql->execute([':busca' => "%$busca%"]);
} else {
    $sql = conexao()->query('SELECT * FROM produtos ORDER BY nome');
}
$produtos = $sql->fetchAll();

$titulo = 'Produtos';
$base = '../';
require __DIR__ . '/../includes/cabecalho.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 titulo-pagina mb-0"><i class="bi bi-box-seam"></i> Produtos</h1>
    <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo produto</a>
</div>

<div class="card">
    <div class="card-body">
        <form class="row g-2 mb-3" method="get">
            <div class="col-md-6">
                <input type="search" name="busca" class="form-control" placeholder="Buscar por nome ou descrição"
                       value="<?= e($busca) ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Buscar</button>
            </div>
        </form>

        <?php if (!$produtos): ?>
            <p class="text-muted mb-0">Nenhum produto encontrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Peso</th><th>Valor</th><th class="text-end">Ações</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td class="fw-semibold"><?= e($p['nome']) ?></td>
                            <td><?= e($p['descricao']) ?></td>
                            <td><?= number_format($p['peso_kg'], 2, ',', '.') ?> kg</td>
                            <td><?= moeda($p['valor']) ?></td>
                            <td class="text-end text-nowrap">
                                <a href="form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i></a>
                                <form action="excluir.php" method="post" class="d-inline"
                                      data-confirmar="Excluir o produto <?= e($p['nome']) ?>?">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" title="Excluir"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/rodape.php'; ?>
