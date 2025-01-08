<?php 
require_once('../../libs/tcpdf/TCPDF-main/tcpdf.php');
require_once('../connection.php');
include('../session_check.php');

// Get form data
$event_id = 30;
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
        $this->Cell(0, 10, 'SGOA FORM 11', 0, 1, 'R'); // Right-aligned header text
    }

    // Footer Method
    public function Footer() {
        global $organization_name; 
        $this->SetY(-30.48); // Position 1.2 inches from the bottom
        $this->SetFont('play', '', 10); // Set font to Arial

        // First line: SASCO and Budget Request
        $this->Cell(0, 10, 'SASCO', 0, 0, 'L');
        $this->Cell(0, 10, 'Liquidation Report', 0, 1, 'R');
        
        // Second line: Organization Name and Page Number
        $this->Cell(0, 10, $organization_name, 0, 0, 'L');
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'R');
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
$pdf->SetMargins(25.4, 25.4, 25.4); // 1-inch margins (25.4mm)
$pdf->SetAutoPageBreak(true, 30.48); // 1.2-inch bottom margin

$pdf->SetFont($centurygothic, 'B', 11);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 1);
$pdf->Cell(0, 0, 'Republic of the Philippines', 0, 1, 'C', 0, '', 1);

$pdf->SetFont('Bookman Old Style', 'B', 11);
$pdf->Cell(0, 0, 'CAVITE STATE UNIVERSITY', 0, 1, 'C', 0, '', 1);

$pdf->SetFont($centuryGothicBold, 'B', 11);
$pdf->Cell(0, 0, 'CCAT Campus', 0, 1, 'C', 0, '', 1);

$pdf->SetFont($centurygothic, 'B', 11);
$pdf->Cell(0, 0, 'Rosario, Cavite', 0, 1, 'C', 0, '', 1);

$pdf->Ln(2);
$pdf->Cell(0, 0, '(046) 437-9507 / (046) 437-6659', 0, 1, 'C', 0, '', 1);
$pdf->Cell(0, 0, 'cvsurosario@cvsu.edu.ph', 0, 1, 'C', 0, '', 1);
$pdf->Cell(0, 0, 'www.cvsu-rosario.edu.ph', 0, 1, 'C', 0, '', 1);

// Now add the logos using HTML for images
$html = '
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div style="flex: 1; text-align: left;">
            <img src="img/cvsu.jpg" width="40" height="40" />
        </div>
        <div style="flex: 1; text-align: right;">
            <img src="img/cvsu.jpg" width="40" height="40" />
        </div>
    </div>
';

// Write the HTML (images only)
//$pdf->writeHTML($html, true, false, true, false, 'C');
$pdf->Ln(10); // Add space after header

// Query to fetch the event title
$query = "SELECT title FROM events WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $eventTitle = ($row['title']); // Convert the title to uppercase
} else {
    $eventTitle = strtoupper("Event Not Found"); // Default title if event is not found
}
$cashReceived = 10500; // Amount received in numeric format
$disbursements = [
    ['description' => 'Sound System', 'note' => '1', 'amount' => 5000, 'details' => [
        ['date' => '01-02-25', 'description' => 'Down payment for sound system', 'qty' => 1, 'unit_price' => 2500, 'total_amount' => 2500, 'reference' => 'XYZ Studio receipt'],
        ['date' => '01-06-25', 'description' => 'Full payment for sound system', 'qty' => 1, 'unit_price' => 2500, 'total_amount' => 2500, 'reference' => 'XYZ Studio receipt']
    ]],
    ['description' => 'Food Allowance', 'note' => '2', 'amount' => 1950, 'details' => [
        ['date' => '01-02-25', 'description' => 'Down payment for food allowance', 'qty' => 2, 'unit_price' => 975, 'total_amount' => 1950, 'reference' => 'XYZ Catering receipt']
    ]]
];
$totalDisbursement = 6950;
$amountToBeRemitted = 3050;

// Function to convert number to words
function convertNumberToWord($number) {
    // Implement the conversion function here (same as previously discussed)
    $number = str_replace(array(',', ' '), '', trim($number));
    if (! $number) {
        return false;
    }
    $number = (int) $number;
    $words = array();
    $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
    );
    $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
    $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
        'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
        'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
    );
    $number_length = strlen($number);
    $levels = (int) (($number_length + 2) / 3);
    $max_length = $levels * 3;
    $number = substr('00' . $number, -$max_length);
    $number_levels = str_split($number, 3);
    for ($i = 0; $i < count($number_levels); $i++) {
        $levels--;
        $hundreds = (int) ($number_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
        $tens = (int) ($number_levels[$i] % 100);
        $singles = '';
        if ($tens < 20) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($number_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . (($levels && (int) ($number_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
    }
    return strtoupper(implode(' ', $words));
}


// Set font
$pdf->SetFont('arial', '', 11);
$html = '
    <table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:left;">
        <tr>
            <th colspan="2" style="text-align:center;">Title of Activity</th>
            <td colspan="2" style="text-align:center;">' . $eventTitle . '</td>
        </tr>
        <tr>
            <th colspan="2" style="text-align:center;">Cash Received</th>
            <td colspan="2" style="text-align:center;">' . convertNumberToWord($cashReceived) . ' PESOS (P ' . number_format($cashReceived, 2) . ')</td>
        </tr>
        <tr>
            <th colspan="4" style="text-align:left;">Less Disbursement:</th>
        </tr>
        <tr>
            <th style="width: 50%;">Description</th>
            <th style="width: 25%;">Note</th>
            <th style="width: 25%;">Amount (P)</th>
        </tr>';

foreach ($disbursements as $disbursement) {
    $html .= '
        <tr>
            <td>' . $disbursement['description'] . '</td>
            <td>' . $disbursement['note'] . '</td>
            <td>' . number_format($disbursement['amount'], 2) . '</td>
        </tr>';
}

// Add total disbursement and amount to be remitted
$html .= '
        <tr>
            <td><b>Total Disbursement</b></td>
            <td colspan="2">' . number_format($totalDisbursement, 2) . '</td>
        </tr>
        <tr>
            <td><b>Amount to be Remitted</b></td>
            <td colspan="2">' . number_format($amountToBeRemitted, 2) . '</td>
        </tr>
    </table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Ln(10); // Space for signatures above names

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

// Add new page for detailed disbursements
$pdf->AddPage();
$pdf->SetFont('arial', '', 11);

// Loop through disbursements to create detailed tables for each
foreach ($disbursements as $disbursement) {
    $html2 = '
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:left;">
            <tr>
                <th colspan="6" style="text-align:left;">Note ' . $disbursement['note'] . ': ' . $disbursement['description'] . '</th>
            </tr>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
                <th>Reference</th>
            </tr>';
    
    // Add detail rows (sample static data)
    $details = [
        ['date' => '01-02-25', 'description' => 'Down payment for sound system', 'qty' => 1, 'unit_price' => 2500, 'total_amount' => 2500, 'reference' => 'XYZ Studio receipt'],
        ['date' => '01-06-25', 'description' => 'Full payment for sound system', 'qty' => 1, 'unit_price' => 2500, 'total_amount' => 2500, 'reference' => 'XYZ Studio receipt'],
    ];
    
    $totalAmountForDisbursement = 0;
    foreach ($details as $detail) {
        $html2 .= '
            <tr>
                <td>' . $detail['date'] . '</td>
                <td>' . $detail['description'] . '</td>
                <td>' . $detail['qty'] . '</td>
                <td>' . number_format($detail['unit_price'], 2) . '</td>
                <td>' . number_format($detail['total_amount'], 2) . '</td>
                <td>' . $detail['reference'] . '</td>
            </tr>';
        $totalAmountForDisbursement += $detail['total_amount'];
    }
    
    // Add total row for this disbursement
    $html2 .= '
        <tr>
            <td colspan="4"><b>TOTAL:</b></td>
            <td colspan="2">' . number_format($totalAmountForDisbursement, 2) . '</td>
        </tr>
    </table>';

    $pdf->writeHTML($html2, true, false, true, false, '');
}

$pdfOutputPath = 'generated_pdfs/' . $eventTitle . '_liquidation.pdf';
$pdf->Output();
?>
