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
        $this->Cell(0, 10, 'SGOA FORM 08', 0, 1, 'R'); // Right-aligned header text
    }

    // Footer Method
    public function Footer() {
        $this->SetY(-30.48); // Position 1.2 inches from the bottom
        $this->SetFont('arial', '', 10); // Set font to Arial

        // First line: SASCO and Budget Request
        $this->Cell(0, 10, 'SASCO', 0, 0, 'L');
        $this->Cell(0, 10, 'Proposal', 0, 1, 'R');
        
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
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 0, strtoupper($organization_name), 0, 1, 'C', 0, '', 1);
$pdf->Ln(5);
$pdf->Cell(0, 0, "PROJECT PROPOSAL", 0, 1, 'C', 0, '', 1);
$pdf->Cell(0, 0, $eventTitle, 0, 1, 'C', 0, '', 1);
$pdf->Ln(10);

// Add body
$left_width = 80;  // Width for the left column
$right_width = 120; // Width for the right column
$row_height = 8;   // Height for each row

// I. TITLE
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell($left_width, $row_height, 'I. TITLE:', 0, 0, 'L'); // Left cell
$pdf->SetFont('arial', '', 11);
$pdf->Cell($right_width, $row_height, $eventTitle, 0, 1, 'L'); // Right cell

// II. PROPONENT
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell($left_width, $row_height, 'II. PROPONENT:', 0, 0, 'L');
$pdf->SetFont('arial', '', 11);
$pdf->Cell($right_width, $row_height, $organization_name, 0, 1, 'L');

// III. COLLABORATOR(S)
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell($left_width, $row_height, 'III. COLLABORATOR(S):', 0, 0, 'L');
$pdf->SetFont('arial', '', 11);
$pdf->Cell($right_width, $row_height, 'None', 0, 1, 'L');

// IV. TARGET DATE AND VENUE
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell($left_width, $row_height, 'IV. TARGET DATE AND VENUE:', 0, 0, 'L');
$pdf->SetFont('arial', '', 11);
$pdf->Cell($right_width, $row_height, 'January 10, 2025', 0, 1, 'L');
$pdf->Cell($left_width, $row_height, '', 0, 0, 'L'); // Empty left cell for alignment
$pdf->Cell($right_width, $row_height, '8 a.m. to 5 p.m. CvSU-CCAT Covered Court 1', 0, 1, 'L');

// Set column widths
$right_column_width = 140; // Width for the SDG list
$row_height = 6; // Row height

// Add left column
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell($left_width, $row_height, 'V. AGENDA:', 0, 0, 'L');

// Add right column with SDG list
$pdf->SetFont('arial', '', 11);
$pdf->MultiCell($right_width, $row_height, 
    "SDG1- No Poverty\n" .
    "SDG2- Zero Hunger\n" .
    "SDG3- Good Health and Well-being\n" .
    "SDG4- Quality Education\n" .
    "SDG5- Gender Equality\n" .
    "SDG6- Clean Water and Sanitation\n" .
    "SDG7- Affordable and Clean Energy\n" .
    "SDG8- Decent Work and Economic Growth\n" .
    "SDG9- Industry, Innovation and Infrastructure\n" .
    "SDG10- Reduced Inequalities\n" .
    "SDG11- Sustainable Cities and Communities\n" .
    "SDG12- Responsible Consumption and Production\n" .
    "SDG13- Climate Action\n" .
    "SDG14- Life Below Water\n" .
    "SDG15- Life On Land\n" .
    "SDG16- Peace, Justice and Strong Institutions\n" .
    "SDG17- Partnership for the Goals", 
    0, 'L', false, 1, '', '', true);

// VI. RATIONALE
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 10, 'VI. RATIONALE', 0, 1);
$pdf->SetFont('arial', '', 11);
$pdf->MultiCell(0, 10, 
    "Provide short rationale about your activity, focusing on who are the proponents and why this activity will be conducted.", 
    0, 'L', false, 1, '', '', true);

// VII. DESCRIPTION
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 10, 'VII. DESCRIPTION', 0, 1);
$pdf->SetFont('arial', '', 11);
$pdf->MultiCell(0, 10, 
    "Describe the event, focusing on when and where it will happen.", 
    0, 'L', false, 1, '', '', true);

// VIII. OBJECTIVES
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 10, 'VIII. OBJECTIVES', 0, 1);
$pdf->SetFont('arial', '', 11);

// Define the objectives text
$generalObjective = "The general objective of this activity is to………………………………… Specifically, it aims to:";
$objectives = [
    "specific objective 1",
    "specific objective 2",
    "specific objective 3",
    "specific objective 4"
];

// Output the general objective
$pdf->MultiCell(0, 10, $generalObjective, 0, 'L', false, 1, '', '', true);

// Output each specific objective with numbers and indentation
foreach ($objectives as $index => $objective) {
    $formattedObjective = ($index + 1) . ". " . $objective;
    $pdf->Cell(10); // Add indentation
    $pdf->MultiCell(0, 10, $formattedObjective, 0, 'L', false, 1, '', '', true);
}

// IMPLEMENTATION PLAN
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 10, 'IX. IMPLEMENTATION PLAN:', 0, 1);
$pdf->Ln(5); // Add some space

// Set table headers
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(80, 10, 'Activity', 1, 0, 'C'); // Width: 90
$pdf->Cell(80, 10, 'Target Date', 1, 1, 'C'); // Width: 90

// Set example data
$activities = [
    [
        "activity" => "Planning of the event (preparation of proposal, letters for communication, formation of committees, and target approval of the event)",
        "date" => "December 16-20, 2024"
    ],
    [
        "activity" => "Opening Program",
        "date" => "January 10, 2025 (8 a.m. to 9 a.m.)"
    ],
    [
        "activity" => "Conduct of different competitions",
        "date" => "January 10, 2025 (9 a.m. to 5 p.m.)"
    ],
    [
        "activity" => "Post Evaluation Meeting",
        "date" => "January 13, 2025"
    ]
];

// Output table rows
$pdf->SetFont('arial', '', 11);
foreach ($activities as $row) {
    $pdf->MultiCell(80, 10, $row['activity'], 1, 'L', false, 0); // Activity cell
    $pdf->MultiCell(80, 10, $row['date'], 1, 'L', false, 1);     // Date cell
}

// X. IMPLEMENTING GUIDELINES
$pdf->Ln(10); // Add space before the section
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 10, 'X. IMPLEMENTING GUIDELINES:', 0, 1);

$pdf->SetFont('arial', '', 11);
$pdf->MultiCell(0, 10, 
    "1. The ABC Organization Day will be conducted on January 10, 2025 from 8 a.m. to 5 p.m. at the CvSU-CCAT Campus Covered Court 1.\n" .
    "2. Registration will start at 7 a.m.\n" .
    "3. a.\n" .
    "4. b.\n" .
    "5. c.\n" .
    "6. d.\n" .
    "7. e.\n" .
    "8. Venue will be kept clean while observing the Garbage-In-Garbage-Out policy.\n" .
    "9. Upon approval, an excuse letter will be prepared and approved by the chairperson of the department and director for instruction.\n" .
    "10. A letter for the utilization of Covered Court 1 will also be prepared with prior communication to the court custodian.", 
    0, 'L', false, 1, '', '', true);

    // XI. FINANCIAL PLAN
$pdf->Ln(10); // Add space before the section
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 10, 'XI. FINANCIAL PLAN', 0, 1);

$pdf->SetFont('arial', '', 11);

// Projected Revenue
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 10, 'PROJECTED REVENUE', 0, 1);
$pdf->SetFont('arial', '', 11);

// Revenue Table Header
$pdf->SetFillColor(230, 230, 230); // Light gray background for headers
$pdf->Cell(80, 10, 'Description', 1, 0, 'C');
$pdf->Cell(30, 10, 'QTY', 1, 0, 'C');
$pdf->Cell(30, 10, 'UNIT PRICE', 1, 0, 'C');
$pdf->Cell(40, 10, 'TOTAL', 1, 1, 'C');

// Example Revenue Data
$pdf->Cell(80, 10, 'Registration', 1);
$pdf->Cell(30, 10, '100', 1, 0, 'C');
$pdf->Cell(30, 10, '100', 1, 0, 'C');
$pdf->Cell(40, 10, '10,000', 1, 1, 'C');

// Subtotal
$pdf->Cell(140, 10, 'Subtotal', 1, 0, 'R');
$pdf->Cell(40, 10, '10,000', 1, 1, 'C');

// Add spacing before the next section
$pdf->Ln(5);

// Projected Expenses
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 10, 'PROJECTED EXPENSES', 0, 1);
$pdf->SetFont('arial', '', 11);

// Expenses Table Header
$pdf->SetFillColor(230, 230, 230); // Light gray background for headers
$pdf->Cell(80, 10, 'Description', 1, 0, 'C');
$pdf->Cell(30, 10, 'QTY', 1, 0, 'C');
$pdf->Cell(30, 10, 'UNIT PRICE', 1, 0, 'C');
$pdf->Cell(40, 10, 'TOTAL', 1, 1, 'C');

// Example Expenses Data
$pdf->Cell(80, 10, 'Certificates and Program and Invitation', 1);
$pdf->Cell(30, 10, '', 1, 0, 'C');
$pdf->Cell(30, 10, '', 1, 0, 'C');
$pdf->Cell(40, 10, '', 1, 1, 'C');

$pdf->Cell(80, 10, 'Vellum Board', 1);
$pdf->Cell(30, 10, '10', 1, 0, 'C');
$pdf->Cell(30, 10, '50', 1, 0, 'C');
$pdf->Cell(40, 10, '500', 1, 1, 'C');

$pdf->Cell(80, 10, 'Ink (Bk/M/C/Y)', 1);
$pdf->Cell(30, 10, '4', 1, 0, 'C');
$pdf->Cell(30, 10, '285', 1, 0, 'C');
$pdf->Cell(40, 10, '1,140', 1, 1, 'C');

$pdf->Cell(80, 10, 'Food for Judges', 1);
$pdf->Cell(30, 10, '5', 1, 0, 'C');
$pdf->Cell(30, 10, '250', 1, 0, 'C');
$pdf->Cell(40, 10, '1,250', 1, 1, 'C');

$pdf->Cell(80, 10, 'Food for Officers', 1);
$pdf->Cell(30, 10, '10', 1, 0, 'C');
$pdf->Cell(30, 10, '150', 1, 0, 'C');
$pdf->Cell(40, 10, '1,500', 1, 1, 'C');

$pdf->Cell(80, 10, 'Champion Prize', 1);
$pdf->Cell(30, 10, '1', 1, 0, 'C');
$pdf->Cell(30, 10, '1,500', 1, 0, 'C');
$pdf->Cell(40, 10, '1,500', 1, 1, 'C');

$pdf->Cell(80, 10, '1st Runner Up Prize', 1);
$pdf->Cell(30, 10, '1', 1, 0, 'C');
$pdf->Cell(30, 10, '1,000', 1, 0, 'C');
$pdf->Cell(40, 10, '1,000', 1, 1, 'C');

// Subtotal
$pdf->Cell(140, 10, 'Subtotal', 1, 0, 'R');
$pdf->Cell(40, 10, '6,890', 1, 1, 'C');

// Projected Income
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(140, 10, 'Projected Income', 1, 0, 'R');
$pdf->Cell(40, 10, '3,110', 1, 1, 'C');

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

$pdfOutputPath = 'generated_pdfs/' . $eventTitle . '_proposal.pdf';
$pdf->Output();
?>
