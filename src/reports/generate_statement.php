<?php 
require_once('../../libs/tcpdf/TCPDF-main/tcpdf.php');
require_once('../connection.php');
include('../session_check.php');
header('Content-Type: application/json');

try {
// Get form data
$event_id = 31;
$org_query = "SELECT organization_name FROM organizations WHERE organization_id = $organization_id";
                                    $org_result = mysqli_query($conn, $org_query);

                                    if ($org_result && mysqli_num_rows($org_result) > 0) {
                                        $org_row = mysqli_fetch_assoc($org_result);
                                        $organization_name = $org_row['organization_name'];
                                    } else {
                                        $organization_name = "Unknown Organization"; // Fallback if no name is found
                                    }

class CustomPDF extends TCPDF {
    public function Header() {
        $this->SetFont('play', 'I', 10); // Set font to Arial, size 11
        $this->Cell(0, 10, 'SGOA FORM 05', 0, 1, 'R'); // Right-aligned header text
    }

    // Footer Method
    public function Footer() {
        $this->SetY(-25.4); // Position 1 inch from the bottom
        $this->SetFont('play', '', 10); // Set font

        // HTML content for footer with adjusted left and right margins
        $html = '
        <div style="border-top: 1px solid #000; font-size: 10px; font-family: Play, sans-serif; line-height: 1; padding-left: 38.1mm; padding-right: 25.4mm;">
            <div style="width: 100%; text-align: left; margin: 0; padding: 0;">
                SASCO
            </div>
            <div style="width: 100%; text-align: left; margin: 0; padding: 0;">
                Financial Statement
            </div>
            <div style="width: 100%; text-align: left; margin: 0; padding: 0;">
                Name of Organization
            </div>
            <div style="width: 100%; text-align: left; margin: 0; padding: 0;">
                Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages() . '
            </div>
        </div>';

        // Write the HTML footer with the border
        $this->writeHTML($html, true, false, true, false, 'L');
    }
}

$pdf = new CustomPDF();

// Register fonts
$bookmanOldStyle = TCPDF_FONTS::addTTFfont('../../libs/tcpdf/TCPDF-main/fonts/bookman-old-style_j4eZ5/Bookman Old Style/Bookman Old Style Bold/Bookman Old Style Bold.ttf', 'TrueTypeUnicode', '', 96);
$arialBold = TCPDF_FONTS::addTTFfont('../../libs/tcpdf/TCPDF-main/fonts/arial-font/arial_bold13.ttf', 'TrueTypeUnicode', '', 96);
$arial = TCPDF_FONTS::addTTFfont('../../libs/tcpdf/TCPDF-main/fonts/arial-font/arial.ttf', 'TrueTypeUnicode', '', 96);
$centuryGothicBold = TCPDF_FONTS::addTTFfont('../../libs/tcpdf/TCPDF-main/fonts/century-gothic/GOTHICB.TTF""', 'TrueTypeUnicode', '', 96);
$centurygothic = TCPDF_FONTS::addTTFfont('../../libs/tcpdf/TCPDF-main/fonts/century-gothic/Century Gothic.ttf', 'TrueTypeUnicode', '', 96);
$play = TCPDF_FONTS::addTTFfont('../../libs/tcpdf/TCPDF-main/fonts/play/Play-Regular.ttf"', 'TrueTypeUnicode', '', 96);

$pdf->AddPage();
$pdf->SetMargins(25.4, 25.4, 25.4); // Set 1-inch margins (top, left, right)
$pdf->SetAutoPageBreak(true, 31.75); // Set 1.25-inch bottom margin (31.75mm)


// Set left logo using HTML
$htmlLeftLogo = '
    <div style="text-align: left;">
        <img src="img/cvsu.jpg" style="width: 100px; height: 100px; margin-top: 5px;" />
    </div>
';

// Set right logo using HTML
$htmlRightLogo = '
    <div style="text-align: right;">
        <img src="img/bagongpilipinas.jpg" style="width: 100px; height: 100px; margin-top: 5px;" />
    </div>
';

// Add the left logo
$pdf->writeHTMLCell(30, 40, 15, 15, $htmlLeftLogo, 0, 0, false, true, 'L', true);

// Add the right logo
$pdf->writeHTMLCell(30, 40, 165, 15, $htmlRightLogo, 0, 0, false, true, 'R', true);

// Center-align the header text
$pdf->SetFont($centurygothic, 'B', 11);
$pdf->SetY(15); // Adjust Y to align with logos
$pdf->Cell(0, 5, 'Republic of the Philippines', 0, 1, 'C');
$pdf->SetFont('Bookman Old Style', 'B', 11);
$pdf->Cell(0, 5, 'CAVITE STATE UNIVERSITY', 0, 1, 'C');
$pdf->SetFont($centuryGothicBold, 'B', 11);
$pdf->Cell(0, 5, 'CCAT Campus', 0, 1, 'C');
$pdf->SetFont($centurygothic, 'B', 11);
$pdf->Cell(0, 5, 'Rosario, Cavite', 0, 1, 'C');

$pdf->Ln(2);
$pdf->Cell(0, 5, '(046) 437-9507 / (046) 437-6659', 0, 1, 'C');
$pdf->Cell(0, 5, 'cvsurosario@cvsu.edu.ph', 0, 1, 'C');
$pdf->Cell(0, 5, 'www.cvsu-rosario.edu.ph', 0, 1, 'C');

$pdf->Ln(10); // Add space after header

$query = "SELECT title FROM events WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $eventTitle = strtoupper($row['title']); // Convert the title to uppercase
} else {
    $eventTitle = strtoupper("Event Not Found"); // Default title if event is not found
}

// Fetching income rows
$income_query = "SELECT amount, reference FROM income WHERE organization_id = $organization_id AND archived=0";
$income_result = mysqli_query($conn, $income_query);

$inflows = []; // Array to store the fetched income rows
if ($income_result && mysqli_num_rows($income_result) > 0) {
    while ($row = mysqli_fetch_assoc($income_result)) {
        $inflows[] = $row; // Append each row to the inflows array
    }
} else {
    echo "No income records found for the organization.";
}

// Fetching total inflows
$total_query = "SELECT SUM(amount) AS total_inflows FROM income WHERE organization_id = $organization_id AND archived=0";
$total_result = mysqli_query($conn, $total_query);

if ($total_result && mysqli_num_rows($total_result) > 0) {
    $total_row = mysqli_fetch_assoc($total_result);
    $total_inflows = $total_row['total_inflows'];
} else {
    $total_inflows = 0; // Fallback if no income rows exist
}

// Add titles
$pdf->SetFont($arialBold, '', 12);
$pdf->Cell(0, 0, strtoupper($organization_name), 0, 1, 'C', 0, '', 1);
$pdf->Ln(5);
$pdf->Cell(0, 0, "FINANCIAL STATEMENT", 0, 1, 'C', 0, '', 1);
$pdf->Cell(0, 0, "AY 2024-2025", 0, 1, 'C', 0, '', 1);
$pdf->Cell(0, 0, "As of ".date('F d, Y'), 0, 1, 'C', 0, '', 1);
$pdf->Ln(10);

$pdf->SetFont('arial', '', 11);

// Calculate totals
$total_inflows = array_sum(array_column($inflows, 'amount'));

// Table 1: Cash Inflows
$html1 = '
<table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:left;">
    <tr>
        <th>Cash Inflows</th>
        <th>Amount</th>
        <th>Reference</th>
    </tr>';

foreach ($inflows as $inflow) {
    $html1 .= '
    <tr>
        <td>Income</td>
        <td>' . number_format($inflow['amount'], 2) . '</td>
        <td>' . htmlspecialchars($inflow['reference']) . '</td>
    </tr>';
}
$html1 .= '
    <tr>
        <td><b>Total Inflows</b></td>
        <td colspan="2">' . number_format($total_inflows, 2) . '</td>
    </tr>
</table>';

// Fetch outflows grouped by category
$query = "
    SELECT 
        category,
        SUM(amount) AS subtotal,
        GROUP_CONCAT(CONCAT(description, ' (', reference, ')') SEPARATOR ', ') AS details
    FROM expenses
    WHERE organization_id = $organization_id
    GROUP BY category
";

// Execute the query
$result = mysqli_query($conn, $query);

// Initialize arrays for subtotals and total outflows
$outflows = [];
$total_outflows = 0;

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $outflows[] = [
            'category' => $row['category'],
            'subtotal' => $row['subtotal'],
            'details' => $row['details'],
        ];
        $total_outflows += $row['subtotal'];
    }
}

$html2 = '
<table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:left;">
    <tr>
        <th>Cash Outflows</th>
        <th>Amount</th>
        <th>Reference</th>
    </tr>';

foreach ($outflows as $outflow) {
    $html2 .= '
    <tr>
        <td>' . htmlspecialchars($outflow['category']) . '</td>
        <td>' . number_format($outflow['subtotal'], 2) . '</td>
        <td>' . htmlspecialchars($outflow['details']) . '</td>
    </tr>';
}

$html2 .= '
    <tr>
        <td><b>TOTAL OUTFLOWS</b></td>
        <td colspan="2">' . number_format($total_outflows, 2) . '</td>
    </tr>
</table>';

// Fetch Cash Balance Beginning, Cash on Bank, and Cash on Hand from organizations table
$organization_query = "
    SELECT 
        beginning_balance, 
        cash_on_bank, 
        cash_on_hand 
    FROM organizations 
    WHERE organization_id = $organization_id
";
$organization_result = mysqli_query($conn, $organization_query);

if ($organization_result && mysqli_num_rows($organization_result) > 0) {
    $organization_row = mysqli_fetch_assoc($organization_result);
    $beginning_balance = $organization_row['beginning_balance'];
    $cash_on_bank = $organization_row['cash_on_bank'];
    $cash_on_hand = $organization_row['cash_on_hand'];
} else {
    $beginning_balance = 0;
    $cash_on_bank = 0;
    $cash_on_hand = 0;
}

// Fetch the latest Cash Balance End from balance_history table
$balance_query = "
    SELECT 
        cash_balance_end 
    FROM balance_history 
    WHERE organization_id = $organization_id 
    ORDER BY balance_date DESC 
    LIMIT 1
";
$balance_result = mysqli_query($conn, $balance_query);

if ($balance_result && mysqli_num_rows($balance_result) > 0) {
    $balance_row = mysqli_fetch_assoc($balance_result);
    $cash_balance_end = $balance_row['cash_balance_end'];
} else {
    $cash_balance_end = 0; // Fallback if no record is found
}

// Table 3: Cash Balance
$html3 = '
<table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:left;">
    <tr>
        <td><b>Cash Balance Beginning (from previous term)</b></td>
        <td>' . number_format($beginning_balance, 2) . '</td>
    </tr>
    <tr>
        <td><b>TOTAL INFLOWS</b></td>
        <td>' . number_format($total_inflows, 2) . '</td>
    </tr>
    <tr>
        <td><b>Total Outflows</b></td>
        <td>' . number_format($total_outflows, 2) . '</td>
    </tr>
    <tr>
        <td><b>Accounted as follows:</b></td>
        <td></td>
    </tr>
    <tr>
        <td>Cash on Bank</td>
        <td>' . number_format($cash_on_bank, 2) . '</td>
    </tr>
    <tr>
        <td>Cash on Hand</td>
        <td>' . number_format($cash_on_hand, 2) . '</td>
    </tr>
    <tr>
        <td><b>Cash Balance End</b></td>
        <td>' . number_format($cash_balance_end, 2) . '</td>
    </tr>
</table>';

// Write HTML to PDF
$pdf->writeHTML($html1, true, false, true, false, '');
$pdf->writeHTML($html2, true, false, true, false, '');
$pdf->writeHTML($html3, true, false, true, false, '');

// Add final spacing
$pdf->Ln(10);
 // Space for signatures above names

// Prepared By Section
$pdf->SetFont($arial, '', 11); 
$pdf->Cell(0, 0, "Prepared by:", 0, 1, 'L', 0, '', 1);
$pdf->Ln(10); // Space for signatures above names

$pdf->SetFont($arialBold, '', 11); 
$pdf->Cell(80, 10, "NAME", 0, 0, 'L', 0); // Name position after space
$pdf->Cell(80, 10, "NAME", 0, 1, 'L', 0);
$pdf->SetFont($arial, 'B', 11);
$pdf->Cell(80, 10, "Treasurer, Organization", 0, 0, 'L', 0);
$pdf->Cell(80, 10, "President, Organization", 0, 1, 'L', 0);
$pdf->Ln(10); // Space between sections

// Recommending Approval Section
$pdf->SetFont($arial, '', 11);
$pdf->Cell(0, 0, "Recommending Approval:", 0, 1, 'L', 0, '', 1);
$pdf->Ln(10); // Space for signatures above names

// Recommending Approval Table
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(80, 10, "NAME", 0, 0, 'L', 0); // Name position after space
$pdf->Cell(80, 10, "NAME", 0, 1, 'L', 0);
$pdf->SetFont($arial, 'B', 11);
$pdf->Cell(80, 10, "Junior Adviser, Organization", 0, 0, 'L', 0);
$pdf->Cell(80, 10, "Senior Adviser, Organization", 0, 1, 'L', 0);
$pdf->Ln(10); // Space between sections

// Example Names for Signatures
$pdf->SetFont($arialBold, '', 11);
$pdf->Ln(10); // Space for signatures above names
$pdf->Cell(80, 10, "JAMES MATHEW S. BELEN", 0, 0, 'L', 0);
$pdf->Cell(80, 10, "MICHAEL EDWARD T. ARMINTIA, REE", 0, 1, 'L', 0);
$pdf->SetFont($arial, 'B', 11);
$pdf->Cell(80, 10, "President, CSG", 0, 0, 'L', 0);
$pdf->Cell(80, 10, "In-charge, SGOA", 0, 1, 'L', 0);
$pdf->Ln(10); // Final spacing


// Approved Section
$pdf->SetFont($arial, 'B', 11); // Arial and Bold for the "Approved" title
$pdf->Cell(0, 0, "APPROVED:", 0, 1, 'C', 0, '', 1);
$pdf->Ln(10);
$pdf->SetFont($arialBold, 'B', 11);
$pdf->Cell(0, 0, "JIMPLE JAY R. MALIGRO", 0, 1, 'C', 0, '', 1);
$pdf->SetFont($arial, 'B', 11);
$pdf->Cell(0, 0, "Coordinator, SDS", 0, 1, 'C', 0, '', 1);

// Generate the file name
    $file_name = "Permit_to_Withdraw_" . $eventTitle . '_' . time() . ".pdf";

    // Use the 'D' parameter to force download
    $pdf->Output($file_name, 'I'); 

    // Exit to ensure no extra output
    exit;
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        "success" => false,
        "errors" => [$e->getMessage()],
    ]);
}
?>
