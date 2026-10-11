<?php
require_once 'auth.php';
include 'includes/header.php';
include 'db.php';

// When I call session_start here it sends me a notice
// That is why i comment this line
// session_start(); // If sessions are used

// Get and validate ID
if (!isset($_GET['id'])) {
    die("ID de prescription manquant");
}
$id = intval($_GET['id']);

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("
    SELECT p.*, c.date_consultation,
           pat.nom AS patient_nom, pat.prenom AS patient_prenom,
           m.nom AS medecin_nom
    FROM prescription p
    INNER JOIN consultation c ON p.id_consultation = c.id_consultation
    INNER JOIN patient pat ON c.no_dossier = pat.no_dossier
    INNER JOIN medecin m ON c.code_medecin = m.code_medecin
    WHERE p.id_prescription = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

if (!$data) {
    die("Prescription introuvable.");
}
?>

<div class="container">
    <h2>Prescription #<?= htmlspecialchars($data['id_prescription']) ?></h2>
    <p><strong>Patient :</strong> <?= htmlspecialchars($data['patient_nom']) ?> <?= htmlspecialchars($data['patient_prenom']) ?></p>
    <p><strong>Médecin :</strong> Dr. <?= htmlspecialchars($data['medecin_nom']) ?></p>
    <p><strong>Date Consultation :</strong> <?= htmlspecialchars($data['date_consultation']) ?></p>

    <h3>Ordonnance :</h3>
    <div class="card">
        <p><?= nl2br(htmlspecialchars($data['ordonnance'])) ?></p>
    </div>

    <a href="prescriptions.php" class="btn">Retour</a>
</div>

<a href="print_prescription.php?id=<?= htmlspecialchars($data['id_prescription']) ?>" 
   class="btn btn-primary" target="_blank">
    <i class="bi bi-printer"></i> Imprimer l’ordonnance
</a>

<?php include 'includes/footer.php'; ?>