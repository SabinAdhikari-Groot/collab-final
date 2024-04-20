<?php
include 'db.php';

// Start or resume a session to access $_SESSION superglobal
session_start();

// Retrieve email from session
$email = $_SESSION['email'];

// Perform the deletion
$sql = "DELETE FROM booking WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);

// Execute the statement
if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}

// Close the statement and the database connection
$stmt->close();
$conn->close();
?>
