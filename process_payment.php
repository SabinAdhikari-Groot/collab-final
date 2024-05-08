<?php
include 'db.php';

// Start or resume a session to access $_SESSION superglobal
session_start();

// Retrieve email and booking ID from session
$email = $_SESSION['email'];

// Fetch booking details for the logged-in user
$sql = "SELECT * FROM booking WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Assuming there's only one booking per user for simplicity
$booking = $result->fetch_assoc();
$bookingId = $booking['id'];
$price = $booking['price'];

// Insert payment details into payment_details table
$paymentStatus = 'done';
$paymentDate = date('Y-m-d'); // Current date
$username = $_SESSION['email'];

$insertSql = "INSERT INTO payment_details (booking_id, username, payment_status, payment_date, price) VALUES (?, ?, ?, ?, ?)";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("issss", $bookingId, $username, $paymentStatus, $paymentDate, $price);
$insertStmt->execute();

// You can perform further actions like updating booking status, sending email notifications, etc.

// Redirect back to mybookings page
header("Location: mybooking.php");
exit();
?>
