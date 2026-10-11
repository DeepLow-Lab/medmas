<?php
require_once 'auth.php';
include 'includes/header.php';
include 'db.php';

// Récupération des prescriptions
$sql = "SELECT p.id_prescription, p.ordonnance, c.date_consultation, 
               pat.nom AS patient_nom, pat.prenom AS patient_prenom,
               m.nom AS medecin_nom
        FROM prescription p
        INNER JOIN consultation c ON p.id_consultation = c.id_consultation
        INNER JOIN patient pat ON c.no_dossier = pat.no_dossier
        INNER JOIN medecin m ON c.code_medecin = m.code_medecin
        ORDER BY p.id_prescription DESC";

$result = $conn->query($sql);
?>

<div class="container">
    <div class="page-toolbar">
        <h2><i class="bi bi-file-medical"></i> Prescriptions</h2>
        <a href="add_prescription.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouvelle prescription</a>
    </div>

    <div class="table-wrap"><table>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Médecin</th>
            <th>Date Consultation</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_prescription'] ?></td>
            <td><?= htmlspecialchars($row['patient_nom'] . " " . $row['patient_prenom']) ?></td>
            <td>Dr. <?= htmlspecialchars($row['medecin_nom']) ?></td>
            <td><?= htmlspecialchars($row['date_consultation']) ?></td>
            <td>
                <a class="btn-small" href="view_prescription.php?id=<?= $row['id_prescription'] ?>">Voir</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table></div>
</div>

<?php include 'includes/footer.php'; ?>
