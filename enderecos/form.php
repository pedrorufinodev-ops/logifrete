<?php
require_once __DIR__ . '/../includes/funcoes.php';

$endereco = [
    'id' => '', 'apelido' => '', 'cep' => '', 'logradouro' => '', 'numero' => '',
    'complemento' => '', 'bairro' => '', 'cidade' => '', 'uf' => '',
];

if (!empty($_GET['id'])) {
    $sql = conexao()->prepare('SELECT * FROM enderecos WHERE id = ?');
    $sql->execute([(int) $_GET['id']]);
    $endereco = $sql->fetch();
    if (!$endereco) {
        flash('Endereço não encontrado.', 'danger');
        redirecionar('index.php');
    }
}

$ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR',
        'PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

$editando = !empty($endereco['id']);
$titulo = $editando ? 'Editar endereço' : 'Novo endereço';
$base = '../';
require __DIR__ . '/../includes/cabecalho.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Endereços</a></li>
        <li class="breadcrumb-item active"><?= $titulo ?></li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <h1 class="h4 titulo-pagina mb-1"><?= $titulo ?></h1>
        <p class="text-muted small mb-4">Este endereço poderá ser usado como origem ou destino de um frete.</p>

        <form action="salvar.php" method="post">
            <input type="hidden" name="id" value="<?= e($endereco['id']) ?>">

            <div class="row g-3">
                <div class="col-md-5">
                    <label for="apelido" class="form-label">Apelido (identificação) *</label>
                    <input type="text" id="apelido" name="apelido" class="form-control" maxlength="80" required
                           placeholder="Ex.: Centro de Distribuição" value="<?= e($endereco['apelido']) ?>">
                </div>
                <div class="col-md-3">
                    <label for="cep" class="form-label">CEP *</label>
                    <input type="text" id="cep" name="cep" class="form-control" data-mascara="cep" maxlength="9" required
                           placeholder="00000-000" value="<?= e($endereco['cep']) ?>">
                    <div id="aviso-cep" class="form-text">Digite o CEP para preencher automaticamente.</div>
                </div>
                <div class="col-md-8">
                    <label for="logradouro" class="form-label">Logradouro *</label>
                    <input type="text" id="logradouro" name="logradouro" class="form-control" maxlength="150" required
                           value="<?= e($endereco['logradouro']) ?>">
                </div>
                <div class="col-md-2">
                    <label for="numero" class="form-label">Número *</label>
                    <input type="text" id="numero" name="numero" class="form-control" maxlength="10" required
                           value="<?= e($endereco['numero']) ?>">
                </div>
                <div class="col-md-2">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" id="complemento" name="complemento" class="form-control" maxlength="80"
                           value="<?= e($endereco['complemento']) ?>">
                </div>
                <div class="col-md-5">
                    <label for="bairro" class="form-label">Bairro *</label>
                    <input type="text" id="bairro" name="bairro" class="form-control" maxlength="80" required
                           value="<?= e($endereco['bairro']) ?>">
                </div>
                <div class="col-md-5">
                    <label for="cidade" class="form-label">Cidade *</label>
                    <input type="text" id="cidade" name="cidade" class="form-control" maxlength="80" required
                           value="<?= e($endereco['cidade']) ?>">
                </div>
                <div class="col-md-2">
                    <label for="uf" class="form-label">UF *</label>
                    <select id="uf" name="uf" class="form-select" required>
                        <option value="">--</option>
                        <?php foreach ($ufs as $uf): ?>
                            <option <?= $endereco['uf'] === $uf ? 'selected' : '' ?>><?= $uf ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar</button>
                <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/rodape.php'; ?>
