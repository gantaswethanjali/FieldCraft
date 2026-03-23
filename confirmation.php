<?php
session_start();

// --- Ensure services & dates exist ---
$selected_services = $_SESSION['one_time_selected_services'] ?? [];
$selected_dates    = $_SESSION['one_time_selected_dates'] ?? [];

// --- Load PHPMailer ---
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';
require __DIR__ . '/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $address  = trim($_POST['service_address'] ?? '');
    $town     = trim($_POST['service_town'] ?? '');
    $postcode = trim($_POST['service_postcode'] ?? '');
    $email    = trim($_POST['service_email'] ?? '');

    // Validation
    if (!$address || !$town || !$postcode || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = '⚠️ Please fill all fields correctly.';
        header('Location: confirmation.php');
        exit;
    }

    // ✅ For local testing, comment out SMTP to avoid errors
    $isLocal = true; // set false on Hostinger

    if (!$isLocal) {
        $mail = new PHPMailer(true);

        try {
            // 🔥 SMTP SETTINGS (Hostinger)
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'email'; 
            $mail->Password   = 'password'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('admin.fieldcraftservices@gmail.com', 'FieldCraft Bookings');
            $mail->addAddress($email);
            $mail->addReplyTo($email);

            $mail->isHTML(true);
            $mail->Subject = "FieldCraft Booking Confirmed";

            $mail->Body = "
            <h3>Booking Confirmed</h3>
            <p>Your FieldCraft booking has been confirmed.</p>

            <p><strong>Services:</strong><br>" . implode(", ", $selected_services) . "</p>

            <p><strong>Dates:</strong><br>" . implode(", ", $selected_dates) . "</p>

            <p><strong>Address:</strong><br>$address, $town, $postcode</p>

            <p>One of our colleagues will contact you shortly.</p>

            <p>Thank you,<br>FieldCraft Team</p>
            ";

            $mail->send();
            $_SESSION['success'] = "✅ Booking confirmed! Email sent to $email.";

        } catch (Exception $e) {
            $_SESSION['error'] = "❌ Email failed: " . $mail->ErrorInfo;
        }
    } else {
        // ✅ Local testing: skip email
        $_SESSION['success'] = "✅ Booking confirmed! (Email skipped in local testing)";
    }

    header('Location: confirmation.php');
    exit;
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">

<h2 class="text-center mb-4 text-success">Confirm Your Service</h2>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="alert alert-success text-center">
    <?= htmlspecialchars($_SESSION['success']) ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
  <div class="alert alert-danger text-center">
    <?= htmlspecialchars($_SESSION['error']) ?>
  </div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (empty($selected_services)): ?>
  <div class="alert alert-danger text-center">
    No services selected. <a href="field_plan.php">Go back</a>
  </div>
<?php else: ?>

<form method="POST" class="card p-4" style="max-width:600px;margin:auto;">

  <h5>Service Location & Contact</h5>

  <input type="text" name="service_address" class="form-control mb-2" placeholder="Address" required>
  <input type="text" name="service_town" class="form-control mb-2" placeholder="Town / City" required>
  <input type="text" name="service_postcode" class="form-control mb-2" placeholder="Postcode" required>
  <input type="email" name="service_email" class="form-control mb-3" placeholder="Email Address" required>

  <button type="submit" class="btn btn-success w-100">
    Confirm Booking
  </button>

</form>

<?php endif; ?>

</div>