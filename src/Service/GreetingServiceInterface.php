<?php
namespace App\Service;

interface GreetingServiceInterface
{
    public function greet(string $name): string;
}