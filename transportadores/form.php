<?php
require_once __DIR__ . '/../includes/funcoes.php';

$transportador = [
    'id' => '', 'nome' => '', 'cpf_cnpj' => '', 'telefone' => '', 'email' => '', 'veiculo' => '', 'placa' => '',
];

if (!empty($_GET['id'])) {
    $sql = conexao()->prepare('SELECT * FROM transportadores WHERE id = ?');
    $sql->execute([(int) $_GET['id']]);
    $transportador = $sql->fetch();
    if (!$transportador) {
        flash('Transportador não encontrado.', 'danger');
        redirecionar('index.php');
    }
}

$editando = !empty($transportador['id']);
$titulo = $editando ? 'Editar transportador' : 'Novo transportador';
$base = '../';
require __DIR__ . '/../includes/cabecalho.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Transportadores</a></li>
        <li class="breadcrumb-item active"><?= $titulo ?></li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <h1 class="h4 titulo-pagina mb-4"><?= $titulo ?></h1>

        <form action="salvar.php" method="post">
            <input type="hidden" name="id" value="<?= e($transportador['id']) ?>">

            <h2 class="h6 text-primary text-uppercase mb-3">Dados do transportador</h2>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome / Razão social *</label>
                    <input type="text" id="nome" name="nome" class="form-control" maxlength="120" required
                           value="<?= e($transportador['nome']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="cpf_cnpj" class="form-label">CPF ou CNPJ *</label>
                    <input type="text" id="cpf_cnpj" name="cpf_cnpj" class="form-control" data-mascara="cpfcnpj"
                           maxlength="18" required value="<?= e($transportador['cpf_cnpj']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" id="telefone" name="telefone" class="form-control" data-mascara="telefone"
                           maxlength="15" placeholder="(00) 00000-0000" value="<?= e($transportador['telefone']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" maxlength="120"
                           value="<?= e($transportador['email']) ?>">
                </div>
            </div>

            <h2 class="h6 text-primary text-uppercase mb-3">Veículo</h2>
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="veiculo" class="form-label">Modelo do veículo</label>
                    <input type="text" id="veiculo" name="veiculo" class="form-control" maxlength="60"
                           placeholder="Ex.: Caminhão VW Delivery" value="<?= e($transportador['veiculo']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="placa" class="form-label">Placa</label>
                    <input type="text" id="placa" name="placa" class="form-control text-uppercase" maxlength="8"
                           placeholder="ABC1D23" value="<?= e($transportador['placa']) ?>">
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
