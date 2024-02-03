<?php

namespace App\ArgumentResolver;

use App\Attribute\RequestBody;
use App\Exception\RequestBodyConvertException;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyArgumentResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @return RequestBody[]
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attribute = $argument->getAttributesOfType(RequestBody::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!$attribute) {
            return [];
        }

        try {
            $model = $this->serializer->deserialize($request->getContent(), $argument->getType(), JsonEncoder::FORMAT);
        } catch (\Throwable $e) {
            throw new RequestBodyConvertException($e);
        }

        $errors = $this->validator->validate($model);

        if ($errors->count() > 0) {
            throw new ValidationException($errors);
        }

        return [$model];
    }
}
