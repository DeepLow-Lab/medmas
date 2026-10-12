<?php
session_start();
require_once "db.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['code_medecin'])) {
    header("Location: login.php");
    exit;
}

$result = $conn->query("SELECT * FROM medecin ORDER BY nom ASC");
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="page-toolbar">
        <h2><i class="bi bi-person-vcard"></i> Médecins</h2>
        <div class="d-flex align-items-center gap-2">
            <a href="add_medecin.php" class="btn btn-primary"><i class="bi bi-person-plus"></i> Ajouter un médecin</a>
            <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Spécialisation</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th style="min-width: 190px;">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['code_medecin']) ?></td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['prenom']) ?></td>
                        <td><?= htmlspecialchars($row['specialisation']) ?></td>
                        <td><?= htmlspecialchars($row['telephone']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <?php if ($row['is_admin'] == 1): ?>
                                <span class="badge bg-success">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="edit_medecin.php?id=<?= $row['code_medecin'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                <a href="delete.php?id=<?= $row['code_medecin'] ?>" 
                                   onclick="return confirm('Voulez-vous vraiment supprimer ce médecin ?');"
                                   class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash3"></i> Supprimer
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">Aucun médecin trouvé.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
