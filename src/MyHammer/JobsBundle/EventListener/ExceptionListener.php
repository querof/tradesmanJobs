<?php
namespace MyHammer\JobsBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $response = new JsonResponse();

        $message = '{"error":"Undefined Error"}';
        $staturError = Response::HTTP_NOT_ACCEPTABLE;

        if (strpos($exception->getMessage(), 'PDO connection') !== false || strpos($exception->getMessage(), 'SQLSTATE') !== false)
        {
          $staturError = Response::HTTP_NOT_ACCEPTABLE;
          $message = '{"error":"DB connection fail}';
        }

        $response->setContent($message);

        $response->setStatusCode($staturError);

        $event->setResponse($response);
    }
}
