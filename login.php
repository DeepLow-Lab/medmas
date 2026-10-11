<?php
session_start();
 require 'db.php';

// SI FORMULAIRE ENVOYÉ
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Recherche du médecin
    $sql = "SELECT * FROM medecin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $medecin = $result->fetch_assoc();

        // Vérifie mot de passe
        if (password_verify($password, $medecin['passcode'])) {

            // Création de la session
            $_SESSION['code_medecin'] = $medecin['code_medecin'];
            $_SESSION['nom'] = $medecin['nom'];
            $_SESSION['prenom'] = $medecin['prenom'];
            $_SESSION['is_admin'] = $medecin['is_admin'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Aucun utilisateur trouvé avec cet email.";
    }
}
?> 

<!DOCTYPE html>
<html>
<head>
    <title>Connexion | MEDMAS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">

            <div class="card shadow-lg p-4">
                <div class="text-center mb-3 text-primary"><i class="bi bi-shield-lock fs-1"></i></div>
                <h3 class="text-center">Accès professionnel</h3>
                <p class="text-muted text-center">Accédez à votre tableau de bord</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label>Email :</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Mot de passe :</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right"></i> Se connecter</button>
                </form>

            </div>

        </div>
    </div>
</div>

</body>
</html>
