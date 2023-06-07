<div class="admin-container">
    <div class="nav-bar">
        <a class="nav-link <?= $data['section'] == 'exercises' ? 'nav-selected' : '' ?>" href="/admin/exercises">Упражнения</a>
        <a class="nav-link <?= $data['section'] == 'colors' ? 'nav-selected' : '' ?>" href="/admin/colors">Цветове</a>
    </div>
    <?php require_once 'Application/Views/admin/' . $data['section'] . '.php'; ?>
    <script src="/Application/Views/admin/<?= $data['section'] ?>.js"></script>
</div>