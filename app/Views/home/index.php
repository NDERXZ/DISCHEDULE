<div style="max-width:1100px;margin:40px auto;text-align:center;">
    <!-- Logo & Hero Section -->
    <div style="margin-bottom:20px;">
        <img src="https://i.imgur.com/Yo8WHdg.png" alt="Dischedule Logo" style="height:64px;width:auto;object-fit:contain;filter:drop-shadow(0 4px 12px rgba(79, 70, 229, 0.25));">
    </div>

    <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 16px;background:var(--color-primary-light);border:1px solid var(--color-primary-focus);border-radius:var(--radius-full);color:var(--color-primary);font-weight:600;font-size:13px;margin-bottom:24px;">
        <i class="fa-brands fa-discord" style="color:var(--color-discord);"></i> Deep Discord Webhooks & OAuth2 Integration
    </div>

    <h1 style="font-size:44px;font-weight:800;letter-spacing:-1px;line-height:1.15;color:var(--text-main);margin-bottom:18px;">
        Schedule & Event Management <br><span style="color:var(--color-primary);">Deeply Connected to Discord</span>
    </h1>

    <p style="font-size:18px;color:var(--text-muted);max-width:700px;margin:0 auto 36px auto;line-height:1.6;">
        Create personal schedules, organize team deadlines, and broadcast rich real-time event updates and reminders directly to your Discord channels.
    </p>

    <div style="display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;margin-bottom:60px;">
        <a href="<?= url('register') ?>" class="btn btn-primary" style="padding:12px 28px;font-size:15px;">
            <i class="fa-solid fa-rocket"></i> Get Started Free
        </a>
        <a href="<?= url('auth/discord') ?>" class="btn btn-discord" style="padding:12px 28px;font-size:15px;">
            <i class="fa-brands fa-discord"></i> Continue with Discord
        </a>
        <a href="<?= url('login') ?>" class="btn btn-secondary" style="padding:12px 24px;font-size:15px;">
            Sign In
        </a>
    </div>

    <!-- Feature Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:24px;text-align:left;">
        <div class="card">
            <div class="card-body" style="padding:28px;">
                <div style="width:44px;height:44px;border-radius:var(--radius-sm);background:var(--color-primary-light);color:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:16px;">
                    <i class="fa-regular fa-calendar-days"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;color:var(--text-main);">Interactive Calendar</h3>
                <p style="color:var(--text-muted);font-size:14px;line-height:1.5;">
                    Month, Week, Day, and Agenda views with smooth drag-and-drop scheduling, timezone awareness, and customizable category tags.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding:28px;">
                <div style="width:44px;height:44px;border-radius:var(--radius-sm);background:var(--color-discord-light);color:var(--color-discord);display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:16px;">
                    <i class="fa-brands fa-discord"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;color:var(--text-main);">Discord Webhook Alerts</h3>
                <p style="color:var(--text-muted);font-size:14px;line-height:1.5;">
                    Send rich formatted Discord embeds with role mentions (`@everyone`, `@here`) when events are scheduled, edited, or starting.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding:28px;">
                <div style="width:44px;height:44px;border-radius:var(--radius-sm);background:var(--color-success-bg);color:var(--color-success);display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:16px;">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;color:var(--text-main);">High-Performance Native PHP</h3>
                <p style="color:var(--text-muted);font-size:14px;line-height:1.5;">
                    Engineered with pure native PHP 8.1+, PDO prepared queries, zero heavy bloatware, blazing sub-millisecond page loads, and full REST API.
                </p>
            </div>
        </div>
    </div>
</div>
