<?php

declare(strict_types=1);

namespace Zajca\Extensions\Route;

use Symfony\Bundle\FrameworkBundle\Routing\AnnotatedRouteControllerLoader;

class ApiRouteLoader extends AnnotatedRouteControllerLoader
{
    protected $routeAnnotationClass = ApiRoute::class;
}
