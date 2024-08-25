<?php
include('sessionCheck.php');

// Include the FPDF library
require_once('fpdf.php');

// Collect form data
$projectName = $_POST['projectName'];
$location = $_POST['location'];
$clientName = $_POST['clientName'];
$inspectionDate = $_POST['inspectionDate'];
$installationDescription = $_POST['installationDescription'];
$installationType = $_POST['installationType'];
$mainSupplier = $_POST['mainSupplier'];
$r1rt = $_POST['r1rt'];
$insulationResistance = $_POST['insulationResistance'];
// Add more fields as needed

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Add content to the PDF
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Schedule of Test Report', 0, 1, 'C');
$pdf->Ln(10);

// Add other content and styling as needed
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Project Name: ' . $projectName, 0, 1, 'L');
$pdf->Cell(0, 10, 'Location/Address: ' . $location, 0, 1, 'L');
$pdf->Cell(0, 10, 'Client Name: ' . $clientName, 0, 1, 'L');
$pdf->Cell(0, 10, 'Date of Inspection: ' . $inspectionDate, 0, 1, 'L');
$pdf->Ln(10);

// Repeat similar sections for other form data...

// Output the PDF to the browser or save to a file
$pdf->Output('ScheduleOfTestReport.pdf', 'F');
?>
