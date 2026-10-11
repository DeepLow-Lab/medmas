<?php
session_start();
require 'db.php';

// Redirection si non connecté
if (!isset($_SESSION['code_medecin'])) {
    header("Location: login.php");
    exit();
}

// Gestion recherche
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = $conn->prepare("SELECT * FROM patient WHERE nom LIKE ? OR prenom LIKE ? OR no_dossier LIKE ?");
    $like = "%$search%";
    $query->bind_param("sss", $like, $like, $like);
    $query->execute();
    $result = $query->get_result();
} else {
    $result = $conn->query("SELECT * FROM patient ORDER BY nom ASC");
}

?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">

    <!-- Header -->
    <div class="page-toolbar">
        <h2 class="page-title"><i class="bi bi-people"></i> Patients</h2>
        <div>
            <a href="add_patient.php" class="btn btn-primary"><i class="bi bi-person-plus"></i> Ajouter un patient</a>
            <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
        </div>
    </div>

    <!-- Barre de recherche -->
    <form class="input-group mb-3" method="GET" id="search">
        <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, prénom ou dossier..."
               value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Rechercher</button>
    </form>

    <!-- Tableau -->
            <div class="table-wrap"><table>
                <thead>
                <tr>
                    <th>No dossier</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Sexe</th>
                    <th>Téléphone</th>
                    <th>Âge</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['no_dossier'] ?></td>
                            <td><?= $row['nom'] ?></td>
                            <td><?= $row['prenom'] ?></td>
                            <td><?= $row['sexe'] ?></td>
                            <td><?= $row['telephone'] ?></td>
                            <td><?= $row['age'] ?></td>
                            <td>
                                <a href="view_patient.php?id=<?= $row['no_dossier'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Voir</a>
                                <a href="edit_patient.php?id=<?= $row['no_dossier'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Modifier</a>
                                <a href="delete.php?id=<?= $row['no_dossier'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Supprimer ce patient ? Cette action est irréversible.');">
                                    <i class="bi bi-trash3"></i> Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Aucun patient trouvé.</td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table></div>

</div>

</body>
</html>
