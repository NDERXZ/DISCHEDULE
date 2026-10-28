<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;"><?= __('categories') ?></h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Organize events with custom colors, icons, and team labels.</p>
    </div>
    <button onclick="Dischedule.openModal('modal-create-category')" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> New Category
    </button>
</div>

<div class="card">
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color);background:var(--bg-subtle);font-size:12px;text-transform:uppercase;color:var(--text-subtle);">
                    <th style="padding:12px 16px;text-align:left;">Category</th>
                    <th style="padding:12px 16px;text-align:left;">Color</th>
                    <th style="padding:12px 16px;text-align:left;">Scope</th>
                    <th style="padding:12px 16px;text-align:left;">Events Count</th>
                    <th style="padding:12px 16px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr style="border-bottom:1px solid var(--border-color);transition:background 0.15s;" onmouseover="this.style.background='var(--bg-subtle)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px;font-weight:600;display:flex;align-items:center;gap:10px;">
                            <span style="width:28px;height:28px;border-radius:var(--radius-sm);background-color:<?= e($cat['color']) ?>20;color:<?= e($cat['color']) ?>;display:flex;align-items:center;justify-content:center;font-size:14px;">
                                <i class="<?= e($cat['icon']) ?>"></i>
                            </span>
                            <?= e($cat['name']) ?>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="display:inline-flex;align-items:center;gap:6px;font-family:monospace;font-size:12px;">
                                <span style="display:inline-block;width:14px;height:14px;border-radius:3px;background-color:<?= e($cat['color']) ?>;"></span>
                                <?= e($cat['color']) ?>
                            </span>
                        </td>
                        <td style="padding:14px 16px;">
                            <?php if (!empty($cat['is_global'])): ?>
                                <span class="badge badge-info">Global Default</span>
                            <?php else: ?>
                                <span class="badge badge-primary">Personal</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 16px;color:var(--text-muted);font-weight:600;">
                            <?= (int)($cat['events_count'] ?? 0) ?>
                        </td>
                        <td style="padding:14px 16px;text-align:right;">
                            <?php if (empty($cat['is_global']) || ($currentUser['role'] ?? '') === 'admin'): ?>
                                <form action="<?= url('categories/' . $cat['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this category?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-icon btn-secondary" style="color:var(--color-danger);" title="Delete">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="font-size:12px;color:var(--text-subtle);">Locked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal-backdrop" id="modal-create-category">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 style="font-size:16px;font-weight:700;"><i class="fa-solid fa-folder-plus"></i> Add Category</h3>
            <button onclick="Dischedule.closeModal('modal-create-category')" class="btn btn-icon"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= url('categories') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Category Name <span style="color:var(--color-danger);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Design Sync, Game Nights" required>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Color Theme</label>
                        <input type="color" name="color" value="#4F46E5" class="form-control" style="height:42px;padding:2px 4px;cursor:pointer;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Icon Class (Font Awesome)</label>
                        <input type="text" name="icon" class="form-control" value="fa-solid fa-calendar" placeholder="fa-solid fa-briefcase">
                    </div>
                </div>

                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:8px;">
                        <input type="checkbox" name="is_global" id="is_global" value="1">
                        <label for="is_global" style="font-size:13px;font-weight:600;cursor:pointer;">Set as Global Category (Visible to all users)</label>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="Dischedule.closeModal('modal-create-category')" class="btn btn-secondary"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Save Category</button>
            </div>
        </form>
    </div>
</div>
