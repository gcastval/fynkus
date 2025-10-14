<?php

declare(strict_types=1);

namespace App\Shared\Symfony\Infraestructure;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class DomainErrorListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        /** @var \DomainError */
        $exception = $event->getThrowable();


        if (is_callable([$exception, 'getStatusCode'])) {
            $HTTP_STATUS_CODE = $exception->getStatusCode();
        } else {
            $HTTP_STATUS_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $data = [
            'code' => $HTTP_STATUS_CODE,
            'message' => $exception->getMessage(),
        ];

        $response = new Response();
        $response->setContent(\strval(json_encode($data)));
        $response->setStatusCode($HTTP_STATUS_CODE);
        $response->headers->set('Content-Type', 'application/json');

        $event->allowCustomResponseCode();
        $event->setResponse($response);
    }
}
