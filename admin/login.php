<?php
require_once '../includes/header.php';

// Si déjà connecté, on redirige vers le dashboard
if (isset($_SESSION['admin'])) {
    header('Location: dashboard.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/db.php';

    $login = $_POST['login'] ?? '';
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if ($login === '' || $mdp === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE login = ?");
        $stmt->execute([$login]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && $admin['mot_de_passe'] === hash('sha256', $mdp)) {
            $_SESSION['admin'] = $admin['login'];
            header('Location: dashboard.php');
            exit;
        } else {
            $erreur = 'Login ou mot de passe incorrect.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-dark text-white text-center">
                <h4 class="mb-0">Connexion Admin</h4>
            </div>
            <div class="card-body">
                <?php if ($erreur): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="login" class="form-label">Login</label>
                        <input type="text" class="form-control" id="login" 
                               name="login" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" 
                               id="mot_de_passe" name="mot_de_passe" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>