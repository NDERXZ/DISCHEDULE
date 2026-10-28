<div style="max-width:500px;margin:60px auto;text-align:center;">
    <div style="width:70px;height:70px;border-radius:50%;background:var(--color-primary-light);color:var(--color-primary);display:inline-flex;align-items:center;justify-content:center;font-size:32px;margin-bottom:20px;">
        <i class="fa-solid fa-magnifying-glass-location"></i>
    </div>
    <h1 style="font-size:28px;font-weight:800;color:var(--text-main);margin-bottom:8px;">404 - Page Not Found</h1>
    <p style="color:var(--text-muted);font-size:14px;margin-bottom:24px;"><?= e($message ?? 'The page, event, or resource you were looking for could not be found.') ?></p>
    <a href="<?= url('calendar') ?>" class="btn btn-primary">
        <i class="fa-solid fa-arrow-left"></i> Return to Calendar
    </a>
</div>
