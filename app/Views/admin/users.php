<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">User Directory</h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Manage platform accounts, assign moderator/admin permissions, and handle suspensions.</p>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px;">
        <form action="<?= url('admin/users') ?>" method="GET" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)) 100px;gap:12px;align-items:flex-end;">
            <div>
                <label class="form-label" style="font-size:12px;">Search Users</label>
                <input type="text" name="search" class="form-control" placeholder="Search username or email..." value="<?= e($search ?? '') ?>">
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">Role</label>
                <select name="role" class="form-control">
                    <option value="">All Roles</option>
                    <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                    <option value="moderator" <?= ($role ?? '') === 'moderator' ? 'selected' : '' ?>>Moderator</option>
                    <option value="user" <?= ($role ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="banned" <?= ($status ?? '') === 'banned' ? 'selected' : '' ?>>Banned</option>
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

<div class="card">
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color);background:var(--bg-subtle);font-size:12px;text-transform:uppercase;color:var(--text-subtle);">
                    <th style="padding:12px 16px;text-align:left;">User</th>
                    <th style="padding:12px 16px;text-align:left;">Role</th>
                    <th style="padding:12px 16px;text-align:left;">Discord</th>
                    <th style="padding:12px 16px;text-align:left;">Status</th>
                    <th style="padding:12px 16px;text-align:left;">Registered</th>
                    <th style="padding:12px 16px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginator['items'] as $u): ?>
                    <tr style="border-bottom:1px solid var(--border-color);transition:background 0.15s;" onmouseover="this.style.background='var(--bg-subtle)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 16px;display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:var(--color-primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                                <?= strtoupper(substr($u['username'], 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:13.5px;color:var(--text-main);"><?= e($u['username']) ?></div>
                                <div style="font-size:11.5px;color:var(--text-muted);"><?= e($u['email']) ?></div>
                            </div>
                        </td>
                        <td style="padding:12px 16px;">
                            <form action="<?= url('admin/users/' . $u['id'] . '/role') ?>" method="POST" style="margin:0;">
                                <?= csrf_field() ?>
                                <select name="role" onchange="this.form.submit()" class="form-control" style="font-size:12px;padding:3px 8px;width:120px;">
                                    <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="moderator" <?= $u['role'] === 'moderator' ? 'selected' : '' ?>>Moderator</option>
                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td style="padding:12px 16px;font-size:12.5px;">
                            <?php if (!empty($u['discord_username'])): ?>
                                <span style="display:flex;align-items:center;gap:6px;color:var(--color-discord);font-weight:600;">
                                    <i class="fa-brands fa-discord"></i> <?= e($u['discord_username']) ?>
                                </span>
                            <?php else: ?>
                                <span style="color:var(--text-subtle);">None</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:12px 16px;">
                            <span class="badge badge-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= ucfirst(e($u['status'])) ?>
                            </span>
                        </td>
                        <td style="padding:12px 16px;font-size:12.5px;color:var(--text-muted);">
                            <?= date('M j, Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td style="padding:12px 16px;text-align:right;">
                            <div style="display:inline-flex;gap:6px;">
                                <form action="<?= url('admin/users/' . $u['id'] . '/toggle-ban') ?>" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-secondary" style="font-size:11.5px;padding:3px 8px;">
                                        <?= $u['status'] === 'banned' ? 'Unban' : 'Ban' ?>
                                    </button>
                                </form>
                                <?php if ($u['id'] !== $currentUser['id']): ?>
                                    <form action="<?= url('admin/users/' . $u['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete user?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-icon btn-secondary" style="color:var(--color-danger);" title="Delete User">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
