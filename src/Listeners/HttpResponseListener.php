<?php

namespace App\Listeners;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class HttpResponseListener
{
    public function __construct(private readonly ?string $origin = null)
    {
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->origin) {
            $event->getResponse()->headers->set('Access-Control-Allow-Origin', $this->origin);
        }
    }
}
