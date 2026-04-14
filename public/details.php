<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Vérification que l'id est bien passé dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: liste.php');
    exit;
}

$id = $_GET['id'];

// Récupération de l'équipement
$stmt = $pdo->prepare("
    SELECT e.*, c.nom AS categorie, 
           u.nom AS user_nom, u.prenom AS user_prenom, u.service,
           l.salle, l.bureau,
           pc.marque AS pc_marque, pc.modele AS pc_modele
    FROM equipement e
    JOIN categorie c ON e.id_categorie = c.id
    LEFT JOIN utilisateur u ON e.id_utilisateur = u.id
    LEFT JOIN localisation l ON e.id_localisation = l.id
    LEFT JOIN equipement pc ON e.id_pc = pc.id
    WHERE e.id = ?
");
$stmt->execute([$id]);
$eq = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'équipement n'existe pas, on redirige
if (!$eq) {
    header('Location: liste.php');
    exit;
}

// Si c'est un PC, on récupère ses composants
$composants = [];
if ($eq['id_categorie'] == 1) {
    $stmt2 = $pdo->prepare("
        SELECT e.*, c.nom AS categorie
        FROM equipement e
        JOIN categorie c ON e.id_categorie = c.id
        WHERE e.id_pc = ?
    ");
    $stmt2->execute([$id]);
    $composants = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?= htmlspecialchars($eq['marque'] . ' ' . $eq['modele']) ?></h2>
    <a href="liste.php" class="btn btn-outline-secondary">← Retour à la liste</a>
</div>

<div class="row">
    <!-- Informations principales -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">Informations générales</div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>Catégorie</th>
                        <td><?= htmlspecialchars($eq['categorie']) ?></td>
                    </tr>
                    <tr>
                        <th>Marque</th>
                        <td><?= htmlspecialchars($eq['marque']) ?></td>
                    </tr>
                    <tr>
                        <th>Modèle</th>
                        <td><?= htmlspecialchars($eq['modele']) ?></td>
                    </tr>
                    <tr>
                        <th>Numéro de série</th>
                        <td><?= htmlspecialchars($eq['num_serie'] ?? 'Non renseigné') ?></td>
                    </tr>
                    <tr>
                        <th>Date d'achat</th>
                        <td><?= $eq['date_achat'] ? date('d/m/Y', strtotime($eq['date_achat'])) : 'Non renseignée' ?></td>
                    </tr>
                    <tr>
                        <th>Prix</th>
                        <td><?= $eq['prix'] ? number_format($eq['prix'], 2, ',', ' ') . ' €' : 'Non renseigné' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Utilisateur et localisation -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">Affectation</div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>Utilisateur</th>
                        <td>
                            <?php if ($eq['user_nom']): ?>
                                <?= htmlspecialchars($eq['user_prenom'] . ' ' . $eq['user_nom']) ?>
                                <br><small class="text-muted"><?= htmlspecialchars($eq['service']) ?></small>
                            <?php else: ?>
                                <span class="text-muted">Non assigné</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Localisation</th>
                        <td>
                            <?php if ($eq['salle']): ?>
                                <?= htmlspecialchars($eq['salle']) ?>
                                <?php if ($eq['bureau']): ?>
                                    — <?= htmlspecialchars($eq['bureau']) ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Non définie</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ($eq['pc_marque']): ?>
                    <tr>
                        <th>PC associé</th>
                        <td>
                            <a href="detail.php?id=<?= $eq['id_pc'] ?>">
                                <?= htmlspecialchars($eq['pc_marque'] . ' ' . $eq['pc_modele']) ?>
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Composants du PC -->
<?php if (!empty($composants)): ?>
<div class="card mb-4">
    <div class="card-header bg-dark text-white">Composants installés</div>
    <div class="card-body">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Numéro de série</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($composants as $comp): ?>
                <tr>
                    <td><?= htmlspecialchars($comp['categorie']) ?></td>
                    <td><?= htmlspecialchars($comp['marque']) ?></td>
                    <td><?= htmlspecialchars($comp['modele']) ?></td>
                    <td><?= htmlspecialchars($comp['num_serie'] ?? '-') ?></td>
                    <td>
                        <a href="detail.php?id=<?= $comp['id'] ?>" class="btn btn-sm btn-info">Voir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Bouton modification (visible seulement si admin connecté) -->
<?php if (isset($_SESSION['admin'])): ?>
<a href="../admin/modifier.php?id=<?= $eq['id'] ?>" class="btn btn-warning">Modifier cet équipement</a>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>