<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Maintenance - Dischedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background-color:var(--bg-body);padding:20px;text-align:center;">
    <div class="card" style="max-width:480px;padding:40px 24px;border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--color-warning-bg);color:var(--color-warning);display:inline-flex;align-items:center;justify-content:center;font-size:28px;margin-bottom:20px;">
            <i class="fa-solid fa-wrench"></i>
        </div>
        <h1 style="font-size:24px;font-weight:800;color:var(--text-main);margin-bottom:8px;">Scheduled Maintenance</h1>
        <p style="color:var(--text-muted);font-size:14px;line-height:1.5;margin-bottom:24px;">
            Dischedule is undergoing planned server upgrades and Discord bot synchronization. We'll be back online in a few moments!
        </p>
        <button onclick="window.location.reload()" class="btn btn-primary" style="width:100%;">
            <i class="fa-solid fa-rotate-right"></i> Check Status Again
        </button>
    </div>
</body>
</html>
