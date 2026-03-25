<?php
session_start();

$selected_services = $_SESSION['one_time_selected_services'] ?? [];
$selected_dates    = $_SESSION['one_time_selected_dates'] ?? [];

// Redirect if no session data
if (empty($selected_services)) {
    header('Location: field_plan.php');
    exit;
}

require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';
require __DIR__ . '/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $address  = trim($_POST['service_address'] ?? '');
    $town     = trim($_POST['service_town'] ?? '');
    $postcode = strtoupper(trim($_POST['service_postcode'] ?? ''));
    $email    = trim($_POST['service_email'] ?? '');
    $phone    = trim($_POST['service_phone'] ?? '');

    // Validation
    $postcodeRegex = "/^[A-Z]{1,2}[0-9R][0-9A-Z]? ?[0-9][A-Z]{2}$/i";
    $phoneRegex    = "/^(\+44\s?7\d{3}|\(?07\d{3}\)?)\s?\d{3}\s?\d{3}$/";

    if (
        !$address || !$town || !$postcode || !$email || !$phone ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !preg_match($postcodeRegex, $postcode) ||
        !preg_match($phoneRegex, $phone)
    ) {
        $_SESSION['error'] = '⚠️ Please enter valid details.';
        header('Location: confirmation.php');
        exit;
    }

    // Safe output
    $addressSafe  = htmlspecialchars($address);
    $townSafe     = htmlspecialchars($town);
    $postcodeSafe = htmlspecialchars($postcode);
    $phoneSafe    = htmlspecialchars($phone);

    $servicesList = !empty($selected_services) ? implode(", ", $selected_services) : "N/A";
    $datesList    = !empty($selected_dates) ? implode(", ", $selected_dates) : "N/A";

    $isLocal = false; // 🔥 SET TO FALSE ON HOSTINGER

    if (!$isLocal) {

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'admin.fieldcraftservices@gmail.com'; // CHANGE
            $mail->Password   = '';   // CHANGE
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->SMTPDebug  = 0;

            $mail->setFrom('admin.fieldcraftservices@gmail.com', 'FieldCraft Bookings');

            // ===== EMAIL TO CUSTOMER =====
            $mail->addAddress($email);
            $mail->addReplyTo('admin.fieldcraftservices@gmail.com', 'FieldCraft');

            $mail->isHTML(true);
            $mail->Subject = "FieldCraft Booking Confirmed";

            $mail->Body = "
            <h3>Booking Confirmed</h3>
            <p>Your FieldCraft booking has been confirmed.</p>

            <p><strong>Services:</strong><br>$servicesList</p>
            <p><strong>Dates:</strong><br>$datesList</p>

            <p><strong>Address:</strong><br>$addressSafe, $townSafe, $postcodeSafe</p>
            <p><strong>Phone:</strong><br>$phoneSafe</p>

            <p>We will contact you shortly.</p>
            <p>Thank you,<br>FieldCraft Team</p>
            ";

            $mail->send();

            // ===== EMAIL TO YOU (ADMIN) =====
            $mail->clearAddresses();
            $mail->clearReplyTos();

            $mail->addAddress('admin.fieldcraftservices@gmail.com'); // YOUR EMAIL

            $mail->Subject = "New Booking Received";

            $mail->Body = "
            <h3>New Booking Received</h3>

            <p><strong>Customer Email:</strong> $email</p>

            <p><strong>Services:</strong><br>$servicesList</p>
            <p><strong>Dates:</strong><br>$datesList</p>

            <p><strong>Address:</strong><br>$addressSafe, $townSafe, $postcodeSafe</p>
            <p><strong>Phone:</strong><br>$phoneSafe</p>
            ";

            $mail->send();

            $_SESSION['success'] = "✅ Booking confirmed! Email sent.";

            // Prevent duplicate submissions
            unset($_SESSION['one_time_selected_services']);
            unset($_SESSION['one_time_selected_dates']);

        } catch (Exception $e) {
            $_SESSION['error'] = "❌ Booking saved, but email could not be sent.";
        }

    } else {
        $_SESSION['success'] = "✅ Booking confirmed! (Local test mode)";
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

<form method="POST" class="card p-4" style="max-width:600px;margin:auto;">

  <h5>Service Location & Contact</h5>

  <input type="text" name="service_address" class="form-control mb-2" placeholder="Address" required>

  <input type="text" name="service_town" class="form-control mb-2" placeholder="Town / City" required>

  <input type="text" name="service_postcode" class="form-control mb-2" placeholder="Postcode (e.g. CB1 1AA)" required>

  <input type="email" name="service_email" class="form-control mb-2" placeholder="Email Address" required>

  <input type="text" name="service_phone" class="form-control mb-3" placeholder="Phone Number (e.g. 07123 456789)" required>

  <button type="submit" class="btn btn-success w-100">
    Confirm Booking
  </button>

</form>

</div>
