<?php
require_once __DIR__ . '/../includes/funcoes.php';

$data = $_GET['data'] ?? '';
$status = $_GET['status'] ?? '';

// Monta o filtro dinamicamente, sempre com parâmetros (Prepared Statement)
$filtros = [];
$parametros = [];
if ($data !== '') {
    $filtros[] = 'f.data_frete = :data';
    $parametros[':data'] = $data;
}
if ($status !== '' && isset(STATUS_FRETE[$status])) {
    $filtros[] = 'f.status = :status';
    $parametros[':status'] = $status;
}
$where = $filtros ? 'WHERE ' . implode(' AND ', $filtros) : '';

$sql = conexao()->prepare(
    "SELECT f.*, t.nome AS transportador, o.cidade AS origem_cidade, o.uf AS origem_uf,
            d.cidade AS destino_cidade, d.uf AS destino_uf,
            (SELECT COALESCE(SUM(i.quantidade), 0) FROM frete_itens i WHERE i.frete_id = f.id) AS total_itens
       FROM fretes f
       JOIN transportadores t ON t.id = f.transportador_id
       JOIN enderecos o ON o.id = f.origem_id
       JOIN enderecos d ON d.id = f.destino_id
       $where
      ORDER BY f.data_frete DESC, f.id DESC"
);
$sql->execute($parametros);
$fretes = $sql->fetchAll();

$titulo = 'Fretes';
$base = '../';
require __DIR__ . '/../includes/cabecalho.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 titulo-pagina mb-0"><i class="bi bi-truck"></i> Fretes</h1>
    <div class="d-flex gap-2">
        <a href="relatorio.php" class="btn btn-outline-primary"><i class="bi bi-file-earmark-arrow-down"></i> Relatório do dia</a>
        <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo frete</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form class="row g-2 mb-3" method="get">
            <div class="col-md-3">
                <label for="data" class="form-label small text-muted mb-1">Data</label>
                <input type="date" id="data" name="data" class="form-control" value="<?= e($data) ?>">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label small text-muted mb-1">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach (STATUS_FRETE as $nome => $cor): ?>
                        <option <?= $status === $nome ? 'selected' : '' ?>><?= $nome ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto d-flex align-items-end gap-2">
                <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="index.php" class="btn btn-link">Limpar</a>
            </div>
        </form>

        <?php if (!$fretes): ?>
            <p class="text-muted mb-0">Nenhum frete encontrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr><th>#</th><th>Data</th><th>Transportador</th><th>Origem → Destino</th><th>Itens</th>
                        <th>Valor do frete</th><th>Status</th><th class="text-end">Ações</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($fretes as $f): ?>
                        <tr>
                            <td>#<?= $f['id'] ?></td>
                            <td><?= data_br($f['data_frete']) ?></td>
                            <td><?= e($f['transportador']) ?></td>
                            <td><?= e($f['origem_cidade'] . '/' . $f['origem_uf']) ?>
                                <i class="bi bi-arrow-right"></i>
                                <?= e($f['destino_cidade'] . '/' . $f['destino_uf']) ?></td>
                            <td><?= (int) $f['total_itens'] ?></td>
                            <td><?= moeda($f['valor_frete']) ?></td>
                            <td><span class="badge text-bg-<?= cor_status($f['status']) ?> badge-status"><?= e($f['status']) ?></span></td>
                            <td class="text-end text-nowrap">
                                <a href="ver.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary" title="Detalhes">
                                    <i class="bi bi-eye"></i></a>
                                <a href="form.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i></a>
                                <form action="excluir.php" method="post" class="d-inline"
                                      data-confirmar="Excluir o frete #<?= $f['id'] ?>?">
                                    <input type="hidden" name="id" value="<?= $f['id'] ?>">
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
