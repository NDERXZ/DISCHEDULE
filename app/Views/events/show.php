<div style="max-width:950px;margin:0 auto;">
    <!-- Top Action Bar -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <a href="<?= url('events') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Events
        </a>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button onclick="Dischedule.openModal('modal-share-event')" class="btn btn-secondary">
                <i class="fa-solid fa-share-nodes"></i> <?= __('share_event') ?>
            </button>
            
            <a href="<?= url('events/export/ics?event_id=' . $event['id']) ?>" class="btn btn-secondary">
                <i class="fa-solid fa-file-export"></i> Add to iCal/Outlook
            </a>

            <form action="<?= url('events/' . $event['id'] . '/duplicate') ?>" method="POST" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-regular fa-copy"></i> <?= __('duplicate') ?>
                </button>
            </form>

            <a href="<?= url('events/' . $event['id'] . '/edit') ?>" class="btn btn-primary">
                <i class="fa-solid fa-pen"></i> <?= __('edit') ?>
            </a>

            <form action="<?= url('events/' . $event['id'] . '/delete') ?>" method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Event Details Grid -->
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:flex-start;">
        <div style="display:flex;flex-direction:column;gap:24px;">
            <!-- Header Card -->
            <div class="card" style="border-top:5px solid <?= e($event['color'] ?? '#4F46E5') ?>;">
                <div class="card-body" style="padding:28px;">
                    <div style="display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
                        <?php if (!empty($event['category_name'])): ?>
                            <span class="badge" style="background-color:<?= e($event['category_color'] ?? '#4F46E5') ?>15;color:<?= e($event['category_color'] ?? '#4F46E5') ?>;border:1px solid <?= e($event['category_color'] ?? '#4F46E5') ?>40;">
                                <i class="<?= e($event['category_icon'] ?? 'fa-solid fa-calendar') ?>" style="margin-right:4px;"></i>
                                <?= e($event['category_name']) ?>
                            </span>
                        <?php endif; ?>

                        <span class="badge badge-<?= $event['priority'] === 'high' ? 'danger' : ($event['priority'] === 'medium' ? 'warning' : 'info') ?>">
                            Priority: <?= ucfirst(e($event['priority'])) ?>
                        </span>

                        <span class="badge badge-<?= $event['status'] === 'published' ? 'success' : 'warning' ?>">
                            <?= ucfirst(e($event['status'])) ?>
                        </span>

                        <?php if (!empty($event['recurrence_rule'])): ?>
                            <span class="badge badge-primary">
                                <i class="fa-solid fa-repeat"></i> Recurring: <?= e($event['recurrence_rule']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 style="font-size:26px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:16px;">
                        <?= e($event['title']) ?>
                    </h1>

                    <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:24px;font-size:14px;color:var(--text-main);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i class="fa-regular fa-clock" style="color:var(--color-primary);width:18px;"></i>
                            <span>
                                <strong>Start:</strong> <?= \App\Helpers\DateHelper::humanReadable($event['start_datetime'], $currentUser['timezone'] ?? 'UTC') ?>
                            </span>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i class="fa-regular fa-clock" style="color:var(--color-danger);width:18px;"></i>
                            <span>
                                <strong>End:</strong> <?= \App\Helpers\DateHelper::humanReadable($event['end_datetime'], $currentUser['timezone'] ?? 'UTC') ?>
                            </span>
                        </div>
                        <?php if (!empty($event['location'])): ?>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <i class="fa-solid fa-location-dot" style="color:var(--color-warning);width:18px;"></i>
                                <span>
                                    <?= e($event['location']) ?>
                                    <?php if (!empty($event['location_url'])): ?>
                                        &bull; <a href="<?= e($event['location_url']) ?>" target="_blank" rel="noopener">Join Link <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;"></i></a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($event['description'])): ?>
                        <div style="border-top:1px solid var(--border-color);padding-top:18px;">
                            <h3 style="font-size:14px;font-weight:700;margin-bottom:8px;">Description & Notes</h3>
                            <div style="color:var(--text-muted);font-size:14px;line-height:1.6;white-space:pre-wrap;"><?= e($event['description']) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($event['tags'])): ?>
                        <div style="display:flex;gap:6px;margin-top:20px;flex-wrap:wrap;">
                            <?php foreach ($event['tags'] as $t): ?>
                                <span class="badge" style="background:var(--bg-subtle);color:var(--text-muted);border:1px solid var(--border-color);">
                                    #<?= e($t['name']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Comments & Discussion -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-regular fa-comments" style="margin-right:8px;color:var(--color-primary);"></i> Comments & Discussion (<?= count($event['comments'] ?? []) ?>)</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($event['comments'])): ?>
                        <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:24px;">
                            <?php foreach ($event['comments'] as $com): ?>
                                <div style="display:flex;gap:12px;">
                                    <div style="width:34px;height:34px;border-radius:50%;background:var(--color-primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
                                        <?= strtoupper(substr($com['username'], 0, 1)) ?>
                                    </div>
                                    <div style="flex:1;background:var(--bg-subtle);padding:12px 16px;border-radius:var(--radius-sm);">
                                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                            <span style="font-weight:700;font-size:13px;"><?= e($com['username']) ?></span>
                                            <span style="font-size:11px;color:var(--text-subtle);"><?= \App\Helpers\DateHelper::timeAgo($com['created_at']) ?></span>
                                        </div>
                                        <div style="font-size:13.5px;color:var(--text-main);line-height:1.4;white-space:pre-wrap;"><?= e($com['content']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color:var(--text-muted);font-size:13px;margin-bottom:16px;">No comments posted yet.</p>
                    <?php endif; ?>

                    <form action="<?= url('events/' . $event['id'] . '/comments') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="form-group" style="margin-bottom:10px;">
                            <textarea name="content" rows="2" class="form-control" placeholder="Write a comment or note..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary" style="padding:6px 14px;font-size:12.5px;">
                            Post Comment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Discord Live Preview & Attachments -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            <!-- Discord Embed Simulator Card -->
            <div class="card" style="background:#2B2D31;color:#DBDEE1;border:none;border-radius:8px;">
                <div class="card-header" style="background:#1E1F22;border-bottom:1px solid #35363C;padding:12px 16px;">
                    <span style="font-size:12px;font-weight:700;color:#F2F3F5;display:flex;align-items:center;gap:6px;">
                        <i class="fa-brands fa-discord" style="color:var(--color-discord);"></i> Discord Embed Preview
                    </span>
                </div>
                <div class="card-body" style="padding:16px;">
                    <div style="background:#2B2D31;border-left:4px solid <?= e($event['color'] ?? '#5865F2') ?>;padding:12px 16px;border-radius:4px;">
                        <div style="font-size:11px;font-weight:600;color:#949BA4;margin-bottom:4px;">Dischedule Bot</div>
                        <div style="font-size:14px;font-weight:700;color:#FFF;margin-bottom:6px;"><?= e($event['title']) ?></div>
                        <div style="font-size:12px;color:#DBDEE1;margin-bottom:12px;line-height:1.4;">
                            <?= e(\App\Helpers\StringHelper::truncate($event['description'] ?? 'Event schedule details.', 150)) ?>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr;gap:8px;font-size:11.5px;">
                            <div>
                                <div style="font-weight:700;color:#B5BAC1;">📅 Schedule Time</div>
                                <div style="color:#FFF;"><?= \App\Helpers\DateHelper::humanReadable($event['start_datetime'], $currentUser['timezone'] ?? 'UTC') ?></div>
                            </div>
                            <?php if (!empty($event['location'])): ?>
                                <div>
                                    <div style="font-weight:700;color:#B5BAC1;">📍 Location</div>
                                    <div style="color:#FFF;"><?= e($event['location']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="font-size:10px;color:#949BA4;margin-top:12px;border-top:1px solid #35363C;padding-top:6px;">
                            Dischedule &bull; <?= date('Y-m-d H:i') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachments Card -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title" style="font-size:13.5px;"><i class="fa-solid fa-paperclip"></i> Attachments</span>
                </div>
                <div class="card-body" style="padding:16px;">
                    <?php if (!empty($event['attachments'])): ?>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <?php foreach ($event['attachments'] as $att): ?>
                                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--bg-subtle);border-radius:var(--radius-sm);font-size:12.5px;">
                                    <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;">
                                        <i class="fa-regular fa-file" style="margin-right:6px;color:var(--color-primary);"></i>
                                        <?= e($att['original_name']) ?>
                                    </div>
                                    <a href="<?= e($att['path']) ?>" download="<?= e($att['original_name']) ?>" class="btn btn-icon btn-secondary" style="width:28px;height:28px;" title="Download">
                                        <i class="fa-solid fa-download" style="font-size:11px;"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="color:var(--text-muted);font-size:12.5px;">No files attached to this event.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Public Sharing Modal -->
<div class="modal-backdrop" id="modal-share-event">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 style="font-size:16px;font-weight:700;"><i class="fa-solid fa-share-nodes"></i> Share Event Publicly</h3>
            <button onclick="Dischedule.closeModal('modal-share-event')" class="btn btn-icon"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:16px;">
                Generate a clean public link anyone can access to view this event schedule and add it to their personal calendar.
            </p>

            <div class="form-group">
                <label class="form-label">Optional Access Password</label>
                <input type="password" id="share-password" class="form-control" placeholder="Leave empty for public access">
            </div>

            <div class="form-group">
                <label class="form-label">Link Expiration Date (Optional)</label>
                <input type="datetime-local" id="share-expires" class="form-control">
            </div>

            <div id="share-result-box" style="display:none;margin-top:16px;background:var(--bg-subtle);padding:14px;border-radius:var(--radius-sm);border:1px solid var(--border-color);">
                <label class="form-label" style="font-size:12px;">Public Share URL:</label>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="share-url-input" class="form-control" readonly style="font-size:12.5px;">
                    <button onclick="Dischedule.copyToClipboard(document.getElementById('share-url-input').value)" class="btn btn-secondary">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="Dischedule.closeModal('modal-share-event')" class="btn btn-secondary"><?= __('close') ?></button>
            <button onclick="generateShareUrl()" class="btn btn-primary">
                <i class="fa-solid fa-link"></i> Generate Share Link
            </button>
        </div>
    </div>
</div>

<script>
async function generateShareUrl() {
    const password = document.getElementById('share-password').value;
    const expiresAt = document.getElementById('share-expires').value;

    const res = await Dischedule.fetch('<?= url('events/' . $event['id'] . '/share-link') ?>', {
        method: 'POST',
        body: {
            share_password: password,
            share_expires_at: expiresAt
        }
    });

    if (res.ok && res.data.share_url) {
        document.getElementById('share-result-box').style.display = 'block';
        document.getElementById('share-url-input').value = res.data.share_url;
        Dischedule.toast('success', 'Public share link generated!');
    } else {
        Dischedule.toast('error', res.data.message || 'Failed to generate link.');
    }
}
</script>
