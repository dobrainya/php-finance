<?php

namespace App\Tests\Listener;

use App\Exception\ValidationException;
use App\Listener\ValidateExceptionListener;
use App\Model\ErrorResponse;
use App\Model\ErrorValidationDetails;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidateExceptionListenerTest extends AbstractTestCase
{
    private readonly SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testInvokeSkippedWhenNotValidationException(): void
    {
        $this->serializer->expects($this->never())
            ->method('serialize');

        $event = $this->createExceptionEvent(new \RuntimeException());

        $this->runEvent($event);
    }

    public function testInvoke()
    {
        $serialized = json_encode([
            'message' => 'validation failed',
            'details' => [
                'violations' => [
                    ['field' => 'name', 'message' => 'test error'],
                ],
            ],
        ]);

        $event = $this->createExceptionEvent(new ValidationException(
            new ConstraintViolationList([
                new ConstraintViolation('test error', null, [], null, 'name', null),
            ]),
        ));

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->callback(static function (ErrorResponse $response) {
                    if ('validation failed' !== $response->getMessage()) {
                        return false;
                    }

                    /** @var ErrorValidationDetails|object $details */
                    $details = $response->getDetails();

                    if (!($details instanceof ErrorValidationDetails)) {
                        return false;
                    }

                    $violations = $details->getViolations();
                    if (1 !== count($violations)) {
                        return false;
                    }

                    return 'name' === $violations[0]->getField() && 'test error' === $violations[0]->getMessage();
                }),
                JsonEncoder::FORMAT
            )
            ->willReturn($serialized);

        $this->runEvent($event);
        $this->assertResponse(Response::HTTP_BAD_REQUEST, $serialized, $event->getResponse());
    }

    private function runEvent(ExceptionEvent $event): void
    {
        $listener = new ValidateExceptionListener($this->serializer);
        $listener($event);
    }
}
