<?php
require_once '../includes/header.php';
require_once '../config/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Récupération des listes pour les menus déroulants
$categories   = $pdo->query("SELECT * FROM categorie")->fetchAll(PDO::FETCH_ASSOC);
$utilisateurs = $pdo->query("SELECT * FROM utilisateur ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$localisations = $pdo->query("SELECT * FROM localisation ORDER BY salle")->fetchAll(PDO::FETCH_ASSOC);
$pcs = $pdo->query("SELECT * FROM equipement WHERE id_categorie = 1 ORDER BY marque")->fetchAll(PDO::FETCH_ASSOC);

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque         = trim($_POST['marque'] ?? '');
    $modele         = trim($_POST['modele'] ?? '');
    $num_serie      = trim($_POST['num_serie'] ?? '');
    $date_achat     = $_POST['date_achat'] ?? null;
    $prix           = $_POST['prix'] ?? null;
    $id_categorie   = $_POST['id_categorie'] ?? null;
    $id_utilisateur = $_POST['id_utilisateur'] ?: null;
    $id_localisation = $_POST['id_localisation'] ?: null;
    $id_pc          = $_POST['id_pc'] ?: null;

    if (!$marque || !$modele || !$id_categorie) {
        $erreur = 'Marque, modèle et catégorie sont obligatoires.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO equipement 
            (marque, modele, num_serie, date_achat, prix, id_categorie, id_utilisateur, id_localisation, id_pc)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $marque, $modele, $num_serie,
            $date_achat ?: null,
            $prix ?: null,
            $id_categorie, $id_utilisateur, $id_localisation, $id_pc
        ]);
        $succes = 'Équipement ajouté avec succès !';
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Ajouter un équipement</h2>
    <a href="dashboard.php" class="btn btn-outline-secondary">← Retour</a>
</div>

<?php if ($erreur): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>
<?php if ($succes): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Marque *</label>
                    <input type="text" name="marque" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Modèle *</label>
                    <input type="text" name="modele" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Catégorie *</label>
                    <select name="id_categorie" class="form-select" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Numéro de série</label>
                    <input type="text" name="num_serie" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date d'achat</label>
                    <input type="date" name="date_achat" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prix (€)</label>
                    <input type="number" step="0.01" name="prix" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Utilisateur</label>
                    <select name="id_utilisateur" class="form-select">
                        <option value="">-- Non assigné --</option>
                        <?php foreach ($utilisateurs as $u): ?>
                            <option value="<?= $u['id'] ?>">
                                <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Localisation</label>
                    <select name="id_localisation" class="form-select">
                        <option value="">-- Non définie --</option>
                        <?php foreach ($localisations as $loc): ?>
                            <option value="<?= $loc['id'] ?>">
                                <?= htmlspecialchars($loc['salle'] . ' - ' . $loc['bureau']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">PC associé (si composant)</label>
                <select name="id_pc" class="form-select">
                    <option value="">-- Aucun --</option>
                    <?php foreach ($pcs as $pc): ?>
                        <option value="<?= $pc['id'] ?>">
                            <?= htmlspecialchars($pc['marque'] . ' ' . $pc['modele']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Ajouter l'équipement</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>