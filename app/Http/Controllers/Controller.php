<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Laravel Starter Kit API",
    description: "Dokumentasi API menggunakan L5 Swagger"
)]
#[OA\Server(url: 'http://localhost:8000', description: 'Local Server')]
abstract class Controller
{
    
    
}
