<?= \Core\View::partial('head', ['title' => $title ?? 'Dischedule Admin', 'currentLang' => $currentLang, 'currentUser' => $currentUser, 'csrfToken' => $csrfToken]) ?>
<body>
    <div class="app-layout">
        <?= \Core\View::partial('sidebar', ['currentUser' => $currentUser]) ?>

        <div class="app-main">
            <header class="app-navbar" style="background:var(--bg-surface);border-bottom:2px solid var(--color-danger);">
                <div style="display:flex;align-items:center;gap:12px;">
                    <span class="badge badge-danger" style="font-size:12px;padding:4px 10px;">
                        <i class="fa-solid fa-shield-halved"></i> Administrator Room
                    </span>
                    <span style="font-weight:700;font-size:15px;color:var(--text-main);"><?= e($title ?? 'Admin') ?></span>
                </div>

                <div class="navbar-actions">
                    <a href="<?= url('calendar') ?>" class="btn btn-secondary" style="font-size:12px;">
                        <i class="fa-solid fa-arrow-left"></i> Exit to App
                    </a>
                    <button class="btn btn-icon theme-toggle-btn" onclick="Dischedule.toggleTheme()" title="Toggle Dark/Light Mode" aria-label="Toggle theme">
                        <i class="fa-solid fa-moon theme-icon-moon"></i>
                        <i class="fa-solid fa-sun theme-icon-sun" style="display:none;"></i>
                    </button>
                </div>
            </header>
            
            <main class="app-content">
                <?= $content ?>
            </main>

            <?= \Core\View::partial('footer') ?>
        </div>
    </div>

    <?= \Core\View::partial('toasts', ['flashes' => $flashes]) ?>
</body>
</html>
