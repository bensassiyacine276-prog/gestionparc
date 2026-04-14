<?php
require_once '../includes/header.php';
require_once '../config/db.php';
?>

<h2 class="mb-4">Liste des équipements</h2>

<!-- Filtres par catégorie -->
<div class="mb-3">
    <a href="liste.php" class="btn btn-outline-secondary btn-sm">Tous</a>
    <a href="liste.php?categorie=1" class="btn btn-outline-primary btn-sm">PCs</a>
    <a href="liste.php?categorie=2" class="btn btn-outline-primary btn-sm">Écrans</a>
    <a href="liste.php?categorie=3" class="btn btn-outline-primary btn-sm">Claviers & Souris</a>
    <a href="liste.php?categorie=4" class="btn btn-outline-primary btn-sm">Imprimantes</a>
    <a href="liste.php?categorie=5" class="btn btn-outline-primary btn-sm">Composants</a>
</div>

<?php
// Construction de la requête selon le filtre
if (isset($_GET['categorie']) && is_numeric($_GET['categorie'])) {
    $stmt = $pdo->prepare("
        SELECT e.*, c.nom AS categorie, u.nom AS user_nom, u.prenom AS user_prenom, l.salle
        FROM equipement e
        JOIN categorie c ON e.id_categorie = c.id
        LEFT JOIN utilisateur u ON e.id_utilisateur = u.id
        LEFT JOIN localisation l ON e.id_localisation = l.id
        WHERE e.id_categorie = ?
        ORDER BY e.marque, e.modele
    ");
    $stmt->execute([$_GET['categorie']]);
} else {
    $stmt = $pdo->query("
        SELECT e.*, c.nom AS categorie, u.nom AS user_nom, u.prenom AS user_prenom, l.salle
        FROM equipement e
        JOIN categorie c ON e.id_categorie = c.id
        LEFT JOIN utilisateur u ON e.id_utilisateur = u.id
        LEFT JOIN localisation l ON e.id_localisation = l.id
        ORDER BY e.marque, e.modele
    ");
}

$equipements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($equipements) === 0): ?>
    <div class="alert alert-info">Aucun équipement trouvé.</div>
<?php else: ?>
<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Catégorie</th>
            <th>Numéro de série</th>
            <th>Utilisateur</th>
            <th>Salle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($equipements as $eq): ?>
        <tr>
            <td><?= htmlspecialchars($eq['marque']) ?></td>
            <td><?= htmlspecialchars($eq['modele']) ?></td>
            <td><?= htmlspecialchars($eq['categorie']) ?></td>
            <td><?= htmlspecialchars($eq['num_serie']) ?></td>
            <td>
                <?php if ($eq['user_nom']): ?>
                    <?= htmlspecialchars($eq['user_prenom'] . ' ' . $eq['user_nom']) ?>
                <?php else: ?>
                    <span class="text-muted">Non assigné</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($eq['salle'] ?? 'Non défini') ?></td>
            <td>
                <a href="detail.php?id=<?= $eq['id'] ?>" class="btn btn-sm btn-info">Voir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>