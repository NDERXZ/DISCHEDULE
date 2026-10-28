<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Dischedule CSV Export & Import Service
 */
class CSVService
{
    public static function export(array $events): string
    {
        $handle = fopen('php://temp', 'r+');
        
        // Header row
        fputcsv($handle, [
            'ID', 'Title', 'Start Date Time', 'End Date Time', 'Timezone',
            'Location', 'Category', 'Priority', 'Status', 'Visibility',
            'Recurrence Rule', 'Description'
        ]);

        foreach ($events as $e) {
            fputcsv($handle, [
                $e['id'] ?? '',
                $e['title'] ?? '',
                $e['start_datetime'] ?? '',
                $e['end_datetime'] ?? '',
                $e['timezone'] ?? 'UTC',
                $e['location'] ?? '',
                $e['category_name'] ?? '',
                $e['priority'] ?? 'medium',
                $e['status'] ?? 'published',
                $e['visibility'] ?? 'private',
                $e['recurrence_rule'] ?? '',
                $e['description'] ?? ''
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv !== false ? $csv : '';
    }

    public static function import(string $filePath): array
    {
        $events = [];
        if (!file_exists($filePath)) return [];

        $handle = fopen($filePath, 'r');
        if (!$handle) return [];

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return [];
        }

        $headerMap = array_flip(array_map('strtolower', array_map('trim', $headers)));

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) continue;

            $events[] = [
                'title'           => $row[$headerMap['title'] ?? 1] ?? 'Imported Event',
                'start_datetime'  => $row[$headerMap['start date time'] ?? $headerMap['start'] ?? 2] ?? date('Y-m-d H:i:s'),
                'end_datetime'    => $row[$headerMap['end date time'] ?? $headerMap['end'] ?? 3] ?? date('Y-m-d H:i:s', strtotime('+1 hour')),
                'timezone'        => $row[$headerMap['timezone'] ?? 4] ?? 'UTC',
                'location'        => $row[$headerMap['location'] ?? 5] ?? '',
                'category_name'   => $row[$headerMap['category'] ?? 6] ?? '',
                'priority'        => $row[$headerMap['priority'] ?? 7] ?? 'medium',
                'status'          => $row[$headerMap['status'] ?? 8] ?? 'published',
                'visibility'      => $row[$headerMap['visibility'] ?? 9] ?? 'private',
                'recurrence_rule' => $row[$headerMap['recurrence rule'] ?? 10] ?? null,
                'description'     => $row[$headerMap['description'] ?? 11] ?? ''
            ];
        }

        fclose($handle);
        return $events;
    }
}
