<?php

namespace App\Shared\Symfony\Infraestructure;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;

class CorsPreflightListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getMethod() !== 'OPTIONS') {
            return;
        }

        $response = new Response();
        $response->setStatusCode(204);
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin', '*'));
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        $event->setResponse($response);
    }
}
