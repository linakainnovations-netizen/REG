<?php
/**
 * Sample PDF Preview Script
 * Generates a high-quality official notice to preview the new branding.
 */

// Include the generator
require_once __DIR__ . '/../backend/pdf_generator.php';

echo "--- St. Paul's Parish PDF Preview System ---\n";
echo "Generating Sample High-Quality Official Notice...\n";

$title = "Official Parish Announcement: New Portal Branding";
$content = "
<p>Dear Parishioners,</p>
<p>We are pleased to unveil the new official documentation layout for St. Paul's Parish Chipata. This design is part of our ongoing effort to digitize and professionalize our community communications. The portal will now automatically include our official logo, full address, and contact details on all generated documents, including:</p>
<ul>
    <li>Thanksgiving and Donation Lists</li>
    <li>Liturgy and Service Rosters</li>
    <li>Parish Executive Committee Notices</li>
    <li>Financial and Procurement Reports</li>
</ul>
<p>We believe this new look reflects the transparency and commitment of our community to excellence through faith and technology.</p>
<p>One Faith, One People, One Portal.</p>
";

try {
    $pdfPath = PDFGenerator::createOfficialNotice($title, $content);
    echo "SUCCESS: PDF Generated Successfully!\n";
    echo "Location: " . $pdfPath . "\n";
    echo "Full Physical Path: C:\\xampp\\htdocs\\St._Paul_Chipata_Portal\\" . str_replace('/', '\\', $pdfPath) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
