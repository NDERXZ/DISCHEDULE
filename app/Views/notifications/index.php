<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;"><?= __('notifications') ?></h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Review recent alerts, event reminders, and team activity logs.</p>
    </div>
    <div style="display:flex;gap:10px;">
        <form action="<?= url('notifications/mark-all-read') ?>" method="POST" style="margin:0;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary">
                <i class="fa-solid fa-check-double"></i> Mark All as Read
            </button>
        </form>
        <form action="<?= url('notifications/clear-all') ?>" method="POST" style="margin:0;" onsubmit="return confirm('Clear all notifications?');">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary" style="color:var(--color-danger);">
                <i class="fa-regular fa-trash-can"></i> Clear All
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($notifications)): ?>
            <div style="padding:48px;text-align:center;color:var(--text-muted);">
                <i class="fa-regular fa-bell-slash" style="font-size:36px;opacity:0.4;margin-bottom:12px;display:block;"></i>
                No notifications to display.
            </div>
        <?php else: ?>
            <div style="display:flex;flex-direction:column;">
                <?php foreach ($notifications as $n): ?>
                    <div style="padding:16px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:flex-start;gap:16px;background:<?= empty($n['is_read']) ? 'var(--bg-subtle)' : 'transparent' ?>;transition:background 0.2s;">
                        <div style="width:36px;height:36px;border-radius:50%;background:<?= $n['type'] === 'success' ? 'var(--color-success-bg)' : ($n['type'] === 'warning' ? 'var(--color-warning-bg)' : ($n['type'] === 'danger' ? 'var(--color-danger-bg)' : 'var(--color-info-bg)')) ?>;color:<?= $n['type'] === 'success' ? 'var(--color-success)' : ($n['type'] === 'warning' ? 'var(--color-warning)' : ($n['type'] === 'danger' ? 'var(--color-danger)' : 'var(--color-info)')) ?>;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
                            <i class="fa-solid <?= $n['type'] === 'success' ? 'fa-circle-check' : ($n['type'] === 'warning' ? 'fa-triangle-exclamation' : ($n['type'] === 'danger' ? 'fa-circle-xmark' : 'fa-bell')) ?>"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                <div style="font-weight:700;font-size:14px;color:var(--text-main);"><?= e($n['title']) ?></div>
                                <span style="font-size:11.5px;color:var(--text-subtle);"><?= \App\Helpers\DateHelper::timeAgo($n['created_at']) ?></span>
                            </div>
                            <div style="font-size:13.5px;color:var(--text-muted);margin-bottom:8px;line-height:1.5;"><?= e($n['message']) ?></div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <?php if (!empty($n['link'])): ?>
                                    <a href="<?= url(ltrim($n['link'], '/')) ?>" class="btn btn-secondary" style="padding:3px 10px;font-size:12px;">
                                        View Details →
                                    </a>
                                <?php endif; ?>
                                <?php if (empty($n['is_read'])): ?>
                                    <form action="<?= url('notifications/' . $n['id'] . '/read') ?>" method="POST" style="margin:0;">
                                        <?= csrf_field() ?>
                                        <button type="submit" style="background:none;border:none;color:var(--color-primary);font-size:12px;font-weight:600;cursor:pointer;">
                                            Mark as read
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
