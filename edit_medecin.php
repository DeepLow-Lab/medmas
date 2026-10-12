<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['code_medecin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: medecins.php");
    exit;
}

$id = $_GET['id'];

// Récupération du médecin
$stmt = $conn->prepare("SELECT * FROM medecin WHERE code_medecin = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Médecin introuvable.");
}

$success = $error = "";

// Mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom       = $_POST['nom'];
    $prenom    = $_POST['prenom'];
    $sexe      = $_POST['sexe'];
    $adresse   = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $email     = $_POST['email'];
    $spec      = $_POST['specialisation'];
    $admin     = isset($_POST['admin']) ? 1 : 0;

    // Vérifier si nouveau password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE medecin SET nom=?, prenom=?, sexe=?, adresse=?, telephone=?, email=?, specialisation=?, password=?, admin=? WHERE code_medecin=?");
        $update->bind_param("ssssssssii", $nom, $prenom, $sexe, $adresse, $telephone, $email, $spec, $password, $admin, $id);
    } else {
        // Sans changer mot de passe
        $update = $conn->prepare("UPDATE medecin SET nom=?, prenom=?, sexe=?, adresse=?, telephone=?, email=?, specialisation=?, admin=? WHERE code_medecin=?");
        $update->bind_param("sssssssii", $nom, $prenom, $sexe, $adresse, $telephone, $email, $spec, $admin, $id);
    }

    if ($update->execute()) {
        $success = "Médecin mis à jour avec succès.";
    } else {
        $error = "Erreur lors de la mise à jour.";
    }
}
?>
<?php include "includes/header.php"; ?>

<div class="container mt-4">
    <div class="page-toolbar">
        <h2><i class="bi bi-pencil-square"></i> Modifier Médecin</h2>
        <a href="medecins.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <div>

        <div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" value="<?= $data['nom'] ?>" class="form-control" required>
                    </div>

                    <div class="col">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" value="<?= $data['prenom'] ?>" class="form-control" required>
                    </div>
                </div>

                <label class="form-label">Sexe</label>
                <select name="sexe" class="form-select mb-3" required>
                    <option value="M" <?= $data['sexe']=='M'?'selected':'' ?>>Masculin</option>
                    <option value="F" <?= $data['sexe']=='F'?'selected':'' ?>>Féminin</option>
                </select>

                <label class="form-label">Adresse</label>
                <textarea name="adresse" class="form-control mb-3"><?= $data['adresse'] ?></textarea>

                <label class="form-label">Téléphone</label>
                <input type="text" name="telephone" value="<?= $data['telephone'] ?>" class="form-control mb-3">

                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?= $data['email'] ?>" class="form-control mb-3">

                <label class="form-label">Spécialisation</label>
                <input type="text" name="specialisation" value="<?= $data['specialisation'] ?>" class="form-control mb-3">

                <label class="form-label">Nouveau mot de passe (optionnel)</label>
                <input type="password" name="password" placeholder="Laisser vide pour ne pas changer" class="form-control mb-4">

                <div class="form-check mb-4">
                    <input type="checkbox" name="admin" class="form-check-input" <?= $data['admin']==1?'checked':'' ?>>
                    <label class="form-check-label">Administrateur</label>
                </div>

                <div class="d-flex align-items-center gap-2 mt-4">
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i> Mettre à jour</button>
                    <a href="medecins.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
