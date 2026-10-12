<?php
require 'auth.php';
require 'db.php';
include 'includes/header.php';

$medecin_id = $_SESSION['medecin_id'] ?? $_SESSION['code_medecin'] ?? null;
if (!$medecin_id) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Erreur : médecin non identifié en session.</div></div>";
    include 'includes/footer.php';
    exit;
}

// Récupérer la liste des patients
$patients_stmt = $conn->prepare("SELECT no_dossier, nom, prenom FROM patient ORDER BY nom, prenom");
$patients_stmt->execute();
$patients = $patients_stmt->get_result();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_dossier = intval($_POST['no_dossier'] ?? 0);
    $date_rdv = trim($_POST['date_rdv'] ?? '');
    $heure_rdv = trim($_POST['heure_rdv'] ?? '');
    $motif = trim($_POST['motif'] ?? '');

    // Validation minimale
    if (!$no_dossier || empty($date_rdv) || empty($heure_rdv) || empty($motif)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Vérifier conflit (même médecin, même date+heure)
        $check_sql = "SELECT COUNT(*) AS cnt FROM rendez_vous WHERE code_medecin = ? AND date_rdv = ? AND heure_rdv = ?";
        $check = $conn->prepare($check_sql);
        $check->bind_param("iss", $medecin_id, $date_rdv, $heure_rdv);
        $check->execute();
        $cnt = $check->get_result()->fetch_assoc()['cnt'];

        if ($cnt > 0) {
            $error = "Conflit d'horaire : vous avez déjà un rendez-vous prévu à cette date et heure.";
        } else {
            $insert_sql = "INSERT INTO rendez_vous (no_dossier, code_medecin, date_rdv, heure_rdv, statut) VALUES (?, ?, ?, ?, 'en_attente')";
            $ins = $conn->prepare($insert_sql);
            $ins->bind_param("iiss", $no_dossier, $medecin_id, $date_rdv, $heure_rdv);

            if ($ins->execute()) {
                header("Location: appointments.php?added=1");
                exit;
            } else {
                $error = "Erreur lors de l'enregistrement : " . $conn->error;
            }
        }
    }
}
?>

<div class="container mt-4">
    <div class="page-toolbar">
        <h2 class="page-title"><i class="bi bi-calendar2-plus"></i> Planifier un rendez-vous</h2>
        <a href="appointments.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle me-2"></i> <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Patient <span class="text-danger">*</span></label>
                <select name="no_dossier" class="form-select" required>
                    <option value="">-- Sélectionner un patient --</option>
                    <?php if ($patients && $patients->num_rows > 0): ?>
                        <?php while ($p = $patients->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($p['no_dossier']) ?>"
                                    <?= ((isset($_POST['no_dossier']) && intval($_POST['no_dossier']) === intval($p['no_dossier'])) ? 'selected' : '') ?>>
                                <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?> (Dossier #<?= htmlspecialchars($p['no_dossier']) ?>)
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Date du rendez-vous <span class="text-danger">*</span></label>
                <input type="date" name="date_rdv" class="form-control" 
                       value="<?= htmlspecialchars($_POST['date_rdv'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Heure du rendez-vous <span class="text-danger">*</span></label>
                <input type="time" name="heure_rdv" class="form-control" 
                       value="<?= htmlspecialchars($_POST['heure_rdv'] ?? '') ?>" required>
            </div>

            <div class="col-12">
                <label class="form-label">Motif du rendez-vous <span class="text-danger">*</span></label>
                <textarea name="motif" class="form-control" rows="3" 
                          placeholder="Ex: Suivi médical, consultation de contrôle, analyse des résultats..." required><?= htmlspecialchars($_POST['motif'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
            <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i> Enregistrer le rendez-vous</button>
            <a href="appointments.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
