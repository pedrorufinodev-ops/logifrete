<?php
require_once __DIR__ . '/../includes/funcoes.php';

$pdo = conexao();
$id = (int) ($_GET['id'] ?? 0);

// Busca o frete com o transportador e os dois endereços (origem e destino)
$sql = $pdo->prepare(
    'SELECT f.*, t.nome AS t_nome, t.cpf_cnpj AS t_doc, t.telefone AS t_telefone, t.veiculo AS t_veiculo, t.placa AS t_placa,
            o.apelido AS o_apelido, o.cep AS o_cep, o.logradouro AS o_logradouro, o.numero AS o_numero,
            o.bairro AS o_bairro, o.cidade AS o_cidade, o.uf AS o_uf,
            d.apelido AS d_apelido, d.cep AS d_cep, d.logradouro AS d_logradouro, d.numero AS d_numero,
            d.bairro AS d_bairro, d.cidade AS d_cidade, d.uf AS d_uf
       FROM fretes f
       JOIN transportadores t ON t.id = f.transportador_id
       JOIN enderecos o ON o.id = f.origem_id
       JOIN enderecos d ON d.id = f.destino_id
      WHERE f.id = ?'
);
$sql->execute([$id]);
$frete = $sql->fetch();

if (!$frete) {
    flash('Frete não encontrado.', 'danger');
    redirecionar('index.php');
}

$sql = $pdo->prepare(
    'SELECT p.nome, p.peso_kg, p.valor, i.quantidade
       FROM frete_itens i
       JOIN produtos p ON p.id = i.produto_id
      WHERE i.frete_id = ?
      ORDER BY p.nome'
);
$sql->execute([$id]);
$itens = $sql->fetchAll();

$pesoTotal = 0;
$valorCarga = 0;
foreach ($itens as $item) {
    $pesoTotal += $item['peso_kg'] * $item['quantidade'];
    $valorCarga += $item['valor'] * $item['quantidade'];
}

$titulo = 'Frete #' . $id;
$base = '../';
require __DIR__ . '/../includes/cabecalho.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 titulo-pagina mb-0">
        Frete #<?= $id ?>
        <span class="badge text-bg-<?= cor_status($frete['status']) ?> fs-6 align-middle"><?= e($frete['status']) ?></span>
    </h1>
    <div class="d-flex gap-2 nao-imprimir">
        <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> Imprimir</button>
        <a href="form.php?id=<?= $id ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Editar</a>
        <a href="index.php" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6 text-primary text-uppercase"><i class="bi bi-box-arrow-up-right"></i> Origem</h2>
                <p class="fw-semibold mb-1"><?= e($frete['o_apelido']) ?></p>
                <p class="small text-muted mb-0"><?= e(endereco_linha($frete, 'o_')) ?><br>CEP <?= e($frete['o_cep']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6 text-danger text-uppercase"><i class="bi bi-geo-alt-fill"></i> Destino</h2>
                <p class="fw-semibold mb-1"><?= e($frete['d_apelido']) ?></p>
                <p class="small text-muted mb-0"><?= e(endereco_linha($frete, 'd_')) ?><br>CEP <?= e($frete['d_cep']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6 text-warning text-uppercase"><i class="bi bi-person-badge"></i> Transportador</h2>
                <p class="fw-semibold mb-1"><?= e($frete['t_nome']) ?></p>
                <p class="small text-muted mb-0">
                    <?= e($frete['t_doc']) ?> · <?= e($frete['t_telefone']) ?><br>
                    <?= e($frete['t_veiculo']) ?> <?= $frete['t_placa'] ? '· Placa ' . e($frete['t_placa']) : '' ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-4 mb-3">
            <div><span class="text-muted small d-block">Data</span><strong><?= data_br($frete['data_frete']) ?></strong></div>
            <div><span class="text-muted small d-block">Valor do frete</span><strong><?= moeda($frete['valor_frete']) ?></strong></div>
            <div><span class="text-muted small d-block">Peso total</span><strong><?= number_format($pesoTotal, 2, ',', '.') ?> kg</strong></div>
            <div><span class="text-muted small d-block">Valor da carga</span><strong><?= moeda($valorCarga) ?></strong></div>
        </div>

        <h2 class="h5"><i class="bi bi-box-seam"></i> Produtos transportados (<?= count($itens) ?>)</h2>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Produto</th><th>Qtd.</th><th>Peso unit.</th><th>Valor unit.</th><th>Subtotal</th></tr></thead>
                <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= e($item['nome']) ?></td>
                        <td><?= (int) $item['quantidade'] ?></td>
                        <td><?= number_format($item['peso_kg'], 2, ',', '.') ?> kg</td>
                        <td><?= moeda($item['valor']) ?></td>
                        <td><?= moeda($item['valor'] * $item['quantidade']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($frete['observacao']): ?>
            <p class="mb-0"><strong>Observação:</strong> <?= e($frete['observacao']) ?></p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/rodape.php'; ?>
