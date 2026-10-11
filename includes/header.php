<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MEDMAS | Gestion clinique</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="assets/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <?php include 'sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <button class="btn-burger" id="toggleSidebar" aria-label="Réduire le menu"><i class="bi bi-layout-sidebar-inset"></i></button>
             <h2>Gestion clinique MEDMAS</h2>
            
            <div class="user-info">

                <!-- Bouton Dark Mode -->
                <button class="theme-toggle theme-btn" aria-label="Changer de thème"><i class="bi bi-moon-stars"></i></button>

                <!-- Nom de l'utilisateur -->
                <span>
                    <i class="bi bi-person-badge"></i> <?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? 'Utilisateur')) ?>
                </span>

                <!-- Logout -->
                <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
            </div>
        </div>
<script src="assets/js/main.js" defer></script>