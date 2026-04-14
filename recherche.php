php
require_once 'includesheader.php';
require_once 'configdb.php';

 Récupération des listes pour les filtres
$categories    = $pdo-query(SELECT  FROM categorie)-fetchAll(PDOFETCH_ASSOC);
$utilisateurs  = $pdo-query(SELECT  FROM utilisateur ORDER BY nom)-fetchAll(PDOFETCH_ASSOC);
$localisations = $pdo-query(SELECT  FROM localisation ORDER BY salle)-fetchAll(PDOFETCH_ASSOC);

 Récupération des critères saisis
$marque          = trim($_GET['marque']  '');
$modele          = trim($_GET['modele']  '');
$id_categorie    = $_GET['id_categorie']  '';
$id_utilisateur  = $_GET['id_utilisateur']  '';
$id_localisation = $_GET['id_localisation']  '';
$date_min        = $_GET['date_min']  '';
$date_max        = $_GET['date_max']  '';

 Construction dynamique de la requête
$conditions = [];
$params     = [];

if ($marque !== '') {
    $conditions[] = e.marque LIKE ;
    $params[] = %$marque%;
}
if ($modele !== '') {
    $conditions[] = e.modele LIKE ;
    $params[] = %$modele%;
}
if ($id_categorie !== '') {
    $conditions[] = e.id_categorie = ;
    $params[] = $id_categorie;
}
if ($id_utilisateur !== '') {
    $conditions[] = e.id_utilisateur = ;
    $params[] = $id_utilisateur;
}
if ($id_localisation !== '') {
    $conditions[] = e.id_localisation = ;
    $params[] = $id_localisation;
}
if ($date_min !== '') {
    $conditions[] = e.date_achat = ;
    $params[] = $date_min;
}
if ($date_max !== '') {
    $conditions[] = e.date_achat = ;
    $params[] = $date_max;
}

 On n'exécute la recherche que si au moins un critère est rempli
$resultats = [];
$rechercheLancee = !empty($conditions);

if ($rechercheLancee) {
    $sql = 
        SELECT e., c.nom AS categorie,
               u.nom AS user_nom, u.prenom AS user_prenom,
               l.salle
        FROM equipement e
        JOIN categorie c ON e.id_categorie = c.id
        LEFT JOIN utilisateur u ON e.id_utilisateur = u.id
        LEFT JOIN localisation l ON e.id_localisation = l.id
        WHERE  . implode(' AND ', $conditions) . 
        ORDER BY e.marque, e.modele
    ;
    $stmt = $pdo-prepare($sql);
    $stmt-execute($params);
    $resultats = $stmt-fetchAll(PDOFETCH_ASSOC);
}


h2 class=mb-4Recherche multicritèreh2

div class=card mb-4
    div class=card-body
        form method=GET action=
            div class=row
                div class=col-md-4 mb-3
                    label class=form-labelMarquelabel
                    input type=text name=marque class=form-control
                           value== htmlspecialchars($marque)  placeholder=Ex Dell, HP...
                div
                div class=col-md-4 mb-3
                    label class=form-labelModèlelabel
                    input type=text name=modele class=form-control
                           value== htmlspecialchars($modele)  placeholder=Ex Latitude 5520...
                div
                div class=col-md-4 mb-3
                    label class=form-labelCatégorielabel
                    select name=id_categorie class=form-select
                        option value=-- Toutes --option
                        php foreach ($categories as $cat) 
                            option value== $cat['id'] 
                                = $cat['id'] == $id_categorie  'selected'  '' 
                                = htmlspecialchars($cat['nom']) 
                            option
                        php endforeach; 
                    select
                div
            div
            div class=row
                div class=col-md-4 mb-3
                    label class=form-labelUtilisateurlabel
                    select name=id_utilisateur class=form-select
                        option value=-- Tous --option
                        php foreach ($utilisateurs as $u) 
                            option value== $u['id'] 
                                = $u['id'] == $id_utilisateur  'selected'  '' 
                                = htmlspecialchars($u['prenom'] . ' ' . $u['nom']) 
                            option
                        php endforeach; 
                    select
                div
                div class=col-md-4 mb-3
                    label class=form-labelLocalisationlabel
                    select name=id_localisation class=form-select
                        option value=-- Toutes --option
                        php foreach ($localisations as $loc) 
                            option value== $loc['id'] 
                                = $loc['id'] == $id_localisation  'selected'  '' 
                                = htmlspecialchars($loc['salle'] . ' - ' . $loc['bureau']) 
                            option
                        php endforeach; 
                    select
                div
                div class=col-md-4 mb-3
                    label class=form-labelDate d'achat entrelabel
                    div class=input-group
                        input type=date name=date_min class=form-control
                               value== htmlspecialchars($date_min) 
                        span class=input-group-textetspan
                        input type=date name=date_max class=form-control
                               value== htmlspecialchars($date_max) 
                    div
                div
            div
            div class=d-flex gap-2
                button type=submit class=btn btn-darkRechercherbutton
                a href=recherche.php class=btn btn-outline-secondaryRéinitialisera
            div
        form
    div
div

!-- Résultats --
php if ($rechercheLancee) 
    h5 class=mb-3
        = count($resultats)  résultat= count($resultats)  1  's'  ''  trouvé= count($resultats)  1  's'  '' 
    h5

    php if (empty($resultats)) 
        div class=alert alert-warningAucun équipement ne correspond à ces critères.div
    php else 
        table class=table table-striped table-hover
            thead class=table-dark
                tr
                    thMarqueth
                    thModèleth
                    thCatégorieth
                    thUtilisateurth
                    thSalleth
                    thDate d'achatth
                    thth
                tr
            thead
            tbody
                php foreach ($resultats as $eq) 
                tr
                    td= htmlspecialchars($eq['marque']) td
                    td= htmlspecialchars($eq['modele']) td
                    td= htmlspecialchars($eq['categorie']) td
                    td
                        php if ($eq['user_nom']) 
                            = htmlspecialchars($eq['user_prenom'] . ' ' . $eq['user_nom']) 
                        php else 
                            span class=text-mutedNon assignéspan
                        php endif; 
                    td
                    td= htmlspecialchars($eq['salle']  'Non définie') td
                    td
                        = $eq['date_achat'] 
                             date('dmY', strtotime($eq['date_achat'])) 
                             'span class=text-muted-span' 
                    td
                    td
                        a href=publicdetail.phpid== $eq['id']  class=btn btn-sm btn-infoVoira
                    td
                tr
                php endforeach; 
            tbody
        table
    php endif; 
php endif; 

php require_once 'includesfooter.php'; 