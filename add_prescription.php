<?php 
require_once 'auth.php';
include 'includes/header.php';
include 'db.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $id_consultation = intval($_POST['id_consultation'] ?? 0);
    $ordonnance = trim($_POST['ordonnance'] ?? '');

    if (!$id_consultation || empty($ordonnance)) {
        $error = "Veuillez sélectionner une consultation et saisir l'ordonnance.";
    } else {
        $stmt = $conn->prepare("INSERT INTO prescription (id_consultation, ordonnance) VALUES (?, ?)");
        $stmt->bind_param("is", $id_consultation, $ordonnance);

        if ($stmt->execute()) {
            header("Location: prescriptions.php?added=1");
            exit;
        } else {
            $error = "Erreur lors de l’enregistrement de la prescription : " . $conn->error;
        }
    }
}

// Récupérer la liste des consultations
$sql = "SELECT c.id_consultation, c.date_consultation,
               p.nom AS patient_nom, p.prenom AS patient_prenom
        FROM consultation c
        INNER JOIN patient p ON c.no_dossier = p.no_dossier
        ORDER BY c.id_consultation DESC";

$consultations = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="page-toolbar">
        <h2 class="page-title"><i class="bi bi-file-medical"></i> Nouvelle prescription</h2>
        <a href="prescriptions.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Consultation associée <span class="text-danger">*</span></label>
                <select name="id_consultation" class="form-select" required>
                    <option value="">-- Choisir une consultation --</option>
                    <?php if ($consultations && $consultations->num_rows > 0): ?>
                        <?php while ($c = $consultations->fetch_assoc()): ?>
                            <option value="<?= $c['id_consultation'] ?>"
                                    <?= ((isset($_POST['id_consultation']) && intval($_POST['id_consultation']) === intval($c['id_consultation'])) ? 'selected' : '') ?>>
                                Consultation #<?= $c['id_consultation'] ?> — Patient: <?= htmlspecialchars($c['patient_nom'] . " " . $c['patient_prenom']) ?> (<?= htmlspecialchars($c['date_consultation']) ?>)
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Prescription / Ordonnance <span class="text-danger">*</span></label>
                <textarea name="ordonnance" class="form-control" rows="4" 
                          placeholder="Ex: Paracétamol 500mg, 1 comprimé 3 fois par jour pendant 5 jours..." required><?= htmlspecialchars($_POST['ordonnance'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
            <button type="submit" name="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer la prescription</button>
            <a href="prescriptions.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
