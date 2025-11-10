<?php
namespace App\Service;

class GreetingService implements GreetingServiceInterface
{
    public function __construct(private string $appName)
    {
    }

    public function greet(string $name): string
    {
        return "[$this->appName] Bonjour, $name !";
    }
}
