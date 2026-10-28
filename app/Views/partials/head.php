<!DOCTYPE html>
<html lang="<?= e($currentLang ?? 'en') ?>" data-theme="<?= e($currentUser['theme'] ?? 'system') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e($csrfToken ?? '') ?>">
    <meta name="description" content="Dischedule is a high-performance event and schedule management platform deeply integrated with Discord webhooks and OAuth2.">
    <title><?= e($title ?? 'Dischedule - Discord-Integrated Schedule Platform') ?></title>
    
    <!-- Instant Early Theme Resolver (prevents white flash on dark mode) -->
    <script>
        (function() {
            try {
                var saved = localStorage.getItem('dischedule_theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = (saved && saved !== 'system') ? saved : (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {}
        })();
    </script>
    <style>
        html[data-theme="dark"] { color-scheme: dark; background-color: #0B0F19; }
        html[data-theme="light"] { color-scheme: light; background-color: #F8FAFC; }
    </style>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://i.imgur.com/Yo8WHdg.png">
    <link rel="apple-touch-icon" href="https://i.imgur.com/Yo8WHdg.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Dischedule Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    
    <!-- Core App JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
</head>
