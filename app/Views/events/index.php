<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;"><?= __('events') ?></h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Manage all scheduled activities, recurring rules, and webhook links.</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="<?= url('events/import-export') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-file-import"></i> Import / Export
        </a>
        <a href="<?= url('events/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> <?= __('new_event') ?>
        </a>
    </div>
</div>

<!-- Filters Bar -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px;">
        <form action="<?= url('events') ?>" method="GET" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)) 100px;gap:12px;align-items:flex-end;">
            <div>
                <label class="form-label" style="font-size:12px;">Search Events</label>
                <input type="text" name="search" class="form-control" placeholder="Search title or location..." value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <div>
                <label class="form-label" style="font-size:12px;"><?= __('categories') ?></label>
                <select name="category_id" class="form-control">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($filters['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size:12px;"><?= __('priority') ?></label>
                <select name="priority" class="form-control">
                    <option value="">All Priorities</option>
                    <option value="low" <?= ($filters['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= ($filters['priority'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size:12px;"><?= __('status') ?></label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="archived" <?= ($filters['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-secondary" style="width:100%;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions Form & Events Table -->
<form action="<?= url('events/bulk') ?>" method="POST" id="bulk-events-form">
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-header" style="background:var(--bg-subtle);">
            <div style="display:flex;align-items:center;gap:12px;">
                <input type="checkbox" id="select-all-events" style="cursor:pointer;">
                <span style="font-size:13px;font-weight:600;">Select All</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <select name="action" class="form-control" style="width:160px;padding:5px 10px;font-size:12.5px;">
                    <option value="">Bulk Actions</option>
                    <option value="archive">Archive Selected</option>
                    <option value="delete">Delete Selected</option>
                </select>
                <button type="submit" class="btn btn-secondary" style="padding:5px 12px;font-size:12.5px;">Apply</button>
            </div>
        </div>

        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color);background:var(--bg-subtle);font-size:12px;text-transform:uppercase;color:var(--text-subtle);">
                        <th style="padding:12px 16px;width:40px;"></th>
                        <th style="padding:12px 16px;text-align:left;"><?= __('event_title') ?></th>
                        <th style="padding:12px 16px;text-align:left;"><?= __('start_time') ?></th>
                        <th style="padding:12px 16px;text-align:left;"><?= __('priority') ?></th>
                        <th style="padding:12px 16px;text-align:left;"><?= __('status') ?></th>
                        <th style="padding:12px 16px;text-align:right;"><?= __('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($paginator['items'])): ?>
                        <tr>
                            <td colspan="6" style="padding:48px;text-align:center;color:var(--text-muted);">
                                <i class="fa-regular fa-calendar-xmark" style="font-size:32px;margin-bottom:8px;display:block;opacity:0.5;"></i>
                                <?= __('no_events_found') ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($paginator['items'] as $ev): ?>
                            <tr style="border-bottom:1px solid var(--border-color);transition:background 0.15s;" onmouseover="this.style.background='var(--bg-subtle)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:14px 16px;text-align:center;">
                                    <input type="checkbox" name="event_ids[]" value="<?= $ev['id'] ?>" class="event-checkbox" style="cursor:pointer;">
                                </td>
                                <td style="padding:14px 16px;">
                                    <a href="<?= url('events/' . $ev['id']) ?>" style="font-weight:700;font-size:14px;color:var(--text-main);display:flex;align-items:center;gap:8px;">
                                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:<?= e($ev['color'] ?? '#4F46E5') ?>;"></span>
                                        <?= e($ev['title']) ?>
                                    </a>
                                    <?php if (!empty($ev['location'])): ?>
                                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                                            <i class="fa-solid fa-location-dot" style="margin-right:4px;"></i> <?= e($ev['location']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:14px 16px;font-size:13px;color:var(--text-muted);">
                                    <?= \App\Helpers\DateHelper::humanReadable($ev['start_datetime'], $currentUser['timezone'] ?? 'UTC') ?>
                                </td>
                                <td style="padding:14px 16px;">
                                    <span class="badge badge-<?= $ev['priority'] === 'high' ? 'danger' : ($ev['priority'] === 'medium' ? 'warning' : 'info') ?>">
                                        <?= ucfirst(e($ev['priority'])) ?>
                                    </span>
                                </td>
                                <td style="padding:14px 16px;">
                                    <span class="badge badge-<?= $ev['status'] === 'published' ? 'success' : ($ev['status'] === 'draft' ? 'warning' : 'info') ?>">
                                        <?= ucfirst(e($ev['status'])) ?>
                                    </span>
                                </td>
                                <td style="padding:14px 16px;text-align:right;">
                                    <div style="display:inline-flex;gap:6px;">
                                        <a href="<?= url('events/' . $ev['id']) ?>" class="btn btn-icon btn-secondary" title="View Details">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <a href="<?= url('events/' . $ev['id'] . '/edit') ?>" class="btn btn-icon btn-secondary" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <button type="button" onclick="if(confirm('Are you sure you want to delete this event?')) { document.getElementById('delete-form-<?= $ev['id'] ?>').submit(); }" class="btn btn-icon btn-secondary" title="Delete" style="color:var(--color-danger);">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <?php if ($paginator['total_pages'] > 1): ?>
            <div style="padding:16px 20px;border-top:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;background:var(--bg-subtle);font-size:13px;">
                <span style="color:var(--text-muted);">
                    Showing page <?= $paginator['current_page'] ?> of <?= $paginator['total_pages'] ?> (<?= $paginator['total'] ?> total events)
                </span>
                <div style="display:flex;gap:6px;">
                    <?php if ($paginator['current_page'] > 1): ?>
                        <a href="<?= url('events?page=' . ($paginator['current_page'] - 1)) ?>" class="btn btn-secondary" style="padding:4px 10px;">← Previous</a>
                    <?php endif; ?>
                    <?php if ($paginator['has_more']): ?>
                        <a href="<?= url('events?page=' . ($paginator['current_page'] + 1)) ?>" class="btn btn-secondary" style="padding:4px 10px;">Next →</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</form>

<!-- Hidden Delete Forms -->
<?php if (!empty($paginator['items'])): ?>
    <?php foreach ($paginator['items'] as $ev): ?>
        <form id="delete-form-<?= $ev['id'] ?>" action="<?= url('events/' . $ev['id'] . '/delete') ?>" method="POST" style="display:none;">
            <?= csrf_field() ?>
        </form>
    <?php endforeach; ?>
<?php endif; ?>

<script>
document.getElementById('select-all-events')?.addEventListener('change', (e) => {
    document.querySelectorAll('.event-checkbox').forEach(cb => cb.checked = e.target.checked);
});
</script>
