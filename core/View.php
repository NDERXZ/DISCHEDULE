<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Template & View Engine
 */
class View
{
    private static string $viewsPath = '';
    private static array $sharedData = [];

    public static function init(?string $viewsPath = null): void
    {
        self::$viewsPath = $viewsPath ?? (dirname(__DIR__) . '/app/Views');
    }

    public static function share(string $key, mixed $value): void
    {
        self::$sharedData[$key] = $value;
    }

    public static function render(string $template, array $data = [], ?string $layout = 'main'): string
    {
        if (empty(self::$viewsPath)) {
            self::init();
        }

        $templateFile = self::$viewsPath . '/' . str_replace('.', '/', $template) . '.php';
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View template not found: {$templateFile}");
        }

        // Merge shared data with view data
        $mergedData = array_merge(self::$sharedData, [
            'appName'     => $_ENV['APP_NAME'] ?? 'Dischedule',
            'appUrl'      => $_ENV['APP_URL'] ?? 'http://localhost:8000',
            'currentUser' => Session::getUser(),
            'csrfToken'   => Session::getCsrfToken(),
            'flashes'     => Session::getFlashes(),
            'currentLang' => I18n::getLocale()
        ], $data);

        // Render the inner view template
        extract($mergedData, EXTR_SKIP);
        ob_start();
        require $templateFile;
        $content = ob_get_clean();

        // If layout specified, render inside layout
        if ($layout !== null && $layout !== '') {
            $layoutFile = self::$viewsPath . '/layouts/' . $layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$layoutFile}");
            }

            extract(array_merge($mergedData, ['content' => $content]), EXTR_SKIP);
            ob_start();
            require $layoutFile;
            return ob_get_clean();
        }

        return $content;
    }

    public static function partial(string $partial, array $data = []): string
    {
        if (empty(self::$viewsPath)) {
            self::init();
        }

        $partialFile = self::$viewsPath . '/partials/' . str_replace('.', '/', $partial) . '.php';
        if (!file_exists($partialFile)) {
            return "<!-- Partial not found: {$partial} -->";
        }

        $mergedData = array_merge(self::$sharedData, [
            'currentUser' => Session::getUser(),
            'csrfToken'   => Session::getCsrfToken()
        ], $data);

        extract($mergedData, EXTR_SKIP);
        ob_start();
        require $partialFile;
        return ob_get_clean();
    }

    public static function escape(?string $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
