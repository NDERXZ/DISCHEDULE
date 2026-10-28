<?= \Core\View::partial('head', ['title' => $title ?? 'Dischedule Auth', 'currentLang' => $currentLang, 'csrfToken' => $csrfToken]) ?>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background-color:var(--bg-body);padding:20px;">
    <div style="width:100%;max-width:440px;">
        <div style="text-align:center;margin-bottom:28px;">
            <a href="<?= url() ?>" style="display:inline-flex;align-items:center;gap:12px;font-size:24px;font-weight:800;color:var(--text-main);letter-spacing:-0.5px;">
                <img src="https://i.imgur.com/Yo8WHdg.png" alt="Dischedule Logo" style="height:40px;width:auto;object-fit:contain;border-radius:8px;">
                <span>Dischedule</span>
            </a>
        </div>

        <div class="card" style="box-shadow:var(--shadow-xl);border-radius:var(--radius-lg);">
            <div class="card-body" style="padding:32px;">
                <?= $content ?>
            </div>
        </div>

        <div style="text-align:center;margin-top:20px;font-size:12px;color:var(--text-muted);">
            &copy; <?= date('Y') ?> Dischedule. High-performance Discord schedule integration.
        </div>
    </div>

    <?= \Core\View::partial('toasts', ['flashes' => $flashes]) ?>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
