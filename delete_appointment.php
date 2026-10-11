<?php
require_once "auth.php";
require_once "db.php";

// Vérifier si un id_rdv est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: appointments.php?error=invalid_id");
    exit;
}

$id_rdv = intval($_GET['id']);

// Vérifier si le rendez-vous existe
$check = $conn->prepare("SELECT id_rdv FROM rendez_vous WHERE id_rdv = ?");
$check->bind_param("i", $id_rdv);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    header("Location: appointments.php?error=not_found");
    exit;
}

// Suppression du rendez-vous
$stmt = $conn->prepare("DELETE FROM rendez_vous WHERE id_rdv = ?");
$stmt->bind_param("i", $id_rdv);

if ($stmt->execute()) {
    header("Location: appointments.php?success=deleted");
} else {
    header("Location: appointments.php?error=delete_failed");
}

exit;
