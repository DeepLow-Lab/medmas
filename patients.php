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
    $search = trim($_GET['search']);
    $query = $conn->prepare("SELECT * FROM patient WHERE nom LIKE ? OR prenom LIKE ? OR no_dossier LIKE ? ORDER BY nom ASC");
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

    <!-- Header fiks / ki pa bouje pou chèche oswa ajoute nenpòt kilè -->
    <div class="patients-sticky-header">
        <div class="page-toolbar mb-3 pb-0 border-0">
            <h2 class="page-title"><i class="bi bi-people"></i> Patients</h2>
            <div class="d-flex align-items-center gap-2">
                <a href="add_patient.php" class="btn btn-primary"><i class="bi bi-person-plus"></i> Ajouter un patient</a>
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
            </div>
        </div>

        <!-- Barre de recherche -->
        <form class="input-group" method="GET" id="searchForm">
            <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="search" id="searchInput" class="form-control" 
                   placeholder="Rechercher par nom, prénom ou no dossier en direct..."
                   value="<?= htmlspecialchars($search) ?>" autocomplete="off">
            <button type="submit" class="btn btn-primary">Rechercher</button>
            <?php if (!empty($search)): ?>
                <a href="patients.php" class="btn btn-secondary" title="Réinitialiser la recherche"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tableau scrollable avec entête figée -->
    <div class="table-wrap scrollable-table">
        <table>
            <thead>
            <tr>
                <th>No dossier</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Sexe</th>
                <th>Téléphone</th>
                <th>Âge</th>
                <th style="min-width: 250px;">Actions</th>
            </tr>
            </thead>

            <tbody id="patientsTableBody">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['no_dossier']) ?></td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['prenom']) ?></td>
                        <td><?= htmlspecialchars($row['sexe']) ?></td>
                        <td><?= htmlspecialchars($row['telephone']) ?></td>
                        <td><?= htmlspecialchars($row['age']) ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="view_patient.php?id=<?= $row['no_dossier'] ?>" class="btn btn-sm btn-info" title="Voir">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                                <a href="edit_patient.php?id=<?= $row['no_dossier'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                <a href="delete.php?id=<?= $row['no_dossier'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Supprimer ce patient ? Cette action est irréversible.');"
                                   title="Supprimer">
                                    <i class="bi bi-trash3"></i> Supprimer
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr id="emptyRow">
                    <td colspan="7" class="text-center text-muted">Aucun patient trouvé.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('patientsTableBody');
    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = tableBody.querySelectorAll('tr:not(#emptyRow)');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matches = text.includes(query);
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        let noMatchRow = document.getElementById('noRealtimeMatchRow');
        if (visibleCount === 0 && rows.length > 0) {
            if (!noMatchRow) {
                noMatchRow = document.createElement('tr');
                noMatchRow.id = 'noRealtimeMatchRow';
                noMatchRow.innerHTML = '<td colspan="7" class="text-center text-muted">Aucun patient correspondant au filtre.</td>';
                tableBody.appendChild(noMatchRow);
            }
            noMatchRow.style.display = '';
        } else if (noMatchRow) {
            noMatchRow.style.display = 'none';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
