<div style="margin-bottom:20px;">
    <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">Discord Webhook Delivery Logs</h1>
    <p style="font-size:13.5px;color:var(--text-muted);">Inspect recent outbound Discord webhook HTTP requests, status codes, and JSON payloads.</p>
</div>

<div class="card">
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color);background:var(--bg-subtle);font-size:12px;text-transform:uppercase;color:var(--text-subtle);">
                    <th style="padding:12px 16px;text-align:left;">Trigger</th>
                    <th style="padding:12px 16px;text-align:left;">Status</th>
                    <th style="padding:12px 16px;text-align:left;">HTTP Code</th>
                    <th style="padding:12px 16px;text-align:left;">User</th>
                    <th style="padding:12px 16px;text-align:left;">Payload Excerpt</th>
                    <th style="padding:12px 16px;text-align:right;">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" style="padding:32px;text-align:center;color:var(--text-muted);">No webhook activity logged yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $l): ?>
                        <tr style="border-bottom:1px solid var(--border-color);font-size:13px;">
                            <td style="padding:12px 16px;font-weight:600;">
                                <span class="badge badge-info"><?= e($l['trigger_type']) ?></span>
                            </td>
                            <td style="padding:12px 16px;">
                                <span class="badge badge-<?= $l['status'] === 'success' ? 'success' : 'danger' ?>">
                                    <?= ucfirst(e($l['status'])) ?>
                                </span>
                            </td>
                            <td style="padding:12px 16px;font-family:monospace;">
                                <?= e($l['response_code'] ?? 'N/A') ?>
                            </td>
                            <td style="padding:12px 16px;color:var(--text-muted);">
                                <?= e($l['username'] ?? 'System/Cron') ?>
                            </td>
                            <td style="padding:12px 16px;font-family:monospace;font-size:11.5px;color:var(--text-muted);max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?= e($l['payload']) ?>
                            </td>
                            <td style="padding:12px 16px;text-align:right;color:var(--text-subtle);font-size:12px;">
                                <?= \App\Helpers\DateHelper::timeAgo($l['created_at']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
