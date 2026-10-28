<div style="max-width:800px;margin:0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">Import & Export Schedules</h1>
            <p style="font-size:13.5px;color:var(--text-muted);">Backup your events, migrate from Google Calendar or Apple iCal, or export to spreadsheets.</p>
        </div>
        <a href="<?= url('events') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Events
        </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
        <!-- Export Card -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-file-export" style="margin-right:8px;color:var(--color-primary);"></i> Export Calendar</span>
            </div>
            <div class="card-body">
                <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:20px;line-height:1.5;">
                    Download all your scheduled events in standard calendar format (.ics) or tabular data (.csv).
                </p>

                <div style="display:flex;flex-direction:column;gap:12px;">
                    <a href="<?= url('events/export/ics') ?>" class="btn btn-primary" style="padding:10px;font-size:13.5px;">
                        <i class="fa-solid fa-calendar-arrow-down"></i> Export as iCalendar (.ics)
                    </a>
                    <a href="<?= url('events/export/csv') ?>" class="btn btn-secondary" style="padding:10px;font-size:13.5px;">
                        <i class="fa-solid fa-file-csv"></i> Export as CSV Spreadsheet
                    </a>
                </div>
            </div>
        </div>

        <!-- Import Card -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-file-import" style="margin-right:8px;color:var(--color-success);"></i> Import Calendar</span>
            </div>
            <div class="card-body">
                <form action="<?= url('events/import') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:16px;line-height:1.5;">
                        Upload an .ics (iCalendar) or .csv file from Google Calendar, Outlook, or Apple Calendar.
                    </p>

                    <div class="form-group">
                        <label class="form-label">Select File (.ics or .csv)</label>
                        <input type="file" name="import_file" class="form-control" accept=".ics,.csv" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;font-size:13.5px;margin-top:8px;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload & Import
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
