<?php
require_once "auth.php";
require_once "db.php";

// Vérifier si un id_rdv est passé
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

// Marquer comme effectué
$stmt = $conn->prepare("UPDATE rendez_vous SET statut = 'effectue' WHERE id_rdv = ?");
$stmt->bind_param("i", $id_rdv);

if ($stmt->execute()) {
    header("Location: appointments.php?success=marked_done");
} else {
    header("Location: appointments.php?error=update_failed");
}

exit;
