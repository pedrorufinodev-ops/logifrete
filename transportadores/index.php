<?php
require_once __DIR__ . '/../includes/funcoes.php';

$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {
    $sql = conexao()->prepare(
        'SELECT * FROM transportadores WHERE nome LIKE :b OR cpf_cnpj LIKE :b OR placa LIKE :b ORDER BY nome'
    );
    $sql->execute([':b' => "%$busca%"]);
} else {
    $sql = conexao()->query('SELECT * FROM transportadores ORDER BY nome');
}
$transportadores = $sql->fetchAll();

$titulo = 'Transportadores';
$base = '../';
require __DIR__ . '/../includes/cabecalho.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 titulo-pagina mb-0"><i class="bi bi-person-badge"></i> Transportadores</h1>
    <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo transportador</a>
</div>

<div class="card">
    <div class="card-body">
        <form class="row g-2 mb-3" method="get">
            <div class="col-md-6">
                <input type="search" name="busca" class="form-control" placeholder="Buscar por nome, CPF/CNPJ ou placa"
                       value="<?= e($busca) ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Buscar</button>
            </div>
        </form>

        <?php if (!$transportadores): ?>
            <p class="text-muted mb-0">Nenhum transportador encontrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr><th>ID</th><th>Nome</th><th>CPF/CNPJ</th><th>Contato</th><th>Veículo</th><th class="text-end">Ações</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($transportadores as $t): ?>
                        <tr>
                            <td><?= $t['id'] ?></td>
                            <td class="fw-semibold"><?= e($t['nome']) ?></td>
                            <td><?= e($t['cpf_cnpj']) ?></td>
                            <td><?= e($t['telefone']) ?><br><small class="text-muted"><?= e($t['email']) ?></small></td>
                            <td><?= e($t['veiculo']) ?><br><small class="text-muted"><?= e($t['placa']) ?></small></td>
                            <td class="text-end text-nowrap">
                                <a href="form.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i></a>
                                <form action="excluir.php" method="post" class="d-inline"
                                      data-confirmar="Excluir o transportador <?= e($t['nome']) ?>?">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
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
