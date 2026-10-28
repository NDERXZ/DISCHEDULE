<div style="max-width:850px;margin:0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">Edit: <?= e($event['title']) ?></h1>
            <p style="font-size:13.5px;color:var(--text-muted);">Update schedule details, reminders, and Discord targets.</p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="<?= url('events/' . $event['id']) ?>" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Event
            </a>
        </div>
    </div>

    <form action="<?= url('events/' . $event['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">Schedule Information</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label"><?= __('event_title') ?> <span style="color:var(--color-danger);">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?= e($event['title']) ?>" required>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label"><?= __('start_time') ?> <span style="color:var(--color-danger);">*</span></label>
                        <input type="datetime-local" name="start_datetime" class="form-control" value="<?= \App\Helpers\DateHelper::formatToUserTimezone($event['start_datetime'], $currentUser['timezone'] ?? 'UTC', 'Y-m-d\TH:i') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('end_time') ?> <span style="color:var(--color-danger);">*</span></label>
                        <input type="datetime-local" name="end_datetime" class="form-control" value="<?= \App\Helpers\DateHelper::formatToUserTimezone($event['end_datetime'], $currentUser['timezone'] ?? 'UTC', 'Y-m-d\TH:i') ?>" required>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label"><?= __('categories') ?></label>
                        <select name="category_id" class="form-control">
                            <option value="">No Category</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ((int)$event['category_id'] === (int)$c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('priority') ?></label>
                        <select name="priority" class="form-control">
                            <option value="low" <?= $event['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= $event['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= $event['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Color Accent</label>
                        <input type="color" name="color" value="<?= e($event['color'] ?? '#4F46E5') ?>" class="form-control" style="height:42px;padding:2px 4px;cursor:pointer;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= __('location') ?></label>
                    <input type="text" name="location" class="form-control" value="<?= e($event['location'] ?? '') ?>" placeholder="e.g., Discord Voice Channel, Google Meet, or Room 301">
                </div>

                <div class="form-group">
                    <label class="form-label"><?= __('description') ?></label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Add event agenda, links, notes..."><?= e($event['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Recurrence Pattern -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">Recurrence Pattern</span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Repeat Frequency</label>
                        <select name="recurrence_rule" class="form-control">
                            <option value="" <?= empty($event['recurrence_rule']) ? 'selected' : '' ?>><?= __('none') ?></option>
                            <option value="FREQ=DAILY" <?= ($event['recurrence_rule'] ?? '') === 'FREQ=DAILY' ? 'selected' : '' ?>><?= __('daily') ?></option>
                            <option value="FREQ=WEEKLY" <?= ($event['recurrence_rule'] ?? '') === 'FREQ=WEEKLY' ? 'selected' : '' ?>><?= __('weekly') ?></option>
                            <option value="FREQ=WEEKLY;BYDAY=MO,WE,FR" <?= ($event['recurrence_rule'] ?? '') === 'FREQ=WEEKLY;BYDAY=MO,WE,FR' ? 'selected' : '' ?>>Weekly on Mon, Wed, Fri</option>
                            <option value="FREQ=MONTHLY" <?= ($event['recurrence_rule'] ?? '') === 'FREQ=MONTHLY' ? 'selected' : '' ?>><?= __('monthly') ?></option>
                            <option value="FREQ=YEARLY" <?= ($event['recurrence_rule'] ?? '') === 'FREQ=YEARLY' ? 'selected' : '' ?>><?= __('yearly') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Repeat Until</label>
                        <input type="date" name="recurrence_end" class="form-control" value="<?= !empty($event['recurrence_end']) ? substr($event['recurrence_end'], 0, 10) : '' ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Automated Reminders -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-regular fa-bell" style="margin-right:8px;color:var(--color-warning);"></i> Automated Reminders</span>
            </div>
            <div class="card-body">
                <?php 
                $firstReminder = !empty($event['reminders']) ? $event['reminders'][0] : null;
                $minutesBefore = $firstReminder ? (int)$firstReminder['minutes_before'] : 0;
                $reminderType = $firstReminder ? $firstReminder['type'] : 'discord';
                ?>
                <div style="display:flex;flex-direction:column;gap:12px;" id="reminders-container">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:center;">
                        <select name="reminders[0][minutes_before]" class="form-control">
                            <option value="0" <?= $minutesBefore === 0 ? 'selected' : '' ?>>At start time (Saat jam mulai - 0 mins)</option>
                            <option value="5" <?= $minutesBefore === 5 ? 'selected' : '' ?>>5 minutes before</option>
                            <option value="10" <?= $minutesBefore === 10 ? 'selected' : '' ?>>10 minutes before</option>
                            <option value="15" <?= $minutesBefore === 15 ? 'selected' : '' ?>>15 minutes before</option>
                            <option value="30" <?= $minutesBefore === 30 ? 'selected' : '' ?>>30 minutes before</option>
                            <option value="60" <?= $minutesBefore === 60 ? 'selected' : '' ?>>1 hour before</option>
                            <option value="1440" <?= $minutesBefore === 1440 ? 'selected' : '' ?>>1 day before</option>
                        </select>
                        <select name="reminders[0][type]" class="form-control">
                            <option value="discord" <?= $reminderType === 'discord' ? 'selected' : '' ?>>Discord Webhook</option>
                            <option value="email" <?= $reminderType === 'email' ? 'selected' : '' ?>>Email Notification</option>
                            <option value="in_app" <?= $reminderType === 'in_app' ? 'selected' : '' ?>>In-App Alert</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Webhook Targets -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-brands fa-discord" style="margin-right:8px;color:var(--color-discord);"></i> Discord Webhook Targets</span>
            </div>
            <div class="card-body">
                <?php 
                $selectedWebhookIds = !empty($event['webhooks']) ? array_column($event['webhooks'], 'id') : [];
                ?>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <?php foreach ($webhooks as $w): ?>
                        <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--bg-subtle);border:1px solid var(--border-color);border-radius:var(--radius-sm);cursor:pointer;">
                            <input type="checkbox" name="webhooks[]" value="<?= $w['id'] ?>" <?= in_array($w['id'], $selectedWebhookIds) ? 'checked' : '' ?> style="cursor:pointer;">
                            <span style="font-weight:600;font-size:13px;"><?= e($w['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:flex-end;gap:12px;margin-bottom:40px;">
            <a href="<?= url('events/' . $event['id']) ?>" class="btn btn-secondary"><?= __('cancel') ?></a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-size:14px;">
                <i class="fa-solid fa-check"></i> Save Changes
            </button>
        </div>
    </form>
</div>
