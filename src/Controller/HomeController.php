<?php
namespace App\Controller;

use App\Service\GreetingServiceInterface;

class HomeController
{
    public function __construct(private GreetingServiceInterface $greeter)
    {
    }

    public function __invoke(): void
    {
        $user = $_GET['name'] ?? 'le monde';
        echo $this->greeter->greet($user);
    }
}
