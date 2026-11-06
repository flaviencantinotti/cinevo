<?php

function enregistrerMessageContact($conn, $nom, $email, $message) {
    $stmt = $conn->prepare("INSERT INTO messages_contact (nom, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $nom, $email, $message);
    $stmt->execute();
}
