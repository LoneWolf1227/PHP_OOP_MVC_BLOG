<?php
try {
    require __DIR__ . '/../autoloader.php';

    $route = $_GET['route'] ?? '';

    $routes = require __DIR__ . '/../MVC/routes.php';

    $isRouteFound = false;

    foreach ($routes as $pattern => $controllerAndAction) {
        preg_match($pattern, $route, $matches);
        if (!empty($matches)) {
            $isRouteFound = true;
            break;
        }
    }

    if (!$isRouteFound) {
        throw new \Exception\NotFoundException('');
    }

    $controllerName = $controllerAndAction[0];
    $actionName = $controllerAndAction[1];

    unset($matches[0]);

    $controller = new $controllerName();
    $controller->$actionName(...$matches);
}
catch (\Exception\DbException $e)
{
    $view = new \MVC\Views\Views(__DIR__ . '/../MVC/Views/Temp/errors');
    $view->renderHTML( '500.php', ['error' => $e->getMessage()], 500);
}
catch (\Exception\NotFoundException $e)
{
    $view = new \MVC\Views\Views(__DIR__ . '/../MVC/Views/Temp/errors');
    $view->renderHTML('404.php', ['error' => $e->getMessage()], 404);
}
catch (\Exception\UnauthorizedException $e)
{
    $view = new \MVC\Views\Views(__DIR__ . '/../MVC/Views/Temp/errors');
    $view->renderHTML('401.php', ['error' => $e->getMessage()], '401');
}