<?php

namespace App\Listeners;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class HttpResponseListener
{
    private null|string $origin;

    /**
     * @param string|null $origin
     */
    public function __construct(?string $origin = null)
    {
        $this->origin = $origin;
    }


    public function onKernelResponse(ResponseEvent $event) {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->origin) {
            $event->getResponse()->headers->set('Access-Control-Allow-Origin', $this->origin);
        }
    }
}
