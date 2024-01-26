<?php

namespace App\Listeners;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class HttpResponseListener
{
    public function __construct(
        private readonly ?string $origin = null,
        private readonly ?string $methods = null,
        private readonly ?string $headers = null
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->origin) {
            $event->getResponse()->headers->set('Access-Control-Allow-Origin', $this->origin);
        }

        if ($this->origin && !empty($this->methods) && [] !== $this->methods) {
            $event->getResponse()->headers->set('Access-Control-Allow-Methods:', $this->methods);
        }

        if ($this->origin && !empty($this->headers) && [] !== $this->headers) {
            $event->getResponse()->headers->set('Access-Control-Allow-Headers', $this->headers);
        }
    }
}
