<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">All Events Moderation</h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Overview of all scheduled public and private events across the application.</p>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color);background:var(--bg-subtle);font-size:12px;text-transform:uppercase;color:var(--text-subtle);">
                    <th style="padding:12px 16px;text-align:left;">Event</th>
                    <th style="padding:12px 16px;text-align:left;">Start Date</th>
                    <th style="padding:12px 16px;text-align:left;">Visibility</th>
                    <th style="padding:12px 16px;text-align:left;">Status</th>
                    <th style="padding:12px 16px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginator['items'] as $ev): ?>
                    <tr style="border-bottom:1px solid var(--border-color);">
                        <td style="padding:12px 16px;">
                            <a href="<?= url('events/' . $ev['id']) ?>" style="font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:8px;">
                                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:<?= e($ev['color'] ?? '#4F46E5') ?>;"></span>
                                <?= e($ev['title']) ?>
                            </a>
                            <div style="font-size:11.5px;color:var(--text-muted);">Author ID: <?= $ev['user_id'] ?></div>
                        </td>
                        <td style="padding:12px 16px;font-size:13px;color:var(--text-muted);">
                            <?= $ev['start_datetime'] ?>
                        </td>
                        <td style="padding:12px 16px;">
                            <span class="badge badge-info"><?= ucfirst(e($ev['visibility'])) ?></span>
                        </td>
                        <td style="padding:12px 16px;">
                            <span class="badge badge-<?= $ev['status'] === 'published' ? 'success' : 'warning' ?>"><?= ucfirst(e($ev['status'])) ?></span>
                        </td>
                        <td style="padding:12px 16px;text-align:right;">
                            <form action="<?= url('events/' . $ev['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Force delete this event?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-icon btn-secondary" style="color:var(--color-danger);" title="Delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
