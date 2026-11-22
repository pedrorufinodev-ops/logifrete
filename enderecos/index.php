<?php
require_once __DIR__ . '/../includes/funcoes.php';

$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {
    $sql = conexao()->prepare(
        'SELECT * FROM enderecos WHERE apelido LIKE :b OR cidade LIKE :b OR cep LIKE :b ORDER BY apelido'
    );
    $sql->execute([':b' => "%$busca%"]);
} else {
    $sql = conexao()->query('SELECT * FROM enderecos ORDER BY apelido');
}
$enderecos = $sql->fetchAll();

$titulo = 'Endereços';
$base = '../';
require __DIR__ . '/../includes/cabecalho.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 titulo-pagina mb-0"><i class="bi bi-geo-alt"></i> Endereços</h1>
    <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo endereço</a>
</div>

<div class="card">
    <div class="card-body">
        <form class="row g-2 mb-3" method="get">
            <div class="col-md-6">
                <input type="search" name="busca" class="form-control" placeholder="Buscar por apelido, cidade ou CEP"
                       value="<?= e($busca) ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Buscar</button>
            </div>
        </form>

        <?php if (!$enderecos): ?>
            <p class="text-muted mb-0">Nenhum endereço encontrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr><th>ID</th><th>Apelido</th><th>CEP</th><th>Endereço</th><th class="text-end">Ações</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($enderecos as $end): ?>
                        <tr>
                            <td><?= $end['id'] ?></td>
                            <td class="fw-semibold"><?= e($end['apelido']) ?></td>
                            <td><?= e($end['cep']) ?></td>
                            <td><?= e(endereco_linha($end)) ?></td>
                            <td class="text-end text-nowrap">
                                <a href="form.php?id=<?= $end['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i></a>
                                <form action="excluir.php" method="post" class="d-inline"
                                      data-confirmar="Excluir o endereço <?= e($end['apelido']) ?>?">
                                    <input type="hidden" name="id" value="<?= $end['id'] ?>">
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
