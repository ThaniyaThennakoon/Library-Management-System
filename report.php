<?php
require('fpdf.php');

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',20);
        $this->Cell(0,10,'LIBRARY ISSUED BOOKS REPORT',0,1,'C');
        $this->SetFont('Arial','',10);
        // තෝරාගත් දිනය වාර්තාවේ මුදුනත පෙන්වීම
        $display_date = isset($_GET['report_date']) ? $_GET['report_date'] : date('Y-m-d');
        $this->Cell(0,5,'Date: '.$display_date,0,1,'C');
        $this->Ln(10);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();

// Table Header
$pdf->SetFillColor(40, 40, 40);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial','B',10); // අකුරු තරමක් කුඩා කිරීමෙන් වැඩි ඉඩක් ලැබේ

// තීරු පළල වෙනස් කිරීම (Book Title සඳහා වැඩි ඉඩක්)
$pdf->Cell(65,10,'Book Title',1,0,'C',true); 
$pdf->Cell(25,10,'Member ID',1,0,'C',true);
$pdf->Cell(50,10,'Member Name',1,0,'C',true);
$pdf->Cell(35,10,'Issue Date',1,1,'C',true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','',10);

$conn = mysqli_connect("localhost", "root", "", "librarydb");

// දිනය අනුව දත්ත පෙරීම (Filter)
$selected_date = isset($_GET['report_date']) ? $_GET['report_date'] : date('Y-m-d');
$query = mysqli_query($conn, "SELECT books.title, users.member_id, users.full_name, issued_books.issue_date 
                              FROM issued_books 
                              JOIN users ON issued_books.user_id = users.id 
                              JOIN books ON issued_books.book_id = books.id 
                              WHERE issued_books.issue_date = '$selected_date'");

if(mysqli_num_rows($query) > 0) {
    while($row = mysqli_fetch_array($query)){
        // Cell වල පළල කලින් Header එකට සමාන විය යුතුයි
        $pdf->Cell(65,10, $row['title'], 1);
        $pdf->Cell(25,10, $row['member_id'], 1);
        $pdf->Cell(50,10, $row['full_name'], 1);
        $pdf->Cell(35,10, $row['issue_date'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(175,10, 'No records found for this date', 1, 1, 'C');
}

$pdf->Output();
?>