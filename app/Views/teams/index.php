<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;"><?= __('teams') ?></h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Collaborate on shared team calendars, deadlines, and group webhooks.</p>
    </div>
    <button onclick="Dischedule.openModal('modal-create-team')" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Create New Team
    </button>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:20px;">
    <?php if (empty($teams)): ?>
        <div class="card" style="grid-column:1 / -1;text-align:center;padding:48px 20px;">
            <i class="fa-solid fa-users" style="font-size:36px;color:var(--color-primary);opacity:0.4;margin-bottom:12px;display:block;"></i>
            <h3 style="font-size:16px;font-weight:700;margin-bottom:6px;">No teams joined yet</h3>
            <p style="color:var(--text-muted);font-size:13px;margin-bottom:16px;">Create a team to share calendars and coordinate with team members.</p>
            <button onclick="Dischedule.openModal('modal-create-team')" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Create Your First Team
            </button>
        </div>
    <?php else: ?>
        <?php foreach ($teams as $team): ?>
            <div class="card" style="transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                <div class="card-header" style="background:var(--bg-subtle);">
                    <span class="card-title" style="font-size:15px;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-users" style="color:var(--color-primary);"></i>
                        <?= e($team['name']) ?>
                    </span>
                    <span class="badge badge-<?= $team['user_role'] === 'owner' ? 'primary' : 'secondary' ?>">
                        <?= ucfirst(e($team['user_role'])) ?>
                    </span>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;justify-content:space-between;min-height:140px;">
                    <p style="font-size:13px;color:var(--text-muted);line-height:1.5;margin-bottom:16px;">
                        <?= e($team['description'] ?? 'No description provided.') ?>
                    </p>
                    <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border-color);padding-top:12px;">
                        <span style="font-size:12px;color:var(--text-subtle);">
                            <i class="fa-solid fa-user-group"></i> <?= (int)$team['members_count'] ?> members
                        </span>
                        <a href="<?= url('teams/' . $team['id']) ?>" class="btn btn-secondary" style="font-size:12px;padding:4px 12px;">
                            Manage Team →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Create Team Modal -->
<div class="modal-backdrop" id="modal-create-team">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 style="font-size:16px;font-weight:700;"><i class="fa-solid fa-users"></i> Create New Team</h3>
            <button onclick="Dischedule.closeModal('modal-create-team')" class="btn btn-icon"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= url('teams') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Team Name <span style="color:var(--color-danger);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Core Engineering / Design Guild" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="What does this team do?"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="Dischedule.closeModal('modal-create-team')" class="btn btn-secondary"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Create Team</button>
            </div>
        </form>
    </div>
</div>
