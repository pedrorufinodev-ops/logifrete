<?php
require_once __DIR__ . '/../includes/funcoes.php';

// Valores padrão para um produto novo
$produto = ['id' => '', 'nome' => '', 'descricao' => '', 'peso_kg' => '', 'valor' => ''];

// Se veio um id na URL, é edição: carrega os dados do banco
if (!empty($_GET['id'])) {
    $sql = conexao()->prepare('SELECT * FROM produtos WHERE id = ?');
    $sql->execute([(int) $_GET['id']]);
    $produto = $sql->fetch();
    if (!$produto) {
        flash('Produto não encontrado.', 'danger');
        redirecionar('index.php');
    }
}

$editando = !empty($produto['id']);
$titulo = $editando ? 'Editar produto' : 'Novo produto';
$base = '../';
require __DIR__ . '/../includes/cabecalho.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Produtos</a></li>
        <li class="breadcrumb-item active"><?= $titulo ?></li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <h1 class="h4 titulo-pagina mb-4"><?= $titulo ?></h1>

        <form action="salvar.php" method="post">
            <input type="hidden" name="id" value="<?= e($produto['id']) ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome do produto *</label>
                    <input type="text" id="nome" name="nome" class="form-control" maxlength="120" required
                           value="<?= e($produto['nome']) ?>">
                </div>
                <div class="col-md-3">
                    <label for="peso_kg" class="form-label">Peso (kg) *</label>
                    <input type="number" id="peso_kg" name="peso_kg" class="form-control" step="0.01" min="0" required
                           value="<?= e($produto['peso_kg']) ?>">
                </div>
                <div class="col-md-3">
                    <label for="valor" class="form-label">Valor unitário (R$) *</label>
                    <input type="number" id="valor" name="valor" class="form-control" step="0.01" min="0" required
                           value="<?= e($produto['valor']) ?>">
                </div>
                <div class="col-12">
                    <label for="descricao" class="form-label">Descrição</label>
                    <input type="text" id="descricao" name="descricao" class="form-control" maxlength="255"
                           value="<?= e($produto['descricao']) ?>">
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
