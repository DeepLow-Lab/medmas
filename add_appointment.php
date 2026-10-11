<?php
require 'auth.php';
require 'db.php';
include 'includes/header.php';

$medecin_id = $_SESSION['medecin_id'] ?? $_SESSION['code_medecin'] ?? null;
if (!$medecin_id) {
    echo "<div class='container'>Erreur : médecin non identifié en session.</div>";
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
    $no_dossier = intval($_POST['no_dossier']);
    $date_rdv = $_POST['date_rdv'];
    $heure_rdv = $_POST['heure_rdv'];
    $motif = trim($_POST['motif']);

    // Validation minimale
    if (!$no_dossier || !$date_rdv || !$heure_rdv || $motif === "") {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Optionnel : vérifier conflit (même médecin, même date+heure)
        $check_sql = "SELECT COUNT(*) AS cnt FROM rendez_vous WHERE code_medecin = ? AND date_rdv = ? AND heure_rdv = ?";
        $check = $conn->prepare($check_sql);
        $check->bind_param("iss", $medecin_id, $date_rdv, $heure_rdv);
        $check->execute();
        $cnt = $check->get_result()->fetch_assoc()['cnt'];

        if ($cnt > 0) {
            $error = "Conflit : vous avez déjà un rendez-vous à cette date/heure.";
        } else {
            $insert_sql = "INSERT INTO rendez_vous (no_dossier, code_medecin, date_rdv, heure_rdv, statut) VALUES (?, ?, ?, ?, 'en_attente')";
            $ins = $conn->prepare($insert_sql);
            $ins->bind_param("iiss", $no_dossier, $medecin_id, $date_rdv, $heure_rdv);

            if ($ins->execute()) {
                $success = "Rendez-vous enregistré.";
                // redirection optionnelle :
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
    <h2><i class="bi bi-calendar2-plus"></i> Nouveau rendez-vous</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Patient</label>
        <select name="no_dossier" required>
            <option value="">-- Sélectionner --</option>
            <?php while ($p = $patients->fetch_assoc()): ?>
                <option value="<?= $p['no_dossier'] ?>">
                    <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Date</label>
        <input type="date" name="date_rdv" required>

        <label>Heure</label>
        <input type="time" name="heure_rdv" required>

        <label>Motif</label>
        <textarea name="motif" rows="3" required></textarea>

        <button class="btn mt-3" type="submit">Enregistrer</button>
        <a href="appointments.php" class="btn btn-secondary mt-3">Annuler</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
