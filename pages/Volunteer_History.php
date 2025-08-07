<?php
session_start();
if (($_SESSION['role'] ?? '') !== 'volunteer') {
    header('Location: /pages/Login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/global.css" />
    <meta charset="UTF-8">
    <title>Volunteer Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9; }
        header { background-color: #2e7d32; padding: 0.75rem 2rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-top: 2rem; color: #333; }
        table { width: 80%; margin: 2rem auto; border-collapse: collapse; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 1rem; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:hover { background-color: #f9f9f9; }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .btn-container button {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border: 1px solid #2e7d32;
            background: white;
            color: #2e7d32;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-container button:hover {
            background: #2e7d32;
            color: white;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
            onload="window.jspdf = window.jspdf || window.jspdf_umd;"></script>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <h1 id="history">Your Volunteer History</h1>

    <table id="volunteerTable">
        <thead>
            <tr>
                <th>Event Date</th>
                <th>Event Name</th>
                <th>Description</th>
                <th>Location</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div class="btn-container">
        <button id="downloadPdfBtn">Download PDF</button>
        <button id="downloadCsvBtn">Download CSV</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const tableBody = document.querySelector('#volunteerTable tbody');

        async function fetchAndRenderVolunteerHistory() {
            tableBody.innerHTML = `<tr><td colspan="5">Loading your history...</td></tr>`;
            try {
                const response = await fetch('/backend/auth/get_volunteer_history.php');
                if (!response.ok) {
                    const err = await response.json();
                    throw new Error(err.error || `HTTP ${response.status}`);
                }
                const volunteerHistory = await response.json();
                tableBody.innerHTML = '';
                if (!volunteerHistory.length) {
                    tableBody.innerHTML = `<tr><td colspan="5">No volunteer history found.</td></tr>`;
                    return;
                }
                volunteerHistory.forEach(r => {
                    const row = tableBody.insertRow();
                    row.innerHTML = `
                        <td>${r.event_date}</td>
                        <td>${r.event_name}</td>
                        <td>${r.description}</td>
                        <td>${r.location}</td>
                        <td>${r.status}</td>
                    `;
                });
            } catch (e) {
                console.error(e);
                tableBody.innerHTML = `<tr><td colspan="5">Failed to load history: ${e.message}</td></tr>`;
            }
        }

        fetchAndRenderVolunteerHistory();
    });

    document.getElementById('downloadPdfBtn').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const rows = document.querySelectorAll('#volunteerTable tbody tr');

        doc.setFontSize(16);
        doc.text("Volunteer History", 20, 20);
        let y = 30;

        rows.forEach(r => {
            const cells = r.querySelectorAll('td');
            let x = 20;
            cells.forEach(c => {
                doc.setFontSize(10);
                doc.text(c.textContent || '', x, y);
                x += 40;
            });
            y += 10;
        });

        doc.save('Volunteer_History.pdf');
    });

    document.getElementById('downloadCsvBtn').addEventListener('click', async () => {
        try {
            const res = await fetch('/backend/auth/get_volunteer_history.php');
            if (!res.ok) throw new Error('Fetch failed');
            const data = await res.json();
            if (!data.length) {
                alert('No data to export.');
                return;
            }

            const headers = ['Event Date','Event Name','Description','Location','Status'];
            const lines = [headers.join(',')];

            data.forEach(r => {
                const row = [
                    r.event_date, r.event_name, r.description,
                    r.location, r.status
                ].map(f => `"${String(f).replace(/"/g,'""')}"`);
                lines.push(row.join(','));
            });

            const blob = new Blob([lines.join('\r\n')], { type: 'text/csv' });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');

            a.href = url;
            a.download = 'Volunteer_History.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        } catch (err) {
            console.error(err);
            alert('CSV export failed.');
        }
    });
    </script>
</body>
</html>
