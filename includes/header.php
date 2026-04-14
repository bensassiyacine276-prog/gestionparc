<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Parc Informatique</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/public/index.php">Parc Info</a>
        <div class="navbar-nav">
            <a class="nav-link" href="/public/liste.php">Équipements</a>
            <a class="nav-link" href="/recherche.php">Recherche</a>
            <?php if (isset($_SESSION['admin'])): ?>
                <a class="nav-link" href="/admin/dashboard.php">Admin</a>
                <a class="nav-link" href="/admin/logout.php">Déconnexion</a>
            <?php else: ?>
                <a class="nav-link" href="/admin/login.php">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">