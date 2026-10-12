<?php
session_start();
if (!isset($_SESSION['code_medecin'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

$error = "";

// Charger la liste des patients pour le menu déroulant
$patients = $conn->query("SELECT no_dossier, nom, prenom FROM patient ORDER BY nom ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_dossier = trim($_POST['no_dossier'] ?? '');
    $code_medecin = $_SESSION['code_medecin'];
    $symptome = trim($_POST['symptome'] ?? '');
    $date_consultation = !empty($_POST['date_consultation']) ? $_POST['date_consultation'] : date("Y-m-d");

    if (empty($no_dossier) || empty($symptome)) {
        $error = "Veuillez sélectionner un patient et saisir les symptômes observés.";
    } else {
        $stmt = $conn->prepare("INSERT INTO consultation (no_dossier, code_medecin, symptome, date_consultation) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $no_dossier, $code_medecin, $symptome, $date_consultation);

        if ($stmt->execute()) {
            header("Location: consultations.php?added=1");
            exit();
        } else {
            $error = "Erreur lors de l'enregistrement de la consultation : " . $conn->error;
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="page-toolbar">
        <h2 class="page-title"><i class="bi bi-clipboard2-plus"></i> Enregistrer une consultation</h2>
        <a href="consultations.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Patient <span class="text-danger">*</span></label>
                <select name="no_dossier" class="form-select" required>
                    <option value="">-- Sélectionner un patient --</option>
                    <?php if ($patients && $patients->num_rows > 0): ?>
                        <?php while ($p = $patients->fetch_assoc()) : ?>
                            <option value="<?= htmlspecialchars($p['no_dossier']) ?>" 
                                    <?= (($_POST['no_dossier'] ?? '') === $p['no_dossier']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nom'] . " " . $p['prenom']) ?> (Dossier #<?= htmlspecialchars($p['no_dossier']) ?>)
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Date de la consultation <span class="text-danger">*</span></label>
                <input type="date" name="date_consultation" class="form-control" 
                       value="<?= htmlspecialchars($_POST['date_consultation'] ?? date('Y-m-d')) ?>" required>
            </div>

            <div class="col-12">
                <label class="form-label">Symptômes / Motifs de la consultation <span class="text-danger">*</span></label>
                <textarea name="symptome" class="form-control" rows="4" 
                          placeholder="Décrivez les symptômes, observations médicales ou plaintes du patient..." required><?= htmlspecialchars($_POST['symptome'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer la consultation</button>
            <a href="consultations.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
