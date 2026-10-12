<?php
session_start();
require_once "db.php";

// Vérification login
if (!isset($_SESSION['code_medecin'])) {
    header("Location: login.php");
    exit();
}

$error = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $no_dossier = trim($_POST['no_dossier'] ?? '');
    $nom        = trim($_POST['nom'] ?? '');
    $prenom     = trim($_POST['prenom'] ?? '');
    $sexe       = trim($_POST['sexe'] ?? '');
    $telephone  = trim($_POST['telephone'] ?? '');
    $adresse    = trim($_POST['adresse'] ?? '');
    $age        = trim($_POST['age'] ?? '');

    // Validation simple
    if (empty($no_dossier) || empty($nom) || empty($prenom) || empty($sexe) || empty($telephone) || empty($age)) {
        $error = "Veuillez remplir tous les champs obligatoires marqués d'une étoile (*).";
    } else {
        // Vérifier si le numéro de dossier existe déjà
        $check = $conn->prepare("SELECT no_dossier FROM patient WHERE no_dossier = ?");
        $check->bind_param("s", $no_dossier);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Ce numéro de dossier existe déjà dans le système.";
        } else {
            // Insertion
            $stmt = $conn->prepare("
                INSERT INTO patient (no_dossier, nom, prenom, sexe, telephone, adresse, age)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssssssi", $no_dossier, $nom, $prenom, $sexe, $telephone, $adresse, $age);

            if ($stmt->execute()) {
                header("Location: patients.php?added=1");
                exit();
            } else {
                $error = "Erreur lors de l’ajout du patient : " . $conn->error;
            }
        }
    }
}
?>

<?php include "includes/header.php"; ?>

<div class="container mt-4">
    <div class="page-toolbar">
        <h2 class="page-title"><i class="bi bi-person-plus"></i> Créer un dossier patient</h2>
        <a href="patients.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="add_patient.php">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">No Dossier <span class="text-danger">*</span></label>
                <input type="text" name="no_dossier" class="form-control" placeholder="Ex: P-1001" 
                       value="<?= htmlspecialchars($_POST['no_dossier'] ?? '') ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control" placeholder="Nom de famille" 
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" name="prenom" class="form-control" placeholder="Prénom" 
                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Sexe <span class="text-danger">*</span></label>
                <select name="sexe" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    <option value="M" <?= (($_POST['sexe'] ?? '') === 'M') ? 'selected' : '' ?>>Masculin</option>
                    <option value="F" <?= (($_POST['sexe'] ?? '') === 'F') ? 'selected' : '' ?>>Féminin</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Âge <span class="text-danger">*</span></label>
                <input type="number" name="age" class="form-control" placeholder="Ex: 35" min="0" max="130" 
                       value="<?= htmlspecialchars($_POST['age'] ?? '') ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                <input type="tel" name="telephone" class="form-control" placeholder="Ex: +509 3123-4567" 
                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" required>
            </div>

            <div class="col-12">
                <label class="form-label">Adresse</label>
                <textarea name="adresse" class="form-control" rows="2" placeholder="Adresse complète du patient"><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer le patient</button>
            <a href="patients.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>

<?php include "includes/footer.php"; ?>
