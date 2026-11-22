<?php
/**
 * Cabeçalho comum a todas as páginas.
 * Antes de incluir, defina $titulo e $base ('' na raiz, '../' nas subpastas).
 */
$titulo = $titulo ?? NOME_SISTEMA;
$base = $base ?? '';
$pagina = basename(dirname($_SERVER['SCRIPT_NAME']));
if (basename($_SERVER['SCRIPT_NAME']) === 'relatorio.php') {
    $pagina = 'relatorio';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | <?= NOME_SISTEMA ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= $base ?>assets/css/estilo.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $base ?>index.php">
            <i class="bi bi-truck"></i> <?= NOME_SISTEMA ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
                aria-controls="menu" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $pagina === 'produtos' ? 'active' : '' ?>" href="<?= $base ?>produtos/index.php">
                        <i class="bi bi-box-seam"></i> Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $pagina === 'enderecos' ? 'active' : '' ?>" href="<?= $base ?>enderecos/index.php">
                        <i class="bi bi-geo-alt"></i> Endereços</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $pagina === 'transportadores' ? 'active' : '' ?>" href="<?= $base ?>transportadores/index.php">
                        <i class="bi bi-person-badge"></i> Transportadores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $pagina === 'fretes' ? 'active' : '' ?>" href="<?= $base ?>fretes/index.php">
                        <i class="bi bi-truck"></i> Fretes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $pagina === 'relatorio' ? 'active' : '' ?>" href="<?= $base ?>fretes/relatorio.php">
                        <i class="bi bi-file-earmark-arrow-down"></i> Relatório</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= e($_SESSION['flash']['tipo']) ?> alert-dismissible fade show" role="alert">
        <?= e($_SESSION['flash']['mensagem']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
