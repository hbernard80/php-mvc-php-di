# Guide d'installation et d'utilisation de PHP-DI

PHP-DI est un conteneur d'injection de dépendances (Dependency Injection) léger et flexible pour PHP. Ce guide décrit les prérequis, les étapes d'installation et plusieurs façons de l'utiliser dans un projet PHP.

## Pré-requis

- PHP 8.1 ou supérieur (PHP-DI fonctionne à partir de PHP 7.4, mais ce projet cible PHP 8.1+)
- [Composer](https://getcomposer.org/) installé globalement
- Un projet PHP initialisé avec un fichier `composer.json`

## Installation

### 1. Créer ou initialiser un projet PHP

```bash
mkdir mon-projet && cd mon-projet
composer init
```

### 2. Installer PHP-DI via Composer

```bash
composer require php-di/php-di
```

Composer ajoute automatiquement PHP-DI comme dépendance dans votre fichier `composer.json` et télécharge le code source dans le répertoire `vendor/`.

### 3. (Optionnel) Installer les définitions automatiques pour les annotations

Si vous souhaitez utiliser les annotations (Doctrine Annotations) pour définir des dépendances, installez le paquet suivant :

```bash
composer require doctrine/annotations
```

## Concepts clés

- **Conteneur** : objet responsable de la création et de la résolution des dépendances.
- **Définitions** : règles qui indiquent au conteneur comment instancier une classe ou une interface.
- **Injection automatique (autowiring)** : PHP-DI peut analyser les signatures de constructeur et résoudre automatiquement les dépendances.

## Configuration du conteneur

PHP-DI propose plusieurs façons de configurer les définitions :

1. **Autowiring par défaut** – sans configuration spécifique, PHP-DI essaie de résoudre les classes en inspectant automatiquement leurs constructeurs.
2. **Fichiers de configuration** – vous pouvez fournir un fichier `definitions.php` qui retourne un tableau associatif de définitions.
3. **Annotations / attributs PHP 8** – permettent de définir des dépendances directement dans le code.
4. **PHP-DI Bridge** – intégration simplifiée avec des frameworks (Slim, Symfony, Laravel, etc.).

### Exemple de configuration via fichier PHP

Créez un fichier `config/definitions.php` :

```php
<?php

use function DI\create;
use function DI\get;

return [
    App\Repository\UserRepositoryInterface::class => create(App\Repository\DoctrineUserRepository::class),
    App\Service\NotificationService::class => create()
        ->constructor(get(App\Repository\UserRepositoryInterface::class)),
];
```

### Chargement du conteneur dans votre application

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config/definitions.php');
$container = $containerBuilder->build();

// Récupération d'une dépendance
$notificationService = $container->get(App\Service\NotificationService::class);
```

## Utilisation avancée

### Autowiring

Si vos classes type-hint leurs dépendances dans leur constructeur, vous n'avez pas besoin d'écrire de définitions explicites.

```php
<?php

namespace App\Service;

use App\Repository\UserRepositoryInterface;

class NotificationService
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function notify(int $userId, string $message): void
    {
        $user = $this->users->find($userId);
        // Envoyer le message...
    }
}
```

PHP-DI identifiera automatiquement `UserRepositoryInterface` si une définition est disponible (ou s'il peut l'autowirer).

### Paramètres scalaires et valeurs partagées

Vous pouvez injecter des paramètres scalaires, des constantes ou des instances uniques dans le conteneur.

```php
return [
    'config.mailer.dsn' => 'smtp://localhost:1025',
    App\Service\Mailer::class => DI\create()
        ->constructor(DI\get('config.mailer.dsn')),
];
```

### Factories et Closures

```php
return [
    App\Service\CacheService::class => function () {
        return new App\Service\CacheService(new Redis());
    },
];
```

### Décorateurs

PHP-DI permet d'envelopper une dépendance par un décorateur :

```php
return [
    App\Service\Mailer::class => DI\decorate(function ($previous, DI\Container $c) {
        return new App\Service\LoggingMailer($previous, $c->get(App\Logger::class));
    }),
];
```

## Utilisation avec un framework

### Exemple : Slim Framework

```php
use DI\Bridge\Slim\App as SlimApp;

$app = SlimApp::createFromContainer($container);
```

Le conteneur alimentera automatiquement vos contrôleurs et middlewares.

### Exemple : Symfony

Symfony possède son propre conteneur, mais vous pouvez intégrer PHP-DI pour des sous-domaines spécifiques via [PHP-DI Bridge for Symfony](https://github.com/PHP-DI/Symfony-Bridge).

## Débogage et outils

- **Inspecter les définitions** : `$container->getDefinition(<id>)`
- **Vérifier les dépendances manquantes** : activez le `ContainerBuilder::enableCompilation()` pour générer un cache et repérer les erreurs plus tôt.
- **Utiliser le compilateur** : `$containerBuilder->enableCompilation(__DIR__ . '/var/cache');`

## Tests unitaires

Lors des tests, vous pouvez créer un conteneur spécifique ou surcharger certaines définitions.

```php
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    App\Service\Mailer::class => new App\Tests\Doubles\FakeMailer(),
]);
$testContainer = $containerBuilder->build();
```

## Ressources supplémentaires

- [Documentation officielle de PHP-DI](https://php-di.org/doc/)
- [Référentiel GitHub de PHP-DI](https://github.com/PHP-DI/PHP-DI)
- [Intégration avec Slim Framework](https://github.com/PHP-DI/Slim-Bridge)
- [Intégration avec Symfony](https://github.com/PHP-DI/Symfony-Bridge)

Ce README devrait vous permettre de démarrer rapidement avec PHP-DI et d'exploiter ses fonctionnalités principales dans vos projets.
