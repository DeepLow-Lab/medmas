<?php
session_start();
require 'db.php';

if (!isset($_SESSION['code_medecin'])) {
    header("Location: login.php");
    exit();
}

$totalPatients = $conn->query("SELECT COUNT(*) AS total FROM patient")->fetch_assoc()['total'];
$totalConsultations = $conn->query("SELECT COUNT(*) AS total FROM consultation")->fetch_assoc()['total'];
$totalRdv = $conn->query("SELECT COUNT(*) AS total FROM rendez_vous")->fetch_assoc()['total'];

$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$is_admin = $_SESSION['is_admin'];

include "includes/header.php";
?>

<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <div class="eyebrow">Vue d’ensemble</div>
            <h2>Bonjour Dr. <?= htmlspecialchars($prenom . " " . $nom) ?></h2>
        </div>
        <span class="role-label"><?= $is_admin ? "Administrateur" : "Médecin" ?></span>
    </div>

    <div class="dashboard-grid">
        <div class="card dashboard-stat stat-card">
            <h4><i class="bi bi-people"></i> Patients</h4>
            <h2><?= $totalPatients ?></h2>
            <a href="patients.php" class="btn btn-sm btn-primary">Ouvrir les dossiers</a>
        </div>
        <div class="card dashboard-stat stat-card">
            <h4><i class="bi bi-clipboard2-pulse"></i> Consultations</h4>
            <h2><?= $totalConsultations ?></h2>
            <a href="consultations.php" class="btn btn-sm btn-primary">Voir les consultations</a>
        </div>
        <div class="card dashboard-stat stat-card">
            <h4><i class="bi bi-calendar2-week"></i> Rendez-vous</h4>
            <h2><?= $totalRdv ?></h2>
            <a href="appointments.php" class="btn btn-sm btn-primary">Voir le planning</a>
        </div>
    </div>

    <section class="quick-actions mt-4">
        <div class="page-toolbar">
            <h3>Accès rapides</h3>
        </div>
        <div class="quick-action-list">
            <a href="add_patient.php"><i class="bi bi-person-plus"></i><span>Créer un dossier patient</span><i class="bi bi-arrow-right"></i></a>
            <a href="add_consultation.php"><i class="bi bi-clipboard2-plus"></i><span>Enregistrer une consultation</span><i class="bi bi-arrow-right"></i></a>
            <a href="add_appointment.php"><i class="bi bi-calendar2-plus"></i><span>Planifier un rendez-vous</span><i class="bi bi-arrow-right"></i></a>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
