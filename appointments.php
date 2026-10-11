<?php
require 'auth.php';
require 'db.php';
include 'includes/header.php';

// id du medecin connecté (nom utilisé précédemment : medecin_id ou code_medecin ?)
$medecin_id = $_SESSION['medecin_id'] ?? $_SESSION['code_medecin'] ?? null;
if (!$medecin_id) {
    echo "<div class='container'>Erreur : médecin non identifié en session.</div>";
    include 'includes/footer.php';
    exit;
}

// Récupérer les rendez-vous du médecin connecté
$sql = "
SELECT r.id_rdv, r.date_rdv, r.heure_rdv, r.statut, r.no_dossier,
       p.nom AS patient_nom, p.prenom AS patient_prenom
FROM rendez_vous r
JOIN patient p ON r.no_dossier = p.no_dossier
WHERE r.code_medecin = ?
ORDER BY r.date_rdv ASC, r.heure_rdv ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $medecin_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <div class="page-toolbar">
        <h2><i class="bi bi-calendar2-week"></i> Rendez-vous</h2>
        <a href="add_appointment.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nouveau rendez-vous</a>
    </div>

    <div class="table-wrap"><table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Patient</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="5" class="text-center">Aucun rendez-vous.</td></tr>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['date_rdv']) ?></td>
                    <td><?= htmlspecialchars($row['heure_rdv']) ?></td>
                    <td><?= htmlspecialchars($row['patient_prenom'] . ' ' . $row['patient_nom']) ?></td>
                    <td><?= htmlspecialchars($row['statut']) ?></td>
                    <td>
                        <a href="view_appointment.php?id=<?= $row['id_rdv'] ?>" class="btn-small">Voir</a>

                        <?php if ($row['statut'] === 'en_attente'): ?>
                            <a href="mark_done.php?id=<?= $row['id_rdv'] ?>" 
                               class="btn-small" 
                               onclick="return confirm('Marquer ce rendez-vous comme effectué ?');">
                                ✓ Effectué
                            </a>
                        <?php endif; ?>

                        <a href="delete_appointment.php?id=<?= $row['id_rdv'] ?>"
                           class="btn-small btn-danger"
                           onclick="return confirm('Supprimer ce rendez-vous ?');">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table></div>
</div>

<?php include 'includes/footer.php'; ?>
