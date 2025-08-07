<?php
/* ---------- 1.  TURN ON ERROR REPORTING & LOGGING ---------- */
ini_set('display_errors', isset($_GET['debug']) ? 1 : 0);   // show errors onscreen only when ?debug=1
ini_set('log_errors', 1);
$logFile = __DIR__ . '/../logs/event_timeline.log';
ini_set('error_log', $logFile);
error_reporting(E_ALL);

/* ---------- 2.  APP START ---------- */
require_once __DIR__ . '/../db.php';
session_start();

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    error_log("Access denied – user role: " . ($_SESSION['role'] ?? 'none'));
    die("Access denied");
}

$events = [];
$error  = '';

try {
    $stmt   = $pdo->query("SELECT * FROM EventDetails ORDER BY event_date DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
    error_log($error);
}

/* Optional quick dump when you hit  /event_timeline.php?debug=1 */
if (isset($_GET['debug'])) {
    echo "<pre style='background:#fee;border:2px solid #c00;padding:10px;'>";
    echo "DEBUG DUMP – \$events (" . count($events) . " rows)\n";
    var_dump($events);
    echo "</pre>";
}
?>
<!-- ---------- 3.  PAGE MARKUP ---------- -->
<div class="event-container">
    <h2>Event Timeline</h2>

    <!-- CSV & PDF Download Buttons -->
    <?php if ($events): ?>
        <div class="download-buttons">
            <button id="downloadTimelineCsvBtn" class="download-button">Download CSV</button>
            <button id="downloadTimelinePdfBtn" class="download-button">Download PDF</button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>

    <?php elseif ($events): ?>
        <ul class="timeline-list">
            <?php foreach ($events as $row): ?>
                <li class="timeline-item">
                    <h3><?= htmlspecialchars($row['event_name']) ?></h3>
                    <p><strong>Date:</strong> <?= htmlspecialchars($row['event_date']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                    <p><strong>Required Skills:</strong> <?= htmlspecialchars($row['required_skills']) ?></p>
                    <p><strong>Urgency:</strong> <?= htmlspecialchars($row['urgency_level']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No events found.</p>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
/* …(same CSS as before)… */
.timeline-list{list-style:none;padding:0;margin:0;}
.timeline-item{margin-bottom:30px;padding:20px;border:1px solid #2e7d32;border-radius:8px;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.1);}
.timeline-item h3{margin-bottom:10px;color:#2e7d32;}
.download-button{padding:6px 12px;background:#fff;color:#2e7d32;border:1px solid #2e7d32;border-radius:4px;cursor:pointer;}
.download-button:hover{background:#e8f5e9;}
.download-buttons {
    text-align: center;
    margin-bottom: 10px;
}
</style>

<script>
console.log('[Timeline] script loaded');
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('downloadTimelineCsvBtn');
    if (!btn) {
        console.warn('[Timeline] No download button found – probably no events.');
        return;
    }

    btn.addEventListener('click', () => {
        const items = document.querySelectorAll('.timeline-item');
        console.log('[Timeline] Export clicked –', items.length, 'items found');
        if (!items.length) {
            alert('No events to export.');
            return;
        }

        /* Build CSV rows */
        const rows = [['Event Name','Date','Location','Required Skills','Urgency']];
        items.forEach(li => {
            const clean = txt => txt.replace(/^[^:]+:\s*/,'').trim().replace(/"/g,'""');
            rows.push([
                clean(li.querySelector('h3')?.textContent || ''),
                clean(li.querySelector('p:nth-of-type(1)')?.textContent || ''),
                clean(li.querySelector('p:nth-of-type(2)')?.textContent || ''),
                clean(li.querySelector('p:nth-of-type(3)')?.textContent || ''),
                clean(li.querySelector('p:nth-of-type(4)')?.textContent || '')
            ]);
        });

        const csv = rows.map(r => r.map(c => `"${c}"`).join(',')).join('\r\n');
        const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
        const url  = URL.createObjectURL(blob);
        const a    = Object.assign(document.createElement('a'), {href:url, download:'event_timeline.csv'});
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    });

    const pdfBtn = document.getElementById('downloadTimelinePdfBtn');
    if (pdfBtn) {
        pdfBtn.addEventListener('click', () => {
            const { jsPDF } = window.jspdf || window.jspdf || {};
            if (!jsPDF) {
                alert('jsPDF library is not loaded.');
                return;
            }

            const doc = new jsPDF();
            doc.setFontSize(14);
            doc.text("Event Timeline", 20, 20);

            const items = document.querySelectorAll('.timeline-item');
            let y = 30;

            items.forEach((li, idx) => {
                const title = li.querySelector('h3')?.textContent || '';
                const date = li.querySelector('p:nth-of-type(1)')?.textContent || '';
                const location = li.querySelector('p:nth-of-type(2)')?.textContent || '';
                const skills = li.querySelector('p:nth-of-type(3)')?.textContent || '';
                const urgency = li.querySelector('p:nth-of-type(4)')?.textContent || '';

                const lines = [
                    `Event Name: ${title}`,
                    `${date}`,
                    `${location}`,
                    `${skills}`,
                    `${urgency}`,
                ];

                lines.forEach(line => {
                    doc.text(line, 20, y);
                    y += 8;
                    if (y > 280) {
                        doc.addPage();
                        y = 20;
                    }
                });

                y += 8;
            });

            doc.save("event_timeline.pdf");
        });
    }
});
</script>
