<?= \Core\View::partial('head', ['title' => $title ?? 'Dischedule', 'currentLang' => $currentLang, 'currentUser' => $currentUser, 'csrfToken' => $csrfToken]) ?>
<body>
    <div class="app-layout">
        <?php if (!empty($currentUser)): ?>
            <?= \Core\View::partial('sidebar', ['currentUser' => $currentUser]) ?>
        <?php endif; ?>

        <div class="app-main" style="<?= empty($currentUser) ? 'margin-left:0;' : '' ?>">
            <?= \Core\View::partial('navbar', ['currentUser' => $currentUser]) ?>
            
            <main class="app-content">
                <?= $content ?>
            </main>

            <?= \Core\View::partial('footer') ?>
        </div>
    </div>

    <?= \Core\View::partial('toasts', ['flashes' => $flashes]) ?>

    <?php if (!empty($currentUser)): ?>
        <?= \Core\View::partial('event_modal') ?>
    <?php endif; ?>

    <!-- Master Scripts -->
    <script src="<?= asset('js/notifications.js') ?>"></script>
</body>
</html>
