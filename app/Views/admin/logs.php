<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">Application Logs</h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Real-time log stream from storage/logs/app-<?= date('Y-m-d') ?>.log</p>
    </div>
    <form action="<?= url('admin/logs/clear') ?>" method="POST" style="margin:0;" onsubmit="return confirm('Clear today log file?');">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-secondary" style="color:var(--color-danger);">
            <i class="fa-regular fa-trash-can"></i> Clear Today's Log
        </button>
    </form>
</div>

<div class="card" style="background:#0F172A;color:#E2E8F0;font-family:monospace;font-size:12px;border:none;">
    <div class="card-body" style="padding:20px;max-height:600px;overflow-y:auto;">
        <pre style="margin:0;white-space:pre-wrap;line-height:1.6;color:#94A3B8;"><?= e($logContent) ?></pre>
    </div>
</div>
