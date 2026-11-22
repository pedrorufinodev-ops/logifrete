<?php
require_once __DIR__ . '/../includes/funcoes.php';

// Data escolhida (padrão: hoje). Valida o formato AAAA-MM-DD.
$data = $_GET['data'] ?? date('Y-m-d');
if (!DateTime::createFromFormat('Y-m-d', $data)) {
    $data = date('Y-m-d');
}

// Busca os fretes do dia, um registro por produto transportado
$sql = conexao()->prepare(
    'SELECT f.id, f.data_frete, f.valor_frete, f.status, t.nome AS transportador, t.placa,
            o.cidade AS origem_cidade, o.uf AS origem_uf, d.cidade AS destino_cidade, d.uf AS destino_uf,
            p.nome AS produto, i.quantidade, p.peso_kg, p.valor
       FROM fretes f
       JOIN transportadores t ON t.id = f.transportador_id
       JOIN enderecos o ON o.id = f.origem_id
       JOIN enderecos d ON d.id = f.destino_id
       JOIN frete_itens i ON i.frete_id = f.id
       JOIN produtos p ON p.id = i.produto_id
      WHERE f.data_frete = ?
      ORDER BY f.id, p.nome'
);
$sql->execute([$data]);
$linhas = $sql->fetchAll();

// Agrupa as linhas por frete (transformação dos dados feita em PHP)
$fretes = [];
foreach ($linhas as $l) {
    $id = $l['id'];
    if (!isset($fretes[$id])) {
        $fretes[$id] = [
            'id'            => $id,
            'transportador' => $l['transportador'],
            'placa'         => $l['placa'],
            'rota'          => $l['origem_cidade'] . '/' . $l['origem_uf'] . ' → ' . $l['destino_cidade'] . '/' . $l['destino_uf'],
            'status'        => $l['status'],
            'valor_frete'   => (float) $l['valor_frete'],
            'peso'          => 0,
            'produtos'      => [],
        ];
    }
    $fretes[$id]['peso'] += $l['peso_kg'] * $l['quantidade'];
    $fretes[$id]['produtos'][] = $l['quantidade'] . 'x ' . $l['produto'];
}

$totalFretes = array_sum(array_column($fretes, 'valor_frete'));
$totalPeso = array_sum(array_column($fretes, 'peso'));

// ===== Download em CSV (abre no Excel) =====
if (($_GET['baixar'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="relatorio_fretes_' . $data . '.csv"');

    $saida = fopen('php://output', 'w');
    fwrite($saida, "\xEF\xBB\xBF"); // BOM para o Excel reconhecer acentos
    fputcsv($saida, ['Relatório de fretes do dia ' . data_br($data)], ';');
    fputcsv($saida, ['Frete', 'Transportador', 'Placa', 'Rota', 'Produtos', 'Peso total (kg)', 'Valor do frete (R$)', 'Status'], ';');
    foreach ($fretes as $f) {
        fputcsv($saida, [
            '#' . $f['id'],
            $f['transportador'],
            $f['placa'],
            $f['rota'],
            implode(' | ', $f['produtos']),
            number_format($f['peso'], 2, ',', '.'),
            number_format($f['valor_frete'], 2, ',', '.'),
            $f['status'],
        ], ';');
    }
    fputcsv($saida, [], ';');
    fputcsv($saida, ['TOTAL', count($fretes) . ' frete(s)', '', '', '',
        number_format($totalPeso, 2, ',', '.'), number_format($totalFretes, 2, ',', '.'), ''], ';');
    fclose($saida);
    exit;
}

$titulo = 'Relatório de fretes';
$base = '../';
require __DIR__ . '/../includes/cabecalho.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 titulo-pagina mb-0"><i class="bi bi-file-earmark-text"></i> Relatório de fretes do dia</h1>
</div>

<div class="card mb-3 nao-imprimir">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="get">
            <div class="col-md-3">
                <label for="data" class="form-label">Escolha o dia</label>
                <input type="date" id="data" name="data" class="form-control" value="<?= e($data) ?>" required>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary"><i class="bi bi-search"></i> Gerar relatório</button>
            </div>
            <?php if ($fretes): ?>
                <div class="col-auto ms-md-auto d-flex gap-2">
                    <a href="?data=<?= e($data) ?>&baixar=csv" class="btn btn-success">
                        <i class="bi bi-filetype-csv"></i> Baixar CSV (Excel)</a>
                    <button type="button" onclick="window.print()" class="btn btn-outline-danger">
                        <i class="bi bi-filetype-pdf"></i> Salvar em PDF</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h5 mb-3">Fretes de <?= data_br($data) ?></h2>

        <?php if (!$fretes): ?>
            <p class="text-muted mb-0">Nenhum frete cadastrado para este dia.</p>
        <?php else: ?>
            <div class="row g-3 mb-3">
                <div class="col-sm-4"><div class="border rounded p-3"><div class="text-muted small">Fretes</div>
                    <div class="fs-4 fw-bold"><?= count($fretes) ?></div></div></div>
                <div class="col-sm-4"><div class="border rounded p-3"><div class="text-muted small">Peso transportado</div>
                    <div class="fs-4 fw-bold"><?= number_format($totalPeso, 2, ',', '.') ?> kg</div></div></div>
                <div class="col-sm-4"><div class="border rounded p-3"><div class="text-muted small">Faturamento em fretes</div>
                    <div class="fs-4 fw-bold"><?= moeda($totalFretes) ?></div></div></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                    <tr><th>Frete</th><th>Transportador</th><th>Rota</th><th>Produtos</th><th>Peso</th><th>Valor</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($fretes as $f): ?>
                        <tr>
                            <td>#<?= $f['id'] ?></td>
                            <td><?= e($f['transportador']) ?><br><small class="text-muted"><?= e($f['placa']) ?></small></td>
                            <td><?= e($f['rota']) ?></td>
                            <td><?= e(implode(', ', $f['produtos'])) ?></td>
                            <td class="text-nowrap"><?= number_format($f['peso'], 2, ',', '.') ?> kg</td>
                            <td class="text-nowrap"><?= moeda($f['valor_frete']) ?></td>
                            <td><span class="badge text-bg-<?= cor_status($f['status']) ?>"><?= e($f['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/rodape.php'; ?>
