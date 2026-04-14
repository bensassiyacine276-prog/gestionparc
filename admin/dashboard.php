<?php
require_once '../includes/header.php';

// Protection de la page : si pas connecté, on redirige
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/db.php';

// Quelques stats pour le dashboard
$nbEquipements = $pdo->query("SELECT COUNT(*) FROM equipement")->fetchColumn();
$nbUtilisateurs = $pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
$nbNonAssignes = $pdo->query("SELECT COUNT(*) FROM equipement WHERE id_utilisateur IS NULL")->fetchColumn();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tableau de bord</h2>
    <a href="../admin/ajouter.php" class="btn btn-success">+ Ajouter un équipement</a>
</div>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h1 class="display-4"><?= $nbEquipements ?></h1>
                <p class="text-muted mb-0">Équipements au total</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-success">
            <div class="card-body">
                <h1 class="display-4"><?= $nbUtilisateurs ?></h1>
                <p class="text-muted mb-0">Utilisateurs enregistrés</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h1 class="display-4"><?= $nbNonAssignes ?></h1>
                <p class="text-muted mb-0">Équipements non assignés</p>
            </div>
        </div>
    </div>
</div>

<!-- Liste avec actions admin -->
<?php
$equipements = $pdo->query("
    SELECT e.*, c.nom AS categorie, u.nom AS user_nom, u.prenom AS user_prenom
    FROM equipement e
    JOIN categorie c ON e.id_categorie = c.id
    LEFT JOIN utilisateur u ON e.id_utilisateur = u.id
    ORDER BY e.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Catégorie</th>
            <th>Utilisateur</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($equipements as $eq): ?>
        <tr>
            <td><?= htmlspecialchars($eq['marque']) ?></td>
            <td><?= htmlspecialchars($eq['modele']) ?></td>
            <td><?= htmlspecialchars($eq['categorie']) ?></td>
            <td>
                <?php if ($eq['user_nom']): ?>
                    <?= htmlspecialchars($eq['user_prenom'] . ' ' . $eq['user_nom']) ?>
                <?php else: ?>
                    <span class="text-muted">Non assigné</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="../public/detail.php?id=<?= $eq['id'] ?>" class="btn btn-sm btn-info">Voir</a>
                <a href="modifier.php?id=<?= $eq['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                <a href="supprimer.php?id=<?= $eq['id'] ?>" class="btn btn-sm btn-danger"
                   onclick="return confirm('Supprimer cet équipement ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>