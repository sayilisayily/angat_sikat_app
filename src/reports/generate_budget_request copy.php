<?php
require_once('../../libs/tcpdf/TCPDF-main/tcpdf.php'); // Include the TCPDF library
require_once('../connection.php'); // Include database connection

// Get form data
$organization_name = $_POST['organization_name'] ?? '';
$event_title = $_POST['event_title'] ?? '';
$event_date = $_POST['event_start_date'] ?? '';
$event_id = $_POST['event_id'] ?? ''; // Fetch event_id from form

// Validate required fields
if (empty($organization_name) || empty($event_title) || empty($event_date) || empty($event_id)) {
    http_response_code(400); // Bad Request
    echo "Invalid form data.";
    exit;
}

// Fetch items from the event_items table based on event_id
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
    echo "No items found for the given event ID.";
    exit;
}

// Create a new PDF document
class CustomPDF extends TCPDF {
    // Custom header
    public function Header() {
        $this->SetY(10); // Position from top
        $this->SetFont('centurygothic', '', 11);
        $headerContent = "Republic of the Philippines\n";
        $this->MultiCell(0, 0, $headerContent, 0, 'C', 0, 1, '', '', true);
        $this->SetFont('bookmanoldstyle', 'B', 11);
        $this->MultiCell(0, 0, "CAVITE STATE UNIVERSITY", 0, 'C', 0, 1, '', '', true);
        $this->SetFont('centurygothic', '', 11);
        $headerContent = "CCAT Campus\nRosario, Cavite\n(046) 437-9507 / (046) 437-6659\ncvsurosario@cvsu.edu.ph\nwww.cvsu-rosario.edu.ph";
        $this->MultiCell(0, 0, $headerContent, 0, 'C', 0, 1, '', '', true);
        $this->Ln(2); // Add spacing
    }

    // Custom footer
    public function Footer() {
        $this->SetFont('play', '', 11);
        $this->SetY(-15);
        $footerContent = "SASCO                                                                                                                             Budget Request\nName of Organization                                                                                                           Page {$this->getAliasNumPage()} of {$this->getAliasNbPages()}";
        $this->MultiCell(0, 0, $footerContent, 0, 'L', 0, 1, '', '', true);
    }
}

// Create PDF instance
$pdf = new CustomPDF();
$pdf->SetMargins(25.4, 50, 12.7); // Left: 1 inch, Top: 2 inches, Right: 0.5 inch
$pdf->AddPage();

// Set font for the body
$pdf->SetFont('arial', '', 11);

// Title: Organization Name, Budget Request, and Title of Activity
$pdf->SetFont('arial', 'B', 11);
$pdf->Cell(0, 0, strtoupper($organization_name), 0, 1, 'C', 0, '', 1);
$pdf->Ln(5);
$pdf->Cell(0, 0, "BUDGET REQUEST", 0, 1, 'C', 0, '', 1);
$pdf->Ln(5);
$pdf->Cell(0, 0, strtoupper($event_title), 0, 1, 'C', 0, '', 1);
$pdf->Ln(10);

// Letter Body
$pdf->SetFont('arial', '', 11);
$bodyContent = "Date: " . date("F j, Y", strtotime($event_date)) . "\n\nJIMPLE JAY R. MALIGRO\nCoordinator, SDS\nThis Campus\n\nSir:\n\nGreetings of peace. I am writing this letter to request for budget disbursement allotted for {$event_title} scheduled on " . date("F j, Y", strtotime($event_date)) . ". This budget will be utilized as follows:";
$pdf->MultiCell(0, 0, $bodyContent, 0, 'L', 0, 1, '', '', true);
$pdf->Ln(5);

// Table: Projected Expenses
$pdf->SetFont('arial', 'B', 11);
$pdf->Cell(0, 0, "PROJECTED EXPENSES", 0, 1, 'C', 0, '', 1);
$pdf->Ln(5);

// Table Headers
$pdf->SetFillColor(230, 230, 230); // Light gray
$pdf->Cell(60, 10, "Description", 1, 0, 'C', 1);
$pdf->Cell(30, 10, "Qty", 1, 0, 'C', 1);
$pdf->Cell(30, 10, "Unit Price", 1, 0, 'C', 1);
$pdf->Cell(40, 10, "Total", 1, 1, 'C', 1);

// Table rows
$pdf->SetFont('arial', '', 11);
$totalAmount = 0;
foreach ($items as $item) {
    $description = $item['description'];
    $quantity = $item['quantity'];
    $unitPrice = $item['unit_price'];
    $total = $quantity * $unitPrice;
    $totalAmount += $total;

    $pdf->Cell(60, 10, $description, 1, 0, 'C', 0);
    $pdf->Cell(30, 10, $quantity, 1, 0, 'C', 0);
    $pdf->Cell(30, 10, number_format($unitPrice, 2), 1, 0, 'C', 0);
    $pdf->Cell(40, 10, number_format($total, 2), 1, 1, 'C', 0);
}

// Total Row
$pdf->SetFont('arial', 'B', 11);
$pdf->Cell(120, 10, "TOTAL", 1, 0, 'C', 0);
$pdf->Cell(40, 10, number_format($totalAmount, 2), 1, 1, 'C', 0);

// Additional Letter Content
$pdf->Ln(5);
$footerContent = "Attached to this letter is the resolution for the approval of the budget request pertaining to this activity. Also, rest assured that official receipts will be secured to provide a proper liquidation report. Thank you.";
$pdf->MultiCell(0, 0, $footerContent, 0, 'L', 0, 1, '', '', true);
$pdf->Ln(10);

// Names and Roles Section
$pdf->SetFont('arial', 'B', 11);
$pdf->MultiCell(0, 0, "Prepared by:", 0, 'L', 0, 1);
$pdf->Ln(10);
$pdf->MultiCell(0, 0, "NAME\t\t\t\t\t\tNAME\nTreasurer, Organization\t\t\t\t\tPresident, Organization", 0, 'L', 0, 1);
$pdf->Ln(10);
$pdf->MultiCell(0, 0, "Recommending Approval:", 0, 'L', 0, 1);
$pdf->Ln(10);
$pdf->MultiCell(0, 0, "NAME\t\t\t\t\t\tNAME\nJunior Adviser, Organization\t\t\tSenior Adviser, Organization", 0, 'L', 0, 1);
$pdf->Ln(10);
$pdf->MultiCell(0, 0, "JAMES MATTHEUW S. BELEN\t\tMICHAEL EDWARD T. ARMINTIA, REE\nPresident, CSG\t\t\t\t\tIn-charge, SGOA", 0, 'L', 0, 1);
$pdf->Ln(10);
$pdf->MultiCell(0, 0, "APPROVED:\nJIMPLE JAY R. MALIGRO\nCoordinator, SDS", 0, 'L', 0, 1);

// Output the PDF
$pdf->Output("Budget_Request.pdf", "I"); // Inline view
exit;
?>