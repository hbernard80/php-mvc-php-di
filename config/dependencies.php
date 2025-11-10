<?php
use DI\ContainerBuilder;
use function DI\autowire;
use function DI\get;
use function DI\create;

use App\Service\GreetingServiceInterface;
use App\Service\GreetingService;
use App\Infrastructure\PdoFactory;

return [
    // Lier une interface à son implémentation (autowiring activé)
    GreetingServiceInterface::class => autowire(GreetingService::class)
        // Injecter un paramètre scalar depuis la config
        ->constructorParameter('appName', get('app.name')),

    // Exemple : créer un PDO via une factory
    PDO::class => autowire(PdoFactory::class)->method('make'),

    // Ou sans classe factory, en “create” direct :
    // PDO::class => create(PDO::class)->constructor(get('db.dsn'), get('db.user'), get('db.pass')),
];
