<?php

use App\Controller\VehicleController;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();
$method = $request->getMethod();
$path = $request->getPathInfo();

$controller = new VehicleController();

switch (true) {
    // Home/index
    case $path === '/' && $method === 'GET':
        $controller->index()->send();
        break;

    // List vehicles
    case $path === '/vehicles' && $method === 'GET':
    case $path === '/vehicles/list' && $method === 'GET':
        $controller->list()->send();
        break;

    // Unified create + update (POST /vehicles/save/0 or /vehicles/save/{id})
    case preg_match('#^/vehicles/save/(\d+)$#', $path, $matches) && $method === 'POST':
        $controller->save((int)$matches[1], $request)->send();
        break;

    // Delete
    case preg_match('#^/vehicles/delete/(\d+)$#', $path, $matches) && $method === 'DELETE':
        $controller->delete((int)$matches[1])->send();
        break;

    // 404 fallback
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
}
