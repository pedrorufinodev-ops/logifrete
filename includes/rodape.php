</main>

<footer class="text-center text-muted small py-4 border-top bg-white">
    <?= NOME_SISTEMA ?> &copy; <?= date('Y') ?> — Projeto da disciplina Fundamentos de Programação para Internet (UNIPAR EAD)
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base ?? '' ?>assets/js/app.js"></script>
<?php foreach ($scripts ?? [] as $script): ?>
<script src="<?= $base ?? '' ?><?= e($script) ?>"></script>
<?php endforeach; ?>
</body>
</html>
