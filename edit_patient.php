<?php
session_start();
require_once "db.php";

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    header("Location: patients.php?error=missing_id");
    exit;
}

$no_dossier = intval($_GET['id']);

// Récupérer les informations du patient
$stmt = $conn->prepare("SELECT * FROM patient WHERE no_dossier = ?");
$stmt->bind_param("i", $no_dossier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: patients.php?error=not_found");
    exit;
}

$patient = $result->fetch_assoc();
$error = "";

// Mettre à jour si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $sexe = trim($_POST['sexe'] ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');

    if (empty($nom) || empty($prenom) || empty($sexe) || empty($telephone)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        $update = $conn->prepare("
            UPDATE patient 
            SET nom = ?, prenom = ?, sexe = ?, date_naissance = ?, telephone = ?, adresse = ?
            WHERE no_dossier = ?
        ");
        $update->bind_param("ssssssi", $nom, $prenom, $sexe, $date_naissance, $telephone, $adresse, $no_dossier);

        if ($update->execute()) {
            header("Location: patients.php?success=updated");
            exit;
        } else {
            $error = "Erreur lors de la mise à jour : " . $conn->error;
        }
    }
}
?>

<?php include "includes/header.php"; ?>

<div class="container mt-4">
    <div class="page-toolbar">
        <h2 class="page-title"><i class="bi bi-pencil-square"></i> Modifier les informations du patient</h2>
        <a href="patients.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($patient['nom']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($patient['prenom']) ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Sexe <span class="text-danger">*</span></label>
                <select name="sexe" class="form-select" required>
                    <option value="M" <?= ($patient['sexe'] === 'M') ? 'selected' : '' ?>>Masculin</option>
                    <option value="F" <?= ($patient['sexe'] === 'F') ? 'selected' : '' ?>>Féminin</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($patient['date_naissance'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($patient['telephone']) ?>" required>
            </div>

            <div class="col-12">
                <label class="form-label">Adresse</label>
                <textarea name="adresse" class="form-control" rows="2"><?= htmlspecialchars($patient['adresse']) ?></textarea>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
            <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i> Enregistrer les modifications</button>
            <a href="patients.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>

<?php include "includes/footer.php"; ?>
