<div class="modal-backdrop" id="modal-create-event">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 style="font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px;margin:0;">
                <i class="fa-solid fa-calendar-plus" style="color:var(--color-primary);"></i>
                <span><?= __('new_event') ?></span>
            </h3>
            <button type="button" onclick="Dischedule.closeModal('modal-create-event')" class="btn btn-icon" title="<?= __('close') ?>">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="<?= url('events') ?>" method="POST" enctype="multipart/form-data" id="quick-event-form">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label"><?= __('event_title') ?> <span style="color:var(--color-danger);">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Weekly Team Sync / Game Night" required autofocus>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label"><?= __('start_time') ?> <span style="color:var(--color-danger);">*</span></label>
                        <input type="datetime-local" name="start_datetime" id="modal-event-start" class="form-control" value="<?= date('Y-m-d\TH:00', strtotime('+1 hour')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('end_time') ?> <span style="color:var(--color-danger);">*</span></label>
                        <input type="datetime-local" name="end_datetime" id="modal-event-end" class="form-control" value="<?= date('Y-m-d\TH:00', strtotime('+2 hours')) ?>" required>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label"><?= __('categories') ?></label>
                        <select name="category_id" class="form-control">
                            <option value="">No Category</option>
                            <?php 
                            $modalCats = \App\Models\Category::getForUser(\Core\Session::getUserId() ?? 0);
                            foreach ($modalCats as $mc): ?>
                                <option value="<?= $mc['id'] ?>"><?= e($mc['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('priority') ?></label>
                        <select name="priority" class="form-control">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High (Urgent)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= __('location') ?></label>
                    <input type="text" name="location" class="form-control" placeholder="Physical address, Google Meet, or Discord #channel">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label"><?= __('recurrence') ?></label>
                        <select name="recurrence_rule" class="form-control">
                            <option value=""><?= __('none') ?></option>
                            <option value="FREQ=DAILY"><?= __('daily') ?></option>
                            <option value="FREQ=WEEKLY"><?= __('weekly') ?></option>
                            <option value="FREQ=MONTHLY"><?= __('monthly') ?></option>
                            <option value="FREQ=YEARLY"><?= __('yearly') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Visibility</label>
                        <select name="visibility" class="form-control">
                            <option value="private">Private (Only Me)</option>
                            <option value="public">Public (Shareable)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tags (comma-separated)</label>
                    <input type="text" name="tags" class="form-control" placeholder="sprint, discord, release, meeting">
                </div>

                <div class="form-group" style="margin-bottom:4px;">
                    <label class="form-label"><?= __('description') ?></label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Event overview, agenda items, Discord stage details..."></textarea>
                </div>
            </div>

            <div class="modal-footer" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <a href="<?= url('events/create') ?>" style="font-size:12.5px;color:var(--color-primary);text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-weight:600;">
                        <i class="fa-solid fa-up-right-from-square" style="font-size:11px;"></i> <?= __('full_form') ?? 'Full Form' ?>
                    </a>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <button type="button" onclick="Dischedule.closeModal('modal-create-event')" class="btn btn-secondary"><?= __('cancel') ?></button>
                    <button type="submit" class="btn btn-primary" id="btn-save-event" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-weight:600;">
                        <i class="fa-solid fa-plus"></i> <?= __('create_event') ?? 'Create Event' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
