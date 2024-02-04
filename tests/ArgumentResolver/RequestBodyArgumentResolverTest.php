<?php

namespace App\Tests\ArgumentResolver;

use App\ArgumentResolver\RequestBodyArgumentResolver;
use App\Attribute\RequestBody;
use App\Exception\RequestBodyConvertException;
use App\Exception\ValidationException;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyArgumentResolverTest extends AbstractTestCase
{
    private readonly SerializerInterface $serializer;
    private readonly ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    public function testResolverReturnsAnEmptyArray()
    {
        $meta = new ArgumentMetadata('some', \stdClass::class, false, false, null);

        $this->assertTrue([] === $this->createArgumentResolver()->resolve(new Request(), $meta));
    }

    public function testResolverThrowsErrorException()
    {
        $meta = new ArgumentMetadata('some', \stdClass::class, false, false, null, false, [
            new RequestBody(),
        ]);

        $request = new Request([], [], [], [], [], [], 'test content');

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($request->getContent(), $meta->getType(), JsonEncoder::FORMAT)
            ->willThrowException(new \RuntimeException());

        $this->expectException(RequestBodyConvertException::class);

        $this->createArgumentResolver()->resolve($request, $meta);
    }

    public function testResolverThrowsViolationException()
    {
        $meta = new ArgumentMetadata('some', \stdClass::class, false, false, null, false, [
            new RequestBody(),
        ]);

        $body = ['test' => 1];
        $encodedBody = json_encode($body);
        $request = new Request([], [], [], [], [], [], $encodedBody);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($encodedBody, \stdClass::class, JsonEncoder::FORMAT)
            ->willReturn($body);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($body)
            ->willReturn(new ConstraintViolationList([
                new ConstraintViolation('some error', null, [], null, 'some path', null),
            ]));

        $this->expectException(ValidationException::class);

        $this->createArgumentResolver()->resolve($request, $meta);
    }

    public function testResolverReturnsNotEmptyArray()
    {
        $meta = new ArgumentMetadata('some', \stdClass::class, false, false, null, false, [
            new RequestBody(),
        ]);

        $body = ['test' => 1];
        $encodedBody = json_encode($body);
        $request = new Request([], [], [], [], [], [], $encodedBody);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($encodedBody, \stdClass::class, JsonEncoder::FORMAT)
            ->willReturn($body);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($body)
            ->willReturn(new ConstraintViolationList([]));

        $this->assertEquals([$body], $this->createArgumentResolver()->resolve($request, $meta));
    }

    private function createArgumentResolver(): RequestBodyArgumentResolver
    {
        return new RequestBodyArgumentResolver($this->serializer, $this->validator);
    }
}
