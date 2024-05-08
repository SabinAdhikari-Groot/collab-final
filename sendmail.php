<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Construct the message
    $msg = "Name: $name\n";
    $msg .= "Email: $email\n\n";
    $msg .= "Message:\n$message";

    // Send the email
    $subject = "User contacting staff and admin";
    $recipient = "adsabin.420@gmail.com"; // Replace with your email
    $headers = "From: $name <$email>";

    if (mail($recipient, $subject, $msg, $headers)) {
        echo "Your message has been sent successfully.";
    } else {
        echo "Sorry, there was an error sending your message. Please try again later.";
    }
}
?>
