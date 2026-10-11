<?php 
require_once 'auth.php';
include 'includes/header.php';
include 'db.php';

// Récupérer la liste des consultations
$sql = "SELECT c.id_consultation, c.date_consultation,
               p.nom AS patient_nom, p.prenom AS patient_prenom
        FROM consultation c
        INNER JOIN patient p ON c.no_dossier = p.no_dossier
        ORDER BY c.id_consultation DESC";

$consultations = $conn->query($sql);
?>

<div class="container">
    <h2><i class="bi bi-file-medical"></i> Nouvelle prescription</h2>

    <form action="" method="POST">
        
        <label>Consultation</label>
        <select name="id_consultation" required>
            <option value="">-- Choisir une consultation --</option>
            <?php while ($c = $consultations->fetch_assoc()): ?>
            <option value="<?= $c['id_consultation'] ?>">
                #<?= $c['id_consultation'] ?> - 
                <?= htmlspecialchars($c['patient_nom'] . " " . $c['patient_prenom']) ?>
                (<?= htmlspecialchars($c['date_consultation']) ?>)
            </option>
            <?php endwhile; ?>
        </select>

        <label>Ordonnance</label>
        <textarea name="ordonnance" placeholder="Ex : Amoxicilline 500mg, 3 fois par jour..." required></textarea>

        <button type="submit" name="submit" class="btn">Enregistrer</button>
    </form>
</div>

<?php
if (isset($_POST['submit'])) {
    $id_consultation = $_POST['id_consultation'];
    $ordonnance = $_POST['ordonnance'];

    $stmt = $conn->prepare("INSERT INTO prescription (id_consultation, ordonnance) VALUES (?, ?)");
    $stmt->bind_param("is", $id_consultation, $ordonnance);

    if ($stmt->execute()) {
        echo "<script>alert('Prescription enregistrée avec succès'); window.location='prescriptions.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de l’enregistrement');</script>";
    }
}
?>

<?php include 'includes/footer.php'; ?>
