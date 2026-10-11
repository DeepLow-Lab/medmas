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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Médecin</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div>
    <div>
        <div class="card-header text-center bg-primary text-white">
            <h3>Ajouter un Médecin</h3>
        </div>

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

                <button type="submit" class="btn btn-primary w-100">Ajouter</button>

            </form>
        </div>

        <div class="card-footer text-center">
            <a href="medecins.php" class="btn btn-secondary" style="margin-top:7px;">Retour</a>
        </div>
    </div>
</div>

</body>
</html>