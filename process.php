<?php
// Include the database connection file
include '../includes/usersdb_conn.php';

// Load PHPMailer classes
require "../Mail/phpmailer/PHPMailerAutoload.php";
require "../Mail/phpmailer/class.smtp.php";
require "../Mail/phpmailer/class.phpmailer.php";

// Function to generate a random order number
function generateOrderNumber() {
    return 'ORD' . uniqid(); // You can customize the format of the order number as needed
}

// Function to calculate the expected delivery date (for example, 7 days from the current date)
function calculateDeliveryDate() {
    return date('Y-m-d', strtotime('+7 days')); // Adjust the number of days as needed
}

// Check if the form is submitted
if(isset($_POST['place_order'])) {
    // Retrieve form data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $subject = 'Order Confirmation';
    $orderNumber = generateOrderNumber(); // Generate a random order number
    $deliveryDate = calculateDeliveryDate(); // Calculate the expected delivery date

    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'jimjamjim03@gmail.com'; // Your SMTP username
        $mail->Password = 'cwgorepxglfdtvmv'; // Your SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587; // Adjust port if needed

        // Sender and recipient settings
        $mail->setFrom('jimjamjim03@gmail.com', 'Shrungar'); // Sender's email and name
        $mail->addAddress($email, $name); // Recipient's email and name

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = 'Dear ' . $name . ',<br><br>Your order has been successfully placed!<br>' .
                         'Order Number: ' . $orderNumber . '<br>' .
                         'Expected Delivery Date: ' . $deliveryDate . '<br><br>' .
                         'Thank you for your order!
                         <br><br>
                <p>With regards,</p>
                <b>From Shrungar</b>';

        // Send email
        if ($mail->send()) {
            // Redirect back to orders.php with a success parameter
            header("Location: orders.php?success=true");
            exit();
        } else {
            // Redirect back to orders.php with an error parameter
            header("Location: orders.php?error=true");
            exit();
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
