<?php
session_start();
// Include the database connection file
include './includes/usersdb_conn.php';

// Check if user_id exists in the session
if (isset($_SESSION['user_id'])) {
    // Get user_id from the session
    $user_id = $_SESSION['user_id'];
    echo "Welcome, User ID: $user_id";
}
// Include PHPMailer library
require "Mail/phpmailer/PHPMailerAutoload.php";
require "Mail/phpmailer/class.smtp.php";
require "Mail/phpmailer/class.phpmailer.php";

// Check if the form is submitted for appointment booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitAppointment'])) {
    // Escape user inputs for security
   // $appointmentId = uniqid(); // Use uniqid() to generate a unique ID for the appointment
    $appointmentFullName = isset($_POST['name']) ? $_POST['name'] : '';
    $appointmentPhone = isset($_POST['phnumber']) ? $_POST['phnumber'] : '';
    $appointmentEmail = isset($_POST['emailid']) ? $_POST['emailid'] : '';
    $appointmentDate = isset($_POST['appointmentDate']) ? $_POST['appointmentDate'] : '';
    $appointmentTime = isset($_POST['appointmentTime']) ? $_POST['appointmentTime'] : '';
    $appointmentCity = isset($_POST['city']) ? $_POST['city'] : '';
    $appointmentPlace = isset($_POST['place']) ? $_POST['place'] : '';
    $appointmentservice = isset($_POST['service']) ? $_POST['service'] : '';

    // Insert data into the appointments table
    $appointmentSql = "INSERT INTO `appointments` (user_id,name, phnumber, emailid, appointmentDate, appointmentTime, city, place, service) 
                       VALUES (?,?, ?, ?, ?, ?, ?, ?, ?)";

    $appointmentStmt = $conn->prepare($appointmentSql);
    if ($appointmentStmt) {
        $appointmentStmt->bind_param("sssssssss",  $_SESSION['user_id'],$appointmentFullName, $appointmentPhone, $appointmentEmail, $appointmentDate, $appointmentTime, $appointmentCity, $appointmentPlace, $appointmentservice);

        if ($appointmentStmt->execute()) {
            try {
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);

                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username   = 'jimjamjim03@gmail.com'; // Your SMTP username
                $mail->Password   = 'cwgorepxglfdtvmv';   // Your SMTP password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                //Recipients
                $mail->setFrom('jimjamjim03@gmail.com', 'Your Name'); // Sender's email address and name
                $mail->addAddress($appointmentEmail, $appointmentFullName); // Recipient's email address and name

                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Appointment Booking Confirmation';
                $mail->Body    = 'Dear ' . $appointmentFullName . ',<br><br>Your appointment has been successfully booked.<br>
                Please arrive 10 minutes before your scheduled appointment time to ensure a smooth and enjoyable experience. If you need to reschedule or cancel your appointment, please contact us at least 24 hours in advance as contact details are mentioned in our profile.

                If you have any questions or special requests, feel free to let us know, and we will be happy to assist you.
                
                We look forward to seeing you soon!
                <br><br>
                <p>With regards,</p>
                <b>From Shrungar</b><br>
                <br><br>Booking Details:<br>Name: ' . $appointmentFullName . '<br>Phone: ' . $appointmentPhone . '<br>Email: ' . $appointmentEmail . '<br>Appointment Date: ' . $appointmentDate . '<br>Appointment Time: ' . $appointmentTime . '<br>City: ' . $appointmentCity . '<br>Place: ' . $appointmentPlace .'<br>Your service: '.$appointmentservice;
                // Send email
                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            // Redirect or display a success message
            header("Location: success_page.php"); // Change this to the desired page
            exit();
        } else {
            echo "Error executing appointment insertion: " . $conn->error;
        }
    } else {
        echo "Error preparing appointment insertion: " . $conn->error;
    }

    $appointmentStmt->close();
}

// Close the database connection (if you have opened it)
mysqli_close($conn);
?>
