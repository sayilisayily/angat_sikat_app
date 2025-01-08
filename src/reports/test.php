<?php
require_once('../../libs/tcpdf/TCPDF-main/tcpdf.php');

class CustomPDF extends TCPDF {
    // Header
    public function Header() {
        $this->SetFont('arial', '', 12);
        $this->Cell(0, 10, 'Event Report', 0, 1, 'C');
    }

    // Footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Create instance of CustomPDF class
$pdf = new CustomPDF();
$pdf->AddPage();

// Set font for the content
$pdf->SetFont('arial', '', 12);

// Adjustments for left and right column widths
$left_width = 80;  // Width for the left column
$right_width = 120; // Width for the right column
$row_height = 8;   // Height for each row

// I. TITLE
$pdf->Cell($left_width, $row_height, 'I. TITLE:', 0, 0, 'L'); // Left cell
$pdf->Cell($right_width, $row_height, 'Organization Day', 0, 1, 'L'); // Right cell

// II. PROPONENT
$pdf->Cell($left_width, $row_height, 'II. PROPONENT:', 0, 0, 'L');
$pdf->Cell($right_width, $row_height, 'ABC Organization', 0, 1, 'L');

// III. COLLABORATOR(S)
$pdf->Cell($left_width, $row_height, 'III. COLLABORATOR(S):', 0, 0, 'L');
$pdf->Cell($right_width, $row_height, 'None', 0, 1, 'L');

// IV. TARGET DATE AND VENUE
$pdf->Cell($left_width, $row_height, 'IV. TARGET DATE AND VENUE:', 0, 0, 'L');
$pdf->Cell($right_width, $row_height, 'January 10, 2025', 0, 1, 'L');
$pdf->Cell($left_width, $row_height, '', 0, 0, 'L'); // Empty left cell for alignment
$pdf->Cell($right_width, $row_height, '8 a.m. to 5 p.m. CvSU-CCAT Covered Court 1', 0, 1, 'L');

// V. AGENDA
$pdf->Ln(10); // Add space before the section
$pdf->SetFont('arial', '', 11);
$pdf->Cell(0, 10, 'V. AGENDA:', 0, 1, 'L'); // Section title

// Set column widths
$left_column_width = 50; // Width for "V. AGENDA"
$right_column_width = 140; // Width for the SDG list
$row_height = 6; // Row height

// Add left column
$pdf->SetFont('arial', '', 11);
$pdf->Cell($left_column_width, $row_height, 'V. AGENDA:', 0, 0, 'L');

// Add right column with SDG list
$pdf->SetFont('arial', '', 11);
$pdf->MultiCell($right_column_width, $row_height, 
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
$pdf->Ln(8); // Add spacing before the next section
$pdf->Cell(0, 10, 'VI. RATIONALE', 0, 1);
$pdf->MultiCell(0, 10, 
    "Provide short rationale about your activity, focusing on who are the proponents and why this activity will be conducted.", 
    0, 'L', false, 1, '', '', true);

// VII. DESCRIPTION
$pdf->Ln(8);
$pdf->Cell(0, 10, 'VII. DESCRIPTION', 0, 1);
$pdf->MultiCell(0, 10, 
    "Describe the event, focusing on when and where it will happen.", 
    0, 'L', false, 1, '', '', true);

// VIII. OBJECTIVES
$pdf->Ln(8);
$pdf->Cell(0, 10, 'VIII. OBJECTIVES', 0, 1);
$pdf->MultiCell(0, 10, 
    "The general objective of this activity is to………………………………… Specifically, it aims to:\n" .
    "specific objective 1;\nspecific objective 2;\nspecific objective 3; and\nspecific objective 4.", 
    0, 'L', false, 1, '', '', true);

// Output the PDF
$pdf->Output('event_report.pdf', 'I');
?>
