<div style="max-width:850px;margin:0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;"><?= __('new_event') ?></h1>
            <p style="font-size:13.5px;color:var(--text-muted);">Configure detailed schedule, recurrence patterns, and Discord webhook triggers.</p>
        </div>
        <a href="<?= url('events') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Events
        </a>
    </div>

    <form action="<?= url('events') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-circle-info" style="margin-right:8px;color:var(--color-primary);"></i> Basic Information</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label"><?= __('event_title') ?> <span style="color:var(--color-danger);">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Q3 Product Roadmap Presentation" value="<?= e(old('title')) ?>" required autofocus>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label"><?= __('start_time') ?> <span style="color:var(--color-danger);">*</span></label>
                        <input type="datetime-local" name="start_datetime" class="form-control" value="<?= e(old('start_datetime', $initialStart ?? (($initialDate ?? date('Y-m-d')) . 'T09:00'))) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('end_time') ?> <span style="color:var(--color-danger);">*</span></label>
                        <input type="datetime-local" name="end_datetime" class="form-control" value="<?= e(old('end_datetime', $initialEnd ?? (($initialDate ?? date('Y-m-d')) . 'T10:00'))) ?>" required>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label"><?= __('categories') ?></label>
                        <select name="category_id" class="form-control">
                            <option value="">No Category</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('priority') ?></label>
                        <select name="priority" class="form-control">
                            <option value="low">Low Priority</option>
                            <option value="medium" selected>Medium Priority</option>
                            <option value="high">High Priority (Urgent)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Color Accent</label>
                        <input type="color" name="color" value="#4F46E5" class="form-control" style="height:42px;padding:2px 4px;cursor:pointer;">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label"><?= __('location') ?></label>
                        <input type="text" name="location" class="form-control" placeholder="Room 401, Discord #stage, or Zoom">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Meeting URL (Optional)</label>
                        <input type="url" name="location_url" class="form-control" placeholder="https://discord.gg/... or https://meet.google.com/...">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= __('description') ?></label>
                    <textarea name="description" rows="4" class="form-control" placeholder="Write full agenda, links, goals, or preparation notes..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Tags (comma-separated)</label>
                    <input type="text" name="tags" class="form-control" placeholder="sprint, discord, launch, review">
                </div>
            </div>
        </div>

        <!-- Recurrence Engine -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-repeat" style="margin-right:8px;color:var(--color-info);"></i> Recurrence & Repetition</span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Repeat Pattern</label>
                        <select name="recurrence_rule" class="form-control" id="recurrence-select">
                            <option value=""><?= __('none') ?></option>
                            <option value="FREQ=DAILY"><?= __('daily') ?></option>
                            <option value="FREQ=WEEKLY"><?= __('weekly') ?></option>
                            <option value="FREQ=WEEKLY;BYDAY=MO,WE,FR">Weekly on Mon, Wed, Fri</option>
                            <option value="FREQ=MONTHLY"><?= __('monthly') ?></option>
                            <option value="FREQ=YEARLY"><?= __('yearly') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Repeat Until (Optional End Date)</label>
                        <input type="date" name="recurrence_end" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- Discord Webhook Integration -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-brands fa-discord" style="margin-right:8px;color:var(--color-discord);"></i> Discord Webhook Dispatch</span>
                <a href="<?= url('webhooks') ?>" target="_blank" style="font-size:12px;font-weight:600;">+ Add Webhook</a>
            </div>
            <div class="card-body">
                <?php if (empty($webhooks)): ?>
                    <div style="padding:16px;background:var(--bg-subtle);border-radius:var(--radius-sm);color:var(--text-muted);font-size:13px;">
                        No Discord webhooks configured. <a href="<?= url('webhooks') ?>" target="_blank" style="font-weight:600;">Configure a webhook</a> to send automated embeds to Discord channels.
                    </div>
                <?php else: ?>
                    <p style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">Select channels to receive instant rich embeds when this event is scheduled or fired:</p>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <?php foreach ($webhooks as $w): ?>
                            <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--bg-subtle);border:1px solid var(--border-color);border-radius:var(--radius-sm);cursor:pointer;">
                                <input type="checkbox" name="webhooks[]" value="<?= $w['id'] ?>" <?= !empty($w['is_default']) ? 'checked' : '' ?> style="cursor:pointer;">
                                <span style="font-weight:600;font-size:13px;"><?= e($w['name']) ?></span>
                                <?php if (!empty($w['mention_role'])): ?>
                                    <span class="badge badge-info"><?= e($w['mention_role']) ?></span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reminders -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-regular fa-bell" style="margin-right:8px;color:var(--color-warning);"></i> Automated Reminders</span>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:12px;" id="reminders-container">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:center;">
                        <select name="reminders[0][minutes_before]" class="form-control">
                            <option value="0" selected>At start time (Saat jam mulai - 0 mins)</option>
                            <option value="5">5 minutes before</option>
                            <option value="10">10 minutes before</option>
                            <option value="15">15 minutes before</option>
                            <option value="30">30 minutes before</option>
                            <option value="60">1 hour before</option>
                            <option value="1440">1 day before</option>
                        </select>
                        <select name="reminders[0][type]" class="form-control">
                            <option value="discord" selected>Discord Webhook</option>
                            <option value="email">Email Notification</option>
                            <option value="in_app">In-App Alert</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Attachment -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-paperclip" style="margin-right:8px;color:var(--text-subtle);"></i> Attachments</span>
            </div>
            <div class="card-body">
                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.ics,.csv">
                <div style="font-size:11.5px;color:var(--text-subtle);margin-top:4px;">
                    Supported: PDF, Images, Word, Excel, ICS, CSV (Max 10 MB).
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:flex-end;gap:12px;margin-bottom:40px;">
            <a href="<?= url('events') ?>" class="btn btn-secondary"><?= __('cancel') ?></a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-size:14px;">
                <i class="fa-solid fa-check"></i> Publish Event
            </button>
        </div>
    </form>
</div>
