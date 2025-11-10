<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use App\Controller\HomeController;

require __DIR__ . '/../vendor/autoload.php';

// 1) Construire le conteneur
$builder = new ContainerBuilder();

// Charger la config et les définitions
$config       = require __DIR__ . '/../config/config.php';
$definitions  = require __DIR__ . '/../config/dependencies.php';

$builder->addDefinitions($config);
$builder->addDefinitions($definitions);

$container = $builder->build();

// 2) Récupérer le contrôleur depuis le conteneur (autowiring)
$controller = $container->get(HomeController::class);

// 3) Lancer l'action
$controller(); // appelle __invoke()
