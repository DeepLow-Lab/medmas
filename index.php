<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue - Polyclinique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: radial-gradient(circle at 10% 10%, #dff5f3, transparent 35%), linear-gradient(135deg, #eaf3f8, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-card {
            max-width: 720px;
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 18px 50px rgba(18, 48, 74, .13);
            animation: rise-in .6s ease both;
        }

        .logo-img {
            width: 120px;
            margin-bottom: 20px;
        }

        .tagline {
            font-size: 1.2rem;
            color: #555;
            margin-top: 10px;
            font-style: italic;
        }
    </style>
</head>

<body>

<div class="welcome-card">

    <!-- LOGO -->
    <img src="assets/MEDMAS_black.png" alt="Logo Polyclinique" class="logo-img">

    <!-- TITRE -->
    <div class="mb-3 text-primary"><i class="bi bi-heart-pulse fs-1"></i></div>
    <h1 class="fw-bold">Système de gestion clinique</h1>

    <!-- SLOGAN -->
    <p class="tagline">« Pour une prise en charge moderne, rapide et efficace »</p>

    <!-- DESCRIPTION -->
    <p class="mt-3 text-muted">
        Bienvenue sur notre plateforme numérique dédiée à la gestion professionnelle des patients,
        consultations, médecins et prescriptions.  
        Une interface simple, rapide et sécurisée au service de la santé.
        <br><br>
        <em>Powered by <a href="https://deeplowlab.tech" target="_blank" rel="noopener">DeepLow Lab / Shoal</a></em>
    </p>
    
    <!-- BOUTON LOGIN -->
    <a href="login.php" class="btn btn-primary btn-lg mt-4 px-5">
        <i class="bi bi-box-arrow-in-right"></i> Se connecter
    </a>

</div>

</body>
</html>
