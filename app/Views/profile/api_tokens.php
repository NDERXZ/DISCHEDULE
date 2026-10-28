<div style="max-width:800px;margin:0 auto;">
    <div style="margin-bottom:24px;">
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">REST API Tokens</h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Generate Bearer access tokens to programmatically interact with Dischedule REST API.</p>
    </div>

    <!-- Navigation Tabs -->
    <div style="display:flex;gap:8px;border-bottom:1px solid var(--border-color);margin-bottom:24px;padding-bottom:2px;">
        <a href="<?= url('profile') ?>" class="btn btn-secondary">
            <i class="fa-regular fa-user"></i> Profile
        </a>
        <a href="<?= url('profile/security') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-lock"></i> Security & Password
        </a>
        <a href="<?= url('profile/api-tokens') ?>" class="btn btn-secondary active" style="background:var(--color-primary-light);color:var(--color-primary);border-color:transparent;font-weight:600;">
            <i class="fa-solid fa-key"></i> API Tokens
        </a>
    </div>

    <?php if (!empty($newPlainToken)): ?>
        <div class="card" style="margin-bottom:24px;background:var(--color-success-bg);border:1px solid rgba(16, 185, 129, 0.3);">
            <div class="card-body" style="padding:20px;">
                <div style="font-weight:700;color:var(--color-success);font-size:14px;margin-bottom:6px;">
                    <i class="fa-solid fa-circle-check"></i> New API Token Generated!
                </div>
                <p style="font-size:13px;color:var(--text-main);margin-bottom:12px;">
                    Make sure to copy your personal API token now. You will not be able to see it again!
                </p>
                <div style="display:flex;gap:8px;">
                    <input type="text" value="<?= e($newPlainToken) ?>" class="form-control" readonly style="font-family:monospace;font-size:13px;background:var(--bg-surface);">
                    <button onclick="Dischedule.copyToClipboard('<?= e($newPlainToken) ?>')" class="btn btn-primary">
                        <i class="fa-regular fa-copy"></i> Copy
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Generate Token Card -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-plus" style="margin-right:8px;color:var(--color-primary);"></i> Create New Token</span>
        </div>
        <div class="card-body">
            <form action="<?= url('profile/api-tokens') ?>" method="POST">
                <?= csrf_field() ?>
                <div style="display:grid;grid-template-columns:1fr 140px;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Token Name <span style="color:var(--color-danger);">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Discord Bot Integration, CLI Script" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expires In</label>
                        <select name="expires_days" class="form-control">
                            <option value="">Never</option>
                            <option value="30" selected>30 Days</option>
                            <option value="90">90 Days</option>
                            <option value="365">1 Year</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Permissions</label>
                    <div style="display:flex;gap:20px;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                            <input type="checkbox" name="permissions[]" value="read" checked> Read Events & Categories
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                            <input type="checkbox" name="permissions[]" value="write" checked> Create & Update Events
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                            <input type="checkbox" name="permissions[]" value="delete"> Delete Events
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="padding:8px 18px;font-size:13px;">
                    <i class="fa-solid fa-key"></i> Generate Token
                </button>
            </form>
        </div>
    </div>

    <!-- Active Tokens Table -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Active API Tokens</span>
        </div>
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color);background:var(--bg-subtle);font-size:12px;text-transform:uppercase;color:var(--text-subtle);">
                        <th style="padding:12px 16px;text-align:left;">Name</th>
                        <th style="padding:12px 16px;text-align:left;">Permissions</th>
                        <th style="padding:12px 16px;text-align:left;">Last Used</th>
                        <th style="padding:12px 16px;text-align:left;">Expires</th>
                        <th style="padding:12px 16px;text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tokens)): ?>
                        <tr>
                            <td colspan="5" style="padding:32px;text-align:center;color:var(--text-muted);font-size:13px;">
                                No active API tokens generated yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tokens as $tk): ?>
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:12px 16px;font-weight:600;"><?= e($tk['name']) ?></td>
                                <td style="padding:12px 16px;">
                                    <span class="badge badge-info"><?= e($tk['permissions']) ?></span>
                                </td>
                                <td style="padding:12px 16px;font-size:12.5px;color:var(--text-muted);">
                                    <?= !empty($tk['last_used_at']) ? \App\Helpers\DateHelper::timeAgo($tk['last_used_at']) : 'Never' ?>
                                </td>
                                <td style="padding:12px 16px;font-size:12.5px;color:var(--text-muted);">
                                    <?= !empty($tk['expires_at']) ? date('M j, Y', strtotime($tk['expires_at'])) : 'Never' ?>
                                </td>
                                <td style="padding:12px 16px;text-align:right;">
                                    <form action="<?= url('profile/api-tokens/' . $tk['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Revoke this token?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary" style="color:var(--color-danger);font-size:12px;padding:3px 8px;">Revoke</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
