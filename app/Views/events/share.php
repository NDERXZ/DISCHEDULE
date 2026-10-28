<div style="max-width:680px;margin:40px auto;">
    <div class="card" style="box-shadow:var(--shadow-xl);border-top:6px solid <?= e($event['color'] ?? '#4F46E5') ?>;border-radius:var(--radius-lg);">
        <div class="card-body" style="padding:36px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--color-primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;">
                        <?= strtoupper(substr($event['author_name'] ?? 'User', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:var(--text-main);">Hosted by <?= e($event['author_name'] ?? 'User') ?></div>
                        <div style="font-size:11.5px;color:var(--text-muted);">Shared via Dischedule</div>
                    </div>
                </div>
                <?php if (!empty($event['category_name'])): ?>
                    <span class="badge" style="background-color:<?= e($event['category_color'] ?? '#4F46E5') ?>15;color:<?= e($event['category_color'] ?? '#4F46E5') ?>;border:1px solid <?= e($event['category_color'] ?? '#4F46E5') ?>40;font-size:12px;padding:5px 12px;">
                        <?= e($event['category_name']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <h1 style="font-size:30px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:20px;line-height:1.2;">
                <?= e($event['title']) ?>
            </h1>

            <div style="background:var(--bg-subtle);border-radius:var(--radius-md);padding:18px;margin-bottom:24px;display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;align-items:center;gap:12px;font-size:14px;">
                    <i class="fa-regular fa-calendar" style="color:var(--color-primary);font-size:16px;width:20px;"></i>
                    <span><strong>Starts:</strong> <?= date('l, F j, Y \a\t g:i A', strtotime($event['start_datetime'])) ?> (UTC)</span>
                </div>
                <div style="display:flex;align-items:center;gap:12px;font-size:14px;">
                    <i class="fa-regular fa-clock" style="color:var(--color-danger);font-size:16px;width:20px;"></i>
                    <span><strong>Ends:</strong> <?= date('l, F j, Y \a\t g:i A', strtotime($event['end_datetime'])) ?> (UTC)</span>
                </div>
                <?php if (!empty($event['location'])): ?>
                    <div style="display:flex;align-items:center;gap:12px;font-size:14px;">
                        <i class="fa-solid fa-location-dot" style="color:var(--color-warning);font-size:16px;width:20px;"></i>
                        <span>
                            <?= e($event['location']) ?>
                            <?php if (!empty($event['location_url'])): ?>
                                &bull; <a href="<?= e($event['location_url']) ?>" target="_blank" rel="noopener" style="font-weight:600;">Open Link <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;"></i></a>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($event['description'])): ?>
                <div style="margin-bottom:28px;">
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-subtle);">Event Details</h3>
                    <div style="font-size:15px;color:var(--text-main);line-height:1.6;white-space:pre-wrap;"><?= e($event['description']) ?></div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div style="display:flex;gap:12px;flex-wrap:wrap;border-top:1px solid var(--border-color);padding-top:24px;">
                <a href="<?= url('events/export/ics?event_id=' . $event['id']) ?>" class="btn btn-primary" style="flex:1;padding:12px;font-size:14px;">
                    <i class="fa-regular fa-calendar-plus"></i> Add to Calendar (.ics)
                </a>
                <?php if (!empty($event['location_url'])): ?>
                    <a href="<?= e($event['location_url']) ?>" target="_blank" rel="noopener" class="btn btn-discord" style="flex:1;padding:12px;font-size:14px;">
                        <i class="fa-solid fa-video"></i> Join Location
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
