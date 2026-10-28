<div style="display:flex;flex-direction:column;gap:24px;">
    <!-- Stat Cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:20px;">
        <div class="card">
            <div class="card-body" style="display:flex;align-items:center;gap:16px;">
                <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--color-primary-light);color:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:22px;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase;">Total Users</div>
                    <div style="font-size:24px;font-weight:800;color:var(--text-main);"><?= number_format($stats['total_users']) ?></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="display:flex;align-items:center;gap:16px;">
                <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--color-success-bg);color:var(--color-success);display:flex;align-items:center;justify-content:center;font-size:22px;">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div>
                    <div style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase;">Total Events</div>
                    <div style="font-size:24px;font-weight:800;color:var(--text-main);"><?= number_format($stats['total_events']) ?></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="display:flex;align-items:center;gap:16px;">
                <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--color-discord-light);color:var(--color-discord);display:flex;align-items:center;justify-content:center;font-size:22px;">
                    <i class="fa-brands fa-discord"></i>
                </div>
                <div>
                    <div style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase;">Webhooks</div>
                    <div style="font-size:24px;font-weight:800;color:var(--text-main);"><?= number_format($stats['total_webhooks']) ?></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="display:flex;align-items:center;gap:16px;">
                <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--color-warning-bg);color:var(--color-warning);display:flex;align-items:center;justify-content:center;font-size:22px;">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <div style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase;">Upcoming Events</div>
                    <div style="font-size:24px;font-weight:800;color:var(--text-main);"><?= number_format($stats['upcoming_events']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
        <!-- Recent System Activity -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-list-ul" style="margin-right:8px;color:var(--color-primary);"></i> Recent System Activity</span>
            </div>
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <tbody>
                        <?php if (empty($recentActivity)): ?>
                            <tr>
                                <td style="padding:32px;text-align:center;color:var(--text-muted);">No activity recorded yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentActivity as $act): ?>
                                <tr style="border-bottom:1px solid var(--border-color);font-size:13px;">
                                    <td style="padding:12px 16px;width:130px;font-weight:600;color:var(--text-main);">
                                        <?= e($act['username'] ?? 'System') ?>
                                    </td>
                                    <td style="padding:12px 16px;color:var(--text-muted);">
                                        <?= e($act['description']) ?>
                                    </td>
                                    <td style="padding:12px 16px;text-align:right;color:var(--text-subtle);font-size:11.5px;">
                                        <?= \App\Helpers\DateHelper::timeAgo($act['created_at']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Diagnostics Info -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-server" style="margin-right:8px;color:var(--color-info);"></i> Environment Diagnostics</span>
            </div>
            <div class="card-body" style="padding:16px;">
                <div style="display:flex;flex-direction:column;gap:12px;font-size:13px;">
                    <div style="display:flex;justify-content:space-between;border-bottom:1px solid var(--border-color);padding-bottom:8px;">
                        <span style="color:var(--text-muted);">PHP Engine:</span>
                        <span style="font-weight:700;"><?= e($systemInfo['php_version']) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;border-bottom:1px solid var(--border-color);padding-bottom:8px;">
                        <span style="color:var(--text-muted);">Database:</span>
                        <span style="font-weight:700;">MySQL (<?= e($systemInfo['db_version']) ?>)</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;border-bottom:1px solid var(--border-color);padding-bottom:8px;">
                        <span style="color:var(--text-muted);">Memory Allocated:</span>
                        <span style="font-weight:700;"><?= e($systemInfo['memory_usage']) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;border-bottom:1px solid var(--border-color);padding-bottom:8px;">
                        <span style="color:var(--text-muted);">Server Software:</span>
                        <span style="font-weight:600;font-size:12px;"><?= substr(e($systemInfo['server_software']), 0, 20) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--text-muted);">Host OS:</span>
                        <span style="font-weight:700;"><?= e($systemInfo['os']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
