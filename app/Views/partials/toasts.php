<div id="toast-container" class="toast-container">
    <?php if (!empty($flashes)): ?>
        <?php foreach ($flashes as $f): ?>
            <div class="toast toast-<?= e($f['type']) ?> show">
                <i class="fa-solid <?= $f['type'] === 'success' ? 'fa-circle-check' : ($f['type'] === 'error' ? 'fa-circle-xmark' : ($f['type'] === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-info')) ?>" style="margin-top:2px;font-size:16px;"></i>
                <div style="flex:1;">
                    <div style="font-weight:600;text-transform:capitalize;margin-bottom:2px;"><?= e($f['type']) ?></div>
                    <div style="font-size:13px;color:var(--text-muted);"><?= e($f['message']) ?></div>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-subtle);cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
