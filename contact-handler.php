<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Sanitize inputs
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$fullName    = clean($_POST['fullName']    ?? '');
$email       = clean($_POST['email']       ?? '');
$phone       = clean($_POST['phone']       ?? '');
$cityState   = clean($_POST['cityState']   ?? '');
$truckMake   = clean($_POST['truckMake']   ?? '');
$truckModel  = clean($_POST['truckModel']  ?? '');
$bedLength   = clean($_POST['bedLength']   ?? '');
$camperModel = clean($_POST['camperModel'] ?? '');
$message     = clean($_POST['message']     ?? '');

// Basic validation
if (empty($fullName) || empty($email) || empty($cityState) || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Required fields missing']);
    exit;
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

// Build email body
$body  = "New inquiry from the Hotomobil USA website.\n\n";
$body .= "-------------------------------------------\n";
$body .= "CONTACT INFORMATION\n";
$body .= "-------------------------------------------\n";
$body .= "Full Name:    {$fullName}\n";
$body .= "Email:        {$email}\n";
$body .= "Phone:        " . ($phone ?: 'Not provided') . "\n";
$body .= "City / State: {$cityState}\n\n";
$body .= "-------------------------------------------\n";
$body .= "TRUCK DETAILS\n";
$body .= "-------------------------------------------\n";
$body .= "Truck Make:   " . ($truckMake  ?: 'Not provided') . "\n";
$body .= "Truck Model:  " . ($truckModel ?: 'Not provided') . "\n";
$body .= "Bed Length:   " . ($bedLength  ?: 'Not provided') . "\n\n";
$body .= "-------------------------------------------\n";
$body .= "CAMPER INTEREST\n";
$body .= "-------------------------------------------\n";
$body .= "Model:        " . ($camperModel ?: 'Not specified') . "\n\n";
$body .= "-------------------------------------------\n";
$body .= "MESSAGE\n";
$body .= "-------------------------------------------\n";
$body .= $message . "\n\n";
$body .= "-------------------------------------------\n";
$body .= "Sent from: gemtruckcamper.com\n";

$to      = 'ebruinal@gemtruckcamper.com';
$subject = 'Hotomobil USA Website Inquiry';
$headers = implode("\r\n", [
    'From: Hotomobil USA Website <noreply@gemtruckcamper.com>',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8',
]);

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Mail sending failed']);
}
