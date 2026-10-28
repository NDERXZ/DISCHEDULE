<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;"><?= __('webhooks') ?></h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Configure Discord channels to receive live event broadcasts, start alerts, and reminders.</p>
    </div>
    <button onclick="Dischedule.openModal('modal-add-webhook')" class="btn btn-discord">
        <i class="fa-brands fa-discord"></i> Connect Webhook
    </button>
</div>

<!-- Info Box -->
<div class="card" style="margin-bottom:24px;background:var(--color-discord-light);border:1px solid rgba(88, 101, 242, 0.2);">
    <div class="card-body" style="padding:16px 20px;display:flex;align-items:flex-start;gap:14px;">
        <i class="fa-brands fa-discord" style="color:var(--color-discord);font-size:24px;margin-top:2px;"></i>
        <div style="font-size:13px;line-height:1.5;color:var(--text-main);">
            <strong>How Discord Webhooks Work in Dischedule:</strong> Go to your Discord server &rarr; <em>Server Settings</em> &rarr; <em>Integrations</em> &rarr; <em>Webhooks</em> &rarr; <em>New Webhook</em>. Copy the URL and paste it below. You can assign default webhooks, mention specific roles (`@everyone`, `@here`, `<@&ROLE_ID>`), and trigger automated reminders.
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color);background:var(--bg-subtle);font-size:12px;text-transform:uppercase;color:var(--text-subtle);">
                    <th style="padding:12px 16px;text-align:left;">Webhook Name</th>
                    <th style="padding:12px 16px;text-align:left;">Target URL</th>
                    <th style="padding:12px 16px;text-align:left;">Role Mention</th>
                    <th style="padding:12px 16px;text-align:left;">Status</th>
                    <th style="padding:12px 16px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($webhooks)): ?>
                    <tr>
                        <td colspan="5" style="padding:48px;text-align:center;color:var(--text-muted);">
                            <i class="fa-brands fa-discord" style="font-size:36px;color:var(--color-discord);opacity:0.4;margin-bottom:10px;display:block;"></i>
                            No Discord webhooks configured yet. Click "Connect Webhook" above to get started.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($webhooks as $w): ?>
                        <tr style="border-bottom:1px solid var(--border-color);transition:background 0.15s;" onmouseover="this.style.background='var(--bg-subtle)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 16px;font-weight:700;color:var(--text-main);">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <i class="fa-brands fa-discord" style="color:var(--color-discord);"></i>
                                    <?= e($w['name']) ?>
                                </div>
                            </td>
                            <td style="padding:14px 16px;font-family:monospace;font-size:12px;color:var(--text-muted);">
                                <?= substr(e($w['url']), 0, 45) ?>...
                            </td>
                            <td style="padding:14px 16px;">
                                <?php if (!empty($w['mention_role'])): ?>
                                    <span class="badge badge-info"><?= e($w['mention_role']) ?></span>
                                <?php else: ?>
                                    <span style="color:var(--text-subtle);font-size:12px;">None</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:14px 16px;">
                                <?php if (!empty($w['is_default'])): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Default</span>
                                <?php else: ?>
                                    <form action="<?= url('webhooks/' . $w['id'] . '/set-default') ?>" method="POST" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary" style="font-size:11.5px;padding:3px 8px;">Set Default</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td style="padding:14px 16px;text-align:right;">
                                <div style="display:inline-flex;gap:6px;">
                                    <button onclick="testWebhookDelivery(<?= $w['id'] ?>)" class="btn btn-secondary" style="font-size:12px;padding:5px 10px;" id="btn-test-<?= $w['id'] ?>">
                                        <i class="fa-solid fa-paper-plane" style="color:var(--color-discord);"></i> Test Webhook
                                    </button>
                                    <form action="<?= url('webhooks/' . $w['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this webhook?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-icon btn-secondary" style="color:var(--color-danger);" title="Delete">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Webhook Modal -->
<div class="modal-backdrop" id="modal-add-webhook">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 style="font-size:16px;font-weight:700;"><i class="fa-brands fa-discord" style="color:var(--color-discord);"></i> Connect Discord Webhook</h3>
            <button onclick="Dischedule.closeModal('modal-add-webhook')" class="btn btn-icon"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= url('webhooks') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Friendly Channel Name <span style="color:var(--color-danger);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., #announcements, #dev-team" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Discord Webhook URL <span style="color:var(--color-danger);">*</span></label>
                    <input type="url" name="url" class="form-control" placeholder="https://discord.com/api/webhooks/..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Role Mention Tag (Optional)</label>
                    <input type="text" name="mention_role" class="form-control" placeholder="@everyone, @here, or <@&role_id>">
                </div>

                <div style="display:flex;align-items:center;gap:8px;margin-top:12px;">
                    <input type="checkbox" name="is_default" id="is_default_wh" value="1" <?= empty($webhooks) ? 'checked' : '' ?>>
                    <label for="is_default_wh" style="font-size:13px;font-weight:600;cursor:pointer;">Set as default webhook for newly scheduled events</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="Dischedule.closeModal('modal-add-webhook')" class="btn btn-secondary"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-discord"><i class="fa-solid fa-plug"></i> Connect Webhook</button>
            </div>
        </form>
    </div>
</div>

<script>
async function testWebhookDelivery(webhookId) {
    const btn = document.getElementById(`btn-test-${webhookId}`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
    }

    const res = await Dischedule.fetch(`/webhooks/${webhookId}/test`, { method: 'POST' });

    if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane" style="color:var(--color-discord);"></i> Test Webhook';
    }

    if (res.ok && res.data.success) {
        Dischedule.toast('success', res.data.message || 'Discord test message delivered successfully!');
    } else {
        Dischedule.toast('error', res.data.message || 'Webhook failed. Check URL permissions.');
    }
}
</script>
