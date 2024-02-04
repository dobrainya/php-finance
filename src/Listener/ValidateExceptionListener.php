<?php

namespace App\Listener;

use App\Exception\ValidationException;
use App\Model\ErrorResponse;
use App\Model\ErrorValidationDetails;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidateExceptionListener
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if (!($throwable instanceof ValidationException)) {
            return;
        }

        $data = $this->serializer->serialize(
            new ErrorResponse($throwable->getMessage(), $this->getViolations($throwable->getViolations())),
            JsonEncoder::FORMAT
        );

        $event->setResponse(new JsonResponse($data, Response::HTTP_BAD_REQUEST, [], true));
    }

    private function getViolations(ConstraintViolationListInterface $violations): ErrorValidationDetails
    {
        $model = new ErrorValidationDetails();
        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $model->addViolation($violation->getPropertyPath(), $violation->getMessage());
        }

        return $model;
    }
}
