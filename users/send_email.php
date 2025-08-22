<?php
session_start();
// --- STEP 0: Import necessary libraries ---
require_once __DIR__ . '/../vendor/autoload.php'; // For PHPMailer (if using Composer)
require_once __DIR__ .('/../fpdf186/fpdf.php');      // << Correct path to FPDF library
require_once __DIR__ . '/../vendor/autoload.php'; // path ไป vendor/autoload.php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // path ไป root ของโปรเจกต์
$dotenv->load();
// Use PHPMailer namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- [NEW] STEP 1: Create a custom PDF class for a booking confirmation layout ---
class PDF extends FPDF
{
    
    // Page header
    function Header()
    {
        // --- Header Title ---
        $this->SetFont('Arial','B',28);
        $this->SetTextColor(0, 123, 255); // Blue color
        $this->Cell(0, 20, 'Booking Confirmation', 0, 1, 'L');
        $this->SetFont('Arial','',11);
        $this->SetTextColor(100);
        $this->Cell(0, 5, 'Thank you for booking with us. Your reservation is confirmed.', 0, 1, 'L');
        $this->Ln(15);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-20);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(150); // Grey text
        // Draw a line
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Cell(0,10,'This is a computer-generated document. No signature is required.',0,0,'C');
    }
}
if(isset($_SESSION['email_data'])){
    // --- STEP 2: Prepare data for the confirmation ---
    $customerName = $_SESSION['email_data']['user_firstname'] ." ".$_SESSION['email_data']['user_lastname'];
    $customerEmail = $_SESSION['email_data']['user_email'];
    $bookingId = "HST-" . $_SESSION['email_data']['charge_id'];
    $bookingDate = date($_SESSION['email_data']['BookingDate']);
    $checkInDate = $_SESSION['email_data']['checkIn'];
    $checkOutDate = $_SESSION['email_data']['checkOut'];
    $proName = $_SESSION['email_data']['property_name'];
    $proProvince =$_SESSION['email_data']['property_pro'];
    $proDis = $_SESSION['email_data']['property_dis'];
    $proSub = $_SESSION['email_data']['property_sub'];
    $roomNum = $_SESSION['email_data']['room_num'];
    $roomCap = $_SESSION['email_data']['room_cap'];
    $guests = $_SESSION['email_data']['guests'];
    $totalAmount = $_SESSION['email_data']['total_price'];
    $hostName =$_SESSION['email_data']['host_firstname'];
    $hostPhone = $_SESSION['email_data']['host_phone'];

    
    // Item details
    $items = [
        ['name' => 'Deluxe Room', 'details' => '1 King Bed, Sea View'],
        ['name' => 'Airport Transfer', 'details' => 'Arrival & Departure'],
    ];
    
    
    // --- STEP 3: Create and save the PDF file using the new custom class ---
    $pdf = new PDF(); // << Use our new PDF class
    $pdf->AddPage();
    
    // --- Key Information Section ---
    $pdf->SetFont('Arial','B',16);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Cell(0, 12, 'Your Booking Details', 0, 1, 'L', true);
    $pdf->Ln(8);
    
    // Booking ID
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(50, 8, 'Booking ID:', 0, 0, 'L');
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0, 8, $bookingId, 0, 1, 'L');
    
    // Guest Name
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(50, 8, 'Guest Name:', 0, 0, 'L');
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0, 8, $customerName, 0, 1, 'L');
    $pdf->Ln(10);
    
    // --- Dates Section ---
    // Check-in
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(95, 8, 'Check-in', 0, 0, 'L');
    // Check-out
    $pdf->Cell(95, 8, 'Check-out', 0, 1, 'L');
    
    // Separator line for dates
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY());
    $pdf->Ln(2);
    
    $pdf->SetFont('Arial','',16);
    $pdf->Cell(95, 10, $checkInDate, 0, 0, 'L');
    $pdf->Cell(95, 10, $checkOutDate, 0, 1, 'L');
    $pdf->SetFont('Arial','',10);
    $pdf->SetTextColor(120);
    $pdf->Cell(95, 6, 'From 02:00 PM', 0, 0, 'L');
    $pdf->Cell(95, 6, 'Until 12:00 PM', 0, 1, 'L');
    $pdf->SetTextColor(0);
    $pdf->Ln(15);
    
    
    // --- [NEW] Services Included Section (No Table) ---
    $pdf->SetFont('Arial','B',16);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Cell(0, 12, 'Room & Services', 0, 1, 'L', true);
    $pdf->Ln(8);
    
    
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0, 8, $roomNum, 0, 1, 'L');
        
        // Item Details (indented)
    $pdf->SetFont('Arial','',11);
    $pdf->SetTextColor(100);
    $pdf->Cell(5); // Indent
    $pdf->Cell(0, 6,  $roomCap, 0, 1, 'L');
    $pdf->SetTextColor(0);
    $pdf->Cell(0, 12, 'Guests', 0, 1, 'L', true);
    $pdf->Cell(0, 8, $guests, 0, 1, 'L');
    $pdf->Ln(6); // Space between items
    
    // foreach ($items as $item) {
    //     // Item Name (as a title)
    //     $pdf->SetFont('Arial','B',12);
    //     $pdf->Cell(0, 8, $item['name'], 0, 1, 'L');
        
    //     // Item Details (indented)
    //     $pdf->SetFont('Arial','',11);
    //     $pdf->SetTextColor(100);
    //     $pdf->Cell(5); // Indent
    //     $pdf->Cell(0, 6, $item['details'], 0, 1, 'L');
    //     $pdf->SetTextColor(0);
    //     $pdf->Ln(6); // Space between items
    // }
    
    // --- Contact Info ---
    $pdf->Ln(10);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0, 8, 'Contact & Location', 0, 1, 'L');
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY());
$pdf->Ln(2);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0, 6, $proName, 0, 1, 'L');
$pdf->Cell(0, 6, $proSub." , ". $proDis." , ". $proProvince, 0, 1, 'L');
$pdf->Cell(0, 6, 'Host: '.$hostName, 0, 1, 'L');
$pdf->Cell(0, 6, 'Phone: '.$hostPhone, 0, 1, 'L');
    
    
    // Save the PDF to the server temporarily
    $pdfFileName = "confirmation_" . $bookingId . ".pdf";
    $pdf->Output('F', $pdfFileName); // 'F' means Save to file
    
    
    // --- STEP 4: Send the email with the PDF attachment ---
    $mail = new PHPMailer(true);
    
    try {
        // --- Server (SMTP) settings ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['EMAIL']; // Your Gmail address
        $mail->Password   = $_ENV['PASSWORD']; // << IMPORTANT: Use your real App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = "UTF-8";
    
        // Sender and recipient
        $mail->setFrom($_ENV['EMAIL'], 'Homestay System');
        $mail->addAddress($customerEmail, 'User ');
    
        // --- Attach the PDF we created ---
        $mail->addAttachment($pdfFileName);
    
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your Booking Confirmation from Phumin Homestay (ID: ' . $bookingId . ')';
        $mail->Body    = 'Dear ' . $customerName . ', <br><br>Your booking is confirmed! Please find your booking confirmation voucher attached to this email.<br><br>We look forward to welcoming you.<br><br>Best regards,<br>Phumin System';
        $mail->AltBody = 'Your booking is confirmed! Please find your booking confirmation voucher attached to this email.';
    
        $mail->send();
        $_SESSION['message'] = "ส่งหลักฐานการจองไปยังอีเมลของคุณเรียบร้อยแล้ว!";
        header('Location: main-menu.php');
    
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    } finally {
        // --- STEP 5: Delete the temporary PDF file from the server ---
        if (file_exists($pdfFileName)) {
            unlink($pdfFileName);
        }
    }

}else {
    // กรณีไม่มีข้อมูลใน session อาจจะ redirect กลับไปหน้าหลัก
    echo "ไม่พบข้อมูลที่จำเป็นสำหรับการส่งอีเมล";
    header('Location: main-menu.php');
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Page Title</title>
</head>

<body>
    <?php
                if (isset($_SESSION['error'])) {
                    echo "<div class='alert alert-danger'><i class='fa-solid fa-ban'></i>" . $_SESSION['error'] . "</div>";
                    unset($_SESSION['error']);
                }
    
                if (isset($_SESSION['message'])) {
                    echo "<div class='alert alert-success'><i class='fa-solid fa-check'></i>" . $_SESSION['message'] . "</div>";
                    unset($_SESSION['message']);
                }
        ?>

</body>

</html>