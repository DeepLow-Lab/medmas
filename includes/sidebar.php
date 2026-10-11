<!-- sidebar.php -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <img src="assets/MEDMAS.png" alt="Logo Polyclinique" class="logo-img" height="70px">
    </div>

    <a href="dashboard.php"><i class="bi bi-grid-1x2 nav-icon"></i><span class="label">Tableau de bord</span></a>
    <a href="patients.php"><i class="bi bi-people nav-icon"></i><span class="label">Patients</span></a>
    <a href="consultations.php"><i class="bi bi-clipboard2-pulse nav-icon"></i><span class="label">Consultations</span></a>
    <a href="prescriptions.php"><i class="bi bi-file-medical nav-icon"></i><span class="label">Prescriptions</span></a>
    <a href="appointments.php"><i class="bi bi-calendar2-week nav-icon"></i><span class="label">Rendez-vous</span></a>

    <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <a href="add_medecin.php"><i class="bi bi-person-plus nav-icon"></i><span class="label">Ajouter un médecin</span></a>
        <a href="medecins.php"><i class="bi bi-person-vcard nav-icon"></i><span class="label">Médecins</span></a>
    <?php endif; ?>
    <!-- Bouton Dark Mode -->
    <button class="theme-toggle theme-btn" aria-label="Changer de thème"><i class="bi bi-moon-stars nav-icon"></i><span class="label">Mode sombre</span></button>
    <a href="logout.php" class="mt-4 text-warning"><i class="bi bi-box-arrow-right nav-icon"></i><span class="label">Déconnexion</span></a>
</div>
<!-- <script>
  document.getElementById('toggleSidebar').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    // Vérifier si la sidebar est visible
    if (sidebar.style.display === 'none' || !sidebar.style.display) {
      sidebar.style.display = 'block'; // afficher
    } else {
      sidebar.style.display = 'none'; // cacher
    }
  });
</script> -->