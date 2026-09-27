<?php
/**
 * PDF Generation Module
 * Integrates with Dompdf to generate Parish documents (Duty lists, Thanksgiving, etc.)
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFGenerator {
    /**
     * Render HTML to PDF and stream it straight to the browser as a
     * download. NOTHING is saved on the server.
     */
    public static function stream($html, $filename) {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        while (ob_get_level() > 0) ob_end_clean();
        $dompdf->stream(preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename), ['Attachment' => true]);
        exit;
    }

    /**
     * Roster table PDF (duties list) — streamed, never stored.
     */
    public static function streamRoster($tasks, $title = 'Parish Duty Roster') {
        $header = self::getBrandedHeaderContent();
        $rows = '';
        $i = 1;
        foreach ($tasks as $t) {
            $rows .= "<tr><td>" . ($i++) . "</td><td>" . htmlspecialchars($t['assigned_date'] ?? '') . "</td><td>" . htmlspecialchars($t['task_name'] ?? '') . "</td><td>" . htmlspecialchars($t['group_name'] ?? $t['group_id'] ?? '—') . "</td><td>" . htmlspecialchars($t['description'] ?? '') . "</td></tr>";
        }
        if ($rows === '') $rows = "<tr><td colspan='5' style='text-align:center;'>No duties scheduled.</td></tr>";
        $html = "
        <style>
            body { font-family: 'Helvetica', sans-serif; padding: 10px; color: #1f2937; }
            h2 { color: #1e3a8a; text-align: center; margin-top: 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 10pt; }
            th { background: #f3f4f6; color: #1e3a8a; }
            .footer { margin-top: 40px; font-size: 9pt; text-align: center; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        </style>
        $header
        <h2>" . htmlspecialchars($title) . "</h2>
        <p style='text-align:center;'>Generated " . date('d M Y') . "</p>
        <table><thead><tr><th>No.</th><th>Date</th><th>Duty</th><th>Group</th><th>Notes</th></tr></thead><tbody>$rows</tbody></table>
        <div class='footer'>Official document of St. Charles Lwanga Regiment Parish. One Faith, One Portal.</div>";
        self::stream($html, str_replace(' ', '_', $title) . '_' . date('Ymd') . '.pdf');
    }

    /**
     * Generate a PDF from HTML and save it (legacy — prefer stream()).
     */
    public static function generate($html, $filename, $saveDir = 'dashboard/assets/generated_pdfs/') {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $fullPath = __DIR__ . '/../' . $saveDir . $filename;
        
        // Ensure directory exists
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        file_put_contents($fullPath, $output);
        return $saveDir . $filename;
    }

    /**
     * Helper to get the base64 encoded logo and official header HTML
     */
    private static function getBrandedHeaderContent() {
        $logoPath = __DIR__ . '/../assets/images/other/saint_logo.png';
        $base64Logo = "";
        
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        return "
        <div style='text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 25px; position: relative;'>
            <table style='width: 100%; border: none;'>
                <tr>
                    <td style='width: 80px; vertical-align: middle; border: none;'>
                        " . ($base64Logo ? "<img src='$base64Logo' style='height: 80px; width: auto;'>" : "") . "
                    </td>
                    <td style='text-align: center; border: none;'>
                        <h1 style='margin: 0; color: #1e3a8a; font-size: 24pt; text-transform: uppercase;'>ST. CHARLES LWANGA PARISH</h1>
                        <p style='margin: 5px 0; font-weight: bold; font-size: 11pt;'>CHITUKUKO ROAD, REGIMENT, LUSAKA, ZAMBIA</p>
                        <p style='margin: 2px 0; font-size: 9pt; color: #4b5563;'>
                            P/B RW 174X | Email: stcharleslwangaregiment@gmail.com
                        </p>
                        <p style='margin: 2px 0; font-size: 9pt; color: #4b5563;'>
                            Tel/Phone: 0975255734
                        </p>
                    </td>
                    <td style='width: 80px; border: none;'></td> <!-- Balancing space -->
                </tr>
            </table>
        </div>";
    }

    /**
     * Template for Thanksgiving List (Goods to be brought)
     */
    public static function createThanksgivingList($items, $groupName) {
        $header = self::getBrandedHeaderContent();
        $html = "
        <style>
            body { font-family: 'Helvetica', sans-serif; padding: 10px; color: #1f2937; }
            h2 { color: #1e3a8a; text-align: center; margin-top: 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; }
            th { background: #f3f4f6; color: #1e3a8a; font-size: 11pt; }
            td { font-size: 10pt; }
            .footer { margin-top: 50px; font-size: 9pt; text-align: center; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 10px; }
            .signature { margin-top: 60px; display: flex; justify-content: space-between; }
        </style>
        $header
        <h2>Thanksgiving Items List</h2>
        <p style='text-align:center;'>Group: <strong>$groupName</strong> | Date: " . date('d M Y') . "</p>
        
        <table>
            <thead>
                <tr>
                    <th style='width: 10%;'>No.</th>
                    <th style='width: 60%;'>Required Good/Item</th>
                    <th style='width: 30%;'>Quantity</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($items as $index => $item) {
            $html .= "
                <tr>
                    <td>" . ($index + 1) . "</td>
                    <td>" . $item['name'] . "</td>
                    <td>" . $item['qty'] . "</td>
                </tr>";
        }

        $html .= "
            </tbody>
        </table>
        
        <div style='margin-top: 40px; margin-left: 20px;'>
            <p>__________________________</p>
            <p style='font-size: 9pt;'>Parish Treasurer / Secretary</p>
        </div>

        <div class='footer'>
            This is an official document of St. Charles Lwanga Regiment Parish. One Faith, One Portal.
        </div>";

        return self::generate($html, "Thanksgiving_" . str_replace(' ', '_', $groupName) . "_" . date('Ymd') . ".pdf");
    }

    /**
     * Create a High-Quality Official Notice/Document
     */
    public static function createOfficialNotice($title, $content, $reference = "") {
        $header = self::getBrandedHeaderContent();
        $html = "
        <style>
            body { font-family: 'Helvetica', sans-serif; padding: 20px; line-height: 1.6; color: #1f2937; }
            .title { text-align: center; text-decoration: underline; font-size: 16pt; font-weight: bold; margin-bottom: 30px; color: #111827; }
            .content { font-size: 12pt; text-align: justify; margin-bottom: 40px; }
            .ref { font-size: 10pt; color: #6b7280; margin-bottom: 20px; }
            .footer { margin-top: 100px; font-size: 9pt; text-align: center; color: #9ca3af; }
            .stamp { width: 150px; height: 150px; border: 2px dashed #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fecaca; font-size: 8pt; text-transform: uppercase; margin: 20px auto; }
        </style>
        $header
        <div class='ref'>REF: " . ($reference ?: "SPP/GEN/" . date('Y/m/d')) . "</div>
        <div class='title'>$title</div>
        <div class='content'>$content</div>
        
        <div style='margin-top: 50px;'>
            <p>Sincerely,</p>
            <br><br>
            <p><strong>Parish Executive Committee</strong></p>
            <p>St. Charles Lwanga Regiment Parish</p>
        </div>

        <div class='footer'>
            &copy; " . date('Y') . " St. Charles Lwanga Parish Portal. All Rights Reserved.
        </div>";

        return self::generate($html, "Official_Notice_" . date('Ymd_His') . ".pdf");
    }
}
?>
