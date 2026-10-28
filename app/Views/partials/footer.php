<footer style="padding:20px 24px;border-top:1px solid var(--border-color);background:var(--bg-surface);display:flex;align-items:center;justify-content:space-between;font-size:12.5px;color:var(--text-muted);flex-wrap:wrap;gap:12px;">
    <div>
        &copy; <?= date('Y') ?> <strong>Dischedule</strong>. High-performance Discord schedule integration.
    </div>
    <div style="display:flex;align-items:center;gap:16px;">
        <a href="<?= url('events/import-export') ?>" style="color:var(--text-muted);"><i class="fa-solid fa-file-import"></i> Import/Export</a>
        <a href="<?= url('api/v1/events') ?>" target="_blank" style="color:var(--text-muted);"><i class="fa-solid fa-code"></i> REST API</a>
        <span style="color:var(--text-subtle);">v2.0.0</span>
    </div>
</footer>
