<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <div style="display:flex;align-items:center;gap:10px;">
            <a href="<?= url('teams') ?>" class="btn btn-icon btn-secondary"><i class="fa-solid fa-arrow-left"></i></a>
            <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);"><?= e($team['name']) ?></h1>
            <span class="badge badge-primary"><?= ucfirst(e($userRole ?? 'member')) ?></span>
        </div>
        <p style="font-size:13.5px;color:var(--text-muted);margin-top:4px;"><?= e($team['description'] ?? 'Team workspace and shared calendar.') ?></p>
    </div>

    <?php if (in_array($userRole, ['owner', 'admin'], true)): ?>
        <div style="display:flex;gap:10px;">
            <button onclick="Dischedule.openModal('modal-invite-member')" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> Invite Member
            </button>
            <?php if ($userRole === 'owner'): ?>
                <form action="<?= url('teams/' . $team['id'] . '/delete') ?>" method="POST" style="margin:0;" onsubmit="return confirm('Delete this team? All shared associations will be removed.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-users" style="margin-right:8px;color:var(--color-primary);"></i> Team Members (<?= count($members) ?>)</span>
    </div>
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color);background:var(--bg-subtle);font-size:12px;text-transform:uppercase;color:var(--text-subtle);">
                    <th style="padding:12px 16px;text-align:left;">Member</th>
                    <th style="padding:12px 16px;text-align:left;">Email</th>
                    <th style="padding:12px 16px;text-align:left;">Team Role</th>
                    <th style="padding:12px 16px;text-align:left;">Joined Date</th>
                    <th style="padding:12px 16px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $m): ?>
                    <tr style="border-bottom:1px solid var(--border-color);transition:background 0.15s;" onmouseover="this.style.background='var(--bg-subtle)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px;display:flex;align-items:center;gap:10px;font-weight:600;">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--color-primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-size:13px;">
                                <?= strtoupper(substr($m['username'], 0, 1)) ?>
                            </div>
                            <span><?= e($m['username']) ?></span>
                        </td>
                        <td style="padding:14px 16px;font-size:13px;color:var(--text-muted);"><?= e($m['email']) ?></td>
                        <td style="padding:14px 16px;">
                            <span class="badge badge-<?= $m['team_role'] === 'owner' ? 'primary' : ($m['team_role'] === 'admin' ? 'info' : 'secondary') ?>">
                                <?= ucfirst(e($m['team_role'])) ?>
                            </span>
                        </td>
                        <td style="padding:14px 16px;font-size:12.5px;color:var(--text-subtle);">
                            <?= date('M j, Y', strtotime($m['joined_at'])) ?>
                        </td>
                        <td style="padding:14px 16px;text-align:right;">
                            <?php if ($m['team_role'] !== 'owner' && in_array($userRole, ['owner', 'admin'], true)): ?>
                                <form action="<?= url('teams/' . $team['id'] . '/remove-member') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Remove this member?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="user_id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-icon btn-secondary" style="color:var(--color-danger);" title="Remove">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Invite Member Modal -->
<div class="modal-backdrop" id="modal-invite-member">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 style="font-size:16px;font-weight:700;"><i class="fa-solid fa-user-plus"></i> Invite Team Member</h3>
            <button onclick="Dischedule.closeModal('modal-invite-member')" class="btn btn-icon"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= url('teams/' . $team['id'] . '/invite') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Username or Email Address <span style="color:var(--color-danger);">*</span></label>
                    <input type="text" name="identifier" class="form-control" placeholder="alex or alex@example.com" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Team Permission Role</label>
                    <select name="role" class="form-control">
                        <option value="member" selected>Member (Can view and contribute)</option>
                        <option value="admin">Admin (Can manage events and members)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="Dischedule.closeModal('modal-invite-member')" class="btn btn-secondary"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Add Member</button>
            </div>
        </form>
    </div>
</div>
