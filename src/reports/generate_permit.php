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
        $this->SetY(-30.48); // Position 1.2 inches from the bottom
        $this->SetFont('play', '', 10); // Set font to Arial

        // First line: SASCO and Budget Request
        $this->Cell(0, 10, 'SASCO', 0, 0, 'L');
        $this->Cell(0, 10, 'Permit to Withdraw', 0, 1, 'R');
        
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


// Function to convert numbers to words
function convertNumberToWord($number = false)
{
    $number = str_replace(array(',', ' '), '' , trim($number));
    if(! $number) {
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
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($number_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $number_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    return strtoupper(implode(' ', $words));
}

// Query to fetch the title and total amount
$query = "SELECT title, total_amount FROM events WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id); // Changed "id" to "i" for integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $eventTitle = strtoupper($row['title']); // Convert the title to uppercase
    $eventAmount = number_format($row['total_amount'], 2); // Format the amount
    $eventAmountWords = convertNumberToWord($row['total_amount']); // Convert amount to words
} else {
    $eventTitle = strtoupper("Event Not Found"); // Default title if event is not found
    $eventAmount = "N/A"; // Default amount if event is not found
    $eventAmountWords = "N/A"; // Default words if event is not found
}


$stmt->close();

// Add titles
$pdf->SetFont($arialBold, '', 11);
$pdf->Cell(0, 0, strtoupper($organization_name), 0, 1, 'C', 0, '', 1);
$pdf->Ln(5);
$pdf->Cell(0, 0, "PERMIT TO WITHDRAW", 0, 1, 'C', 0, '', 1);
$pdf->Cell(0, 0, $eventTitle, 0, 1, 'C', 0, '', 1);
$pdf->Ln(10);

// Date
$pdf->SetFont('arial', '', 11);
// Amount Label
$pdf->Cell(30, 10, 'Amount:', 0, 0, 'L');

// Amount Text
$pdf->Cell(0, 10, $eventAmountWords .' PESOS (P '. $eventAmount .')', 0, 1, 'L');

// Underline the Amount Text
$pdf->SetY($pdf->GetY() - 10);  // Adjust Y position to draw the line under the text
$pdf->Cell(0, 10, '____________________________________________________________', 0, 1, 'L');

// Name of Organization
$pdf->SetFont('arial', '', 11);

// Name of Organization with underline directly below
$pdf->Cell(60, 10, 'Name of Organization:', 0, 0, 'L'); // Label
$pdf->Cell(80, 10, '__________________________', 0, 0, 'L'); // Underline
$pdf->SetX($pdf->GetX() - 80); // Move back to write over the underline
$pdf->Cell(60, 10, $organization_name, 0, 0, 'L'); // Organization name written over the underline

// Organization Type
$pdf->Cell(0, 10, '□ Academic □ Non-Academic', 0, 1, 'L'); // Options with checkboxes

// Purpose
$pdf->Cell(30, 10, 'Purpose:', 0, 0, 'L');
$pdf->Cell(0, 10, $eventTitle, 0, 1, 'L');
$pdf->SetY($pdf->GetY() - 9);
$pdf->Cell(0, 10, '______________________________________________________________', 0, 1, 'R');

// Amount
$pdf->Cell(30, 10, 'Amount:', 0, 0, 'L');
$pdf->Cell(0, 10, $eventAmountWords .' PESOS (P '. $eventAmount .')', 0, 1, 'L');
$pdf->Cell(0, 10, '____________________________________________________________', 0, 1, 'R');

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

$pdfOutputPath = 'generated_pdfs/' . $eventTitle . '_permit.pdf';
$pdf->Output();
?>
