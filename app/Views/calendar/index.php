<div class="calendar-card">
    <!-- Top Header & Filter Toolbar -->
    <div class="calendar-header-toolbar">
        <div class="calendar-title-group">
            <h2 class="calendar-month-title" id="calendar-title">Loading...</h2>
            
            <div style="display:flex;align-items:center;gap:4px;">
                <button class="btn btn-secondary" id="cal-today" style="padding:4px 12px;font-size:12px;border-radius:var(--radius-full);font-weight:600;">
                    <i class="fa-regular fa-calendar-check" style="margin-right:4px;"></i> <?= __('today') ?>
                </button>
                <div style="display:flex;gap:2px;">
                    <button class="btn btn-icon btn-secondary" id="cal-prev" title="Previous (Left Arrow)" style="width:28px;height:28px;font-size:11px;border-radius:50%;">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-icon btn-secondary" id="cal-next" title="Next (Right Arrow)" style="width:28px;height:28px;font-size:11px;border-radius:50%;">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Inline Live Filters -->
        <div class="calendar-filters-bar">
            <div style="position:relative;width:170px;">
                <input type="text" id="calendar-search" class="form-control" placeholder="Search events..." style="padding-left:28px;font-size:12px;height:30px;border-radius:var(--radius-full);">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-subtle);font-size:11px;"></i>
            </div>

            <select name="category_id" class="form-control calendar-filter" style="font-size:12px;height:30px;padding:2px 10px;border-radius:var(--radius-full);max-width:150px;">
                <option value=""><?= __('all_categories') ?></option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="priority" class="form-control calendar-filter" style="font-size:12px;height:30px;padding:2px 10px;border-radius:var(--radius-full);max-width:130px;">
                <option value=""><?= __('all_priorities') ?></option>
                <option value="low">Low Priority</option>
                <option value="medium">Medium Priority</option>
                <option value="high">High (Urgent)</option>
            </select>
        </div>

        <!-- View Controls & Create Event -->
        <div class="calendar-view-controls">
            <div class="view-mode-tabs">
                <button class="view-tab <?= ($currentView ?? 'month') === 'month' ? 'active' : '' ?>" data-view="month"><?= __('month') ?></button>
                <button class="view-tab <?= ($currentView ?? '') === 'week' ? 'active' : '' ?>" data-view="week"><?= __('week') ?></button>
                <button class="view-tab <?= ($currentView ?? '') === 'day' ? 'active' : '' ?>" data-view="day"><?= __('day') ?></button>
                <button class="view-tab <?= ($currentView ?? '') === 'agenda' ? 'active' : '' ?>" data-view="agenda"><?= __('agenda') ?></button>
            </div>

            <a href="<?= url('events/create') ?>" class="btn btn-primary" style="padding:6px 14px;font-size:12.5px;border-radius:var(--radius-md);display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                <i class="fa-solid fa-plus"></i>
                <span><?= __('new_event') ?></span>
            </a>
        </div>
    </div>

    <!-- Main Calendar Render Target -->
    <div id="calendar-render-area">
        <div style="padding:80px 20px;text-align:center;color:var(--text-muted);">
            <i class="fa-solid fa-spinner fa-spin" style="font-size:28px;margin-bottom:12px;color:var(--color-primary);"></i>
            <div style="font-size:13.5px;font-weight:500;">Loading calendar events...</div>
        </div>
    </div>

    <!-- Bottom Category Legend & Keyboard Shortcut Footer -->
    <div style="padding:10px 18px;background:var(--bg-subtle);border-top:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;font-size:11.5px;overflow:hidden;max-width:100%;">
        <!-- Categories Chips -->
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;overflow:hidden;">
            <span style="font-weight:700;color:var(--text-muted);text-transform:uppercase;font-size:10px;letter-spacing:0.5px;flex-shrink:0;">Categories:</span>
            <?php foreach ($categories as $cat): ?>
                <span style="display:inline-flex;align-items:center;gap:4px;background:var(--bg-surface);border:1px solid var(--border-color);padding:2px 7px;border-radius:var(--radius-full);color:var(--text-main);font-size:11px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <span style="width:6px;height:6px;border-radius:50%;background-color:<?= e($cat['color']) ?>;flex-shrink:0;"></span>
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($cat['name']) ?></span>
                    <span style="color:var(--text-subtle);font-size:9.5px;flex-shrink:0;">(<?= (int)($cat['events_count'] ?? 0) ?>)</span>
                </span>
            <?php endforeach; ?>
        </div>

        <!-- Shortcuts Hint -->
        <div style="display:flex;align-items:center;gap:6px;color:var(--text-muted);font-size:10.5px;flex-shrink:0;">
            <span>Shortcuts:</span>
            <span><kbd style="background:var(--bg-surface);padding:1px 3px;border-radius:2px;border:1px solid var(--border-color);">T</kbd> Today</span>
            <span><kbd style="background:var(--bg-surface);padding:1px 3px;border-radius:2px;border:1px solid var(--border-color);">M</kbd> Month</span>
            <span><kbd style="background:var(--bg-surface);padding:1px 3px;border-radius:2px;border:1px solid var(--border-color);">W</kbd> Week</span>
            <span><kbd style="background:var(--bg-surface);padding:1px 3px;border-radius:2px;border:1px solid var(--border-color);">D</kbd> Day</span>
            <span><kbd style="background:var(--bg-surface);padding:1px 3px;border-radius:2px;border:1px solid var(--border-color);">A</kbd> Agenda</span>
            <span><kbd style="background:var(--bg-surface);padding:1px 3px;border-radius:2px;border:1px solid var(--border-color);">N</kbd> New Event</span>
        </div>
    </div>
</div>

<script src="<?= asset('js/calendar.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    DischeduleCalendar.init({
        defaultView: '<?= e($currentView ?? 'month') ?>'
    });
});
</script>
