<?php
// Fichier : deploy.php
// Ce fichier permet de déclencher le déploiement depuis GitHub Actions

// 1. Définir un token secret (à mettre dans GitHub Actions)
$secret_token = "MonSuperTokenSecret2026";

// 2. Vérifier le token envoyé par GitHub
if (!isset($_GET['token']) || $_GET['token'] !== $secret_token) {
    header('HTTP/1.1 403 Forbidden');
    die("Accès refusé. Token invalide.");
}

// 3. Dossier racine du projet (un dossier au dessus de public_html généralement, ou dans public_html si tout est là)
// Adaptez le chemin si besoin. Ici on suppose que le script est dans public_html.
$project_dir = __DIR__;

// 4. Exécuter les commandes de mise à jour
// Remarque : 2>&1 permet de capturer les erreurs dans la sortie.
$commands = [
    "cd {$project_dir}",
    "git pull origin main 2>&1",
    "composer install --no-dev --optimize-autoloader 2>&1",
    "php artisan migrate --force 2>&1",
    "php artisan config:cache 2>&1",
    "php artisan route:cache 2>&1",
    "php artisan view:cache 2>&1",
    "php artisan l5-swagger:generate 2>&1",
];

$output = '';
foreach ($commands as $command) {
    $output .= "<span style=\"color: #6be237;\">\$</span> <span style=\"color: #729fcf;\">{$command}\n</span>";
    $output .= htmlentities(shell_exec($command)) . "\n";
}

// 5. Afficher le résultat
echo "<pre style=\"background-color: #111; color: #fff; padding: 10px; border-radius: 5px;\">";
echo $output;
echo "</pre>";
echo "<h3 style=\"color: green;\">Déploiement terminé avec succès !</h3>";
?>
