<?php
require_once "auth.php";
require_once "db.php";

if (empty($_SESSION['is_admin'])) {
    header("Location: dashboard.php?error=no_access");
    exit;
}

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom       = $_POST['nom'];
    $prenom    = $_POST['prenom'];
    $sexe      = $_POST['sexe'];
    $adresse   = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $email     = $_POST['email'];
    $spec      = $_POST['specialisation'];
    $password  = $_POST['password'];
    $admin     = 0;

    // Vérifier e-mail unique
    $check = $conn->prepare("SELECT code_medecin FROM medecin WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "Un médecin avec cet email existe déjà.";
    } else {

        // Hash du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertion
        $stmt = $conn->prepare("INSERT INTO medecin (nom, prenom, sexe, adresse, telephone, email, specialisation, passcode, is_admin)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Correction ici : ajout du type de paramètre
        $stmt->bind_param("ssssssssi", $nom, $prenom, $sexe, $adresse, $telephone, $email, $spec, $hashed_password, $admin);

        if ($stmt->execute()) {
            $success = "Médecin ajouté avec succès.";
        } else {
            $error = "Erreur lors de l'ajout du médecin.";
        }
    }
}
?>
<?php include "includes/header.php"; ?>

<div class="container mt-4">
    <div class="page-toolbar">
        <h2><i class="bi bi-person-plus"></i> Ajouter un Médecin</h2>
        <a href="medecins.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <div>
        <div>

        <div class="card-body">
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
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="col">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sexe</label>
                    <select name="sexe" class="form-select" required>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea name="adresse" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Spécialisation</label>
                    <input type="text" name="specialisation" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-check mb-4" class="check">
                    <input type="checkbox" name="admin" class="form-check-input">
                    <label class="form-check-label">Administrateur (accès complet)</label>
                </div>

                <div class="d-flex align-items-center gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Ajouter</button>
                    <a href="medecins.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>