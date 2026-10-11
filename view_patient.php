<?php
require_once "auth.php";
require_once "db.php";

// Vérifier si id du patient est présent
if (!isset($_GET['id'])) {
    header("Location: patients.php?error=missing_id");
    exit;
}

$id = intval($_GET['id']);

// Récupération des infos du patient
$patient_sql = $conn->prepare("SELECT * FROM patient WHERE no_dossier = ?");
$patient_sql->bind_param("i", $id);
$patient_sql->execute();
$patient = $patient_sql->get_result()->fetch_assoc();

if (!$patient) {
    header("Location: patients.php?error=not_found");
    exit;
}

// Récupération des consultations
$cons_sql = $conn->prepare("
    SELECT c.*, m.nom AS med_nom, m.prenom AS med_prenom 
    FROM consultation c
    JOIN medecin m ON c.code_medecin = m.code_medecin
    WHERE c.no_dossier = ?
");
$cons_sql->bind_param("i", $id);
$cons_sql->execute();
$consultations = $cons_sql->get_result();

// Récupération des prescriptions
$pres_sql = $conn->prepare("
    SELECT p.*, c.date_consultation 
    FROM prescription p
    JOIN consultation c ON p.id_consultation = c.id_consultation
    WHERE c.no_dossier = ?
");
$pres_sql->bind_param("i", $id);
$pres_sql->execute();
$prescriptions = $pres_sql->get_result();

// Récupération des rendez-vous
$rdv_sql = $conn->prepare("
    SELECT r.*, m.nom AS med_nom, m.prenom AS med_prenom
    FROM rendez_vous r
    JOIN medecin m ON r.code_medecin = m.code_medecin
    WHERE r.no_dossier = ?
");
$rdv_sql->bind_param("i", $id);
$rdv_sql->execute();
$rdvs = $rdv_sql->get_result();
?>

<?php include "includes/header.php"; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<div class="container mt-4">

    <a href="patients.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Retour</a>

    <h2 class="mb-3">Dossier du patient : <?= $patient['nom'] . " " . $patient['prenom'] ?></h2>

    <!-- Informations du patient -->
    <div class="card mb-4">
        <div class="card-header">Informations personnelles</div>
        <div class="card-body">
            <p><strong>No dossier :</strong> <?= $patient['no_dossier'] ?></p>
            <p><strong>Nom :</strong> <?= $patient['nom'] ?></p>
            <p><strong>Prénom :</strong> <?= $patient['prenom'] ?></p>
            <p><strong>Sexe :</strong> <?= $patient['sexe'] ?></p>
            <p><strong>Âge :</strong> <?= $patient['age'] ?></p>
            <p><strong>Téléphone :</strong> <?= $patient['telephone'] ?></p>
            <p><strong>Adresse :</strong> <?= $patient['adresse'] ?></p>

            <a href="edit_patient.php?id=<?= $patient['no_dossier'] ?>" class="btn btn-primary">Modifier</a>
        </div>
    </div>
    <br>

    <!-- Consultations -->
    <div class="card mb-4">
        <div class="card-header">Consultations</div>
        <div class="card-body">
            <br>
            <?php if ($consultations->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Médecin</th>
                            <th>Symptômes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $consultations->fetch_assoc()): ?>
                            <tr>
                                <td><?= $c['date_consultation'] ?></td>
                                <td><?= $c['med_prenom'] . " " . $c['med_nom'] ?></td>
                                <td><?= $c['symptome'] ?></td>
                                <td>
                                    <a href="view_consultation.php?id=<?= $c['id_consultation'] ?>" class="btn btn-info btn-sm">Voir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune consultation trouvée.</p>
            <?php endif; ?>
            <br>
            <a href="add_consultation.php?patient=<?= $patient['no_dossier'] ?>" class="btn btn-success mb-3">
                <i class="bi bi-plus-lg"></i> Nouvelle consultation
            </a>

        </div>
    </div>
            <br>
    <!-- Prescriptions -->
    <div class="card mb-4">
        <div class="card-header">Prescriptions</div>
        <div class="card-body">

            <?php if ($prescriptions->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date consultation</th>
                            <th>Ordonnance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $prescriptions->fetch_assoc()): ?>
                            <tr>
                                <td><?= $p['date_consultation'] ?></td>
                                <td><?= substr($p['ordonnance'], 0, 50) ?>...</td>
                                <td>
                                    <a href="view_prescription.php?id=<?= $p['id_prescription'] ?>" class="btn btn-info btn-sm">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune prescription trouvée.</p>
            <?php endif; ?>

        </div>
        
    </div>
                <br>
    <!-- Rendez-vous -->
    <div class="card mb-4">
        <div class="card-header">Rendez-vous</div>
        <br>
        <div class="card-body">

            <a href="add_appointment.php?patient=<?= $patient['no_dossier'] ?>" class="btn btn-success mb-3">
                <i class="bi bi-plus-lg"></i> Nouveau rendez-vous
            </a>

            <?php if ($rdvs->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Médecin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = $rdvs->fetch_assoc()): ?>
                            <tr>
                                <td><?= $r['date_rdv'] ?></td>
                                <td><?= $r['heure_rdv'] ?></td>
                                <td><?= $r['med_prenom'] . " " . $r['med_nom'] ?></td>
                                <td><?= $r['statut'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun rendez-vous trouvé.</p>
            <?php endif; ?>

        </div>
    </div>

</div>

<!-- <?php include "footer.php"; ?> -->
