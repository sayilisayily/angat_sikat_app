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
        $this->SetFont('arial', 'I', 10); // Set font to Arial, size 11
        $this->Cell(0, 10, 'SGOA FORM 10', 0, 1, 'R'); // Right-aligned header text
    }

    // Footer Method
    public function Footer() {
        $this->SetY(-30.48); // Position 1.2 inches from the bottom
        $this->SetFont('arial', '', 10); // Set font to Arial

        // First line: SASCO and Budget Request
        $this->Cell(0, 10, 'SASCO', 0, 0, 'L');
        $this->Cell(0, 10, 'Budget Request', 0, 1, 'R');
        
        // Second line: Organization Name and Page Number
        $this->Cell(0, 10, 'Name of Organization', 0, 0, 'L');
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



// Query to fetch the event title and start date
$query = "SELECT title, event_start_date FROM events WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $eventTitle = ($row['title']); // Convert the title to uppercase
    $eventStartDate = date("F j, Y", strtotime($row['event_start_date'])); // Format the start date
} else {
    $eventTitle = strtoupper("Event Not Found"); // Default title if event is not found
    $eventStartDate = "N/A"; // Default date if event is not found
}


$stmt->close();

// Add titles
$pdf->SetFont($arialBold, '', 12);
$pdf->Cell(0, 0, strtoupper($organization_name), 0, 1, 'C', 0, '', 1);
$pdf->Ln(5);
$pdf->Cell(0, 0, "BUDGET REQUEST", 0, 1, 'C', 0, '', 1);
$pdf->Cell(0, 0, $eventTitle, 0, 1, 'C', 0, '', 1);
$pdf->Ln(10);

// Add letter body
// Bold font for date and name
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 0, date("F j, Y"), 0, 1, 'L', 0, '', 1);
$pdf->Cell(0, 0, 'JIMPLE JAY R. MALIGRO', 0, 1, 'L', 0, '', 1);

// Normal font for the rest
$pdf->SetFont($arial, '', 11);
$pdf->MultiCell(0, 0, "Coordinator, SDS\nThis Campus\n\nSir:\n\nGreetings of peace. I am writing this letter to request for budget disbursement allotted for ". $eventTitle ." scheduled on " . $eventStartDate . ". This budget will be utilized as follows:", 0, 'L', 0, 1, '', '', true);
$pdf->Ln(5);


// Add table title spanning all columns
$pdf->SetFont($arialBold, '', 11);
$pdf->SetFillColor(230, 230, 230); // Optional: Highlight the title background
$pdf->Cell(160, 10, "PROJECTED EXPENSES", 1, 1, 'L'); // Spans all columns (60 + 30 + 30 + 40 = 160)

// Add table header
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(50, 10, "Description", 1, 0, 'L');
$pdf->Cell(25, 10, "QTY", 1, 0, 'C');
$pdf->Cell(25, 10, "UNIT PRICE", 1, 0, 'C');
$pdf->Cell(30, 10, "TOTAL", 1, 0, 'C');
$pdf->Cell(30, 10, "", 1, 1, 'C');  



$query = "SELECT description, quantity, amount AS unit_price FROM event_items WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
} else {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'No items found for the given event ID.']);
    exit;
}
// Table rows
$pdf->SetFont($arial, '', 11);
$totalAmount = 0;

foreach ($items as $item) {
    $description = $item['description'];
    $quantity = $item['quantity'];
    $unitPrice = $item['unit_price'];
    $total = $quantity * $unitPrice;
    $totalAmount += $total;

    $pdf->Cell(50, 10, $description, 1, 0, 'C', 0);
    $pdf->Cell(25, 10, $quantity, 1, 0, 'C', 0);
    $pdf->Cell(25, 10, number_format($unitPrice), 1, 0, 'C', 0);
    $pdf->Cell(30, 10, number_format($total), 1, 0, 'C', 0);
    $pdf->Cell(30, 10, "", 1, 1, 'C'); 
}

// Total row
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(130, 10, "TOTAL", 1, 0, 'L', 0);
$pdf->Cell(30, 10, number_format($totalAmount), 1, 1, 'C', 0);
$pdf->Ln(10);

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

$pdfOutputPath = 'generated_pdfs/' . $eventTitle . '_budget_request.pdf';
$pdf->Output();
?>
