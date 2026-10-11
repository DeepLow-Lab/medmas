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

<div class="container mt-5">
    <div class="page-toolbar">
        <h2><i class="bi bi-person-vcard"></i> Médecins</h2>
        <a href="add_medecin.php" class="btn btn-primary"><i class="bi bi-person-plus"></i> Ajouter un médecin</a>
    </div>

    <div>
        <div>

            <div class="table-wrap"><table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Spécialisation</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['code_medecin'] ?></td>
                        <td><?= $row['nom'] ?></td>
                        <td><?= $row['prenom'] ?></td>
                        <td><?= $row['specialisation'] ?></td>
                        <td><?= $row['telephone'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td>
                            <?php if ($row['is_admin'] == 1): ?>
                                <span class="badge bg-success">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_medecin.php?id=<?= $row['code_medecin'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <br>
                            <a href="delete.php?id=<?= $row['code_medecin'] ?>" 
                               onclick="return confirm('Voulez-vous vraiment supprimer ce médecin ?');"
                               class="btn btn-danger btn-sm">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>

            </table></div>

        </div>
    </div>

    <div class="mt-3">
        <a href="dashboard.php" class="btn btn-secondary">Retour au Dashboard</a>
    </div>

</div>

</body>
</html>
