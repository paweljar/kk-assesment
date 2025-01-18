<?php

declare(strict_types=1);

namespace App\Request\RequestResolver;

use App\Request\CalculateQuotesRequest;
use App\Request\Factory\CalculateQuotesRequestFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class CalculateQuotesRequestResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private CalculateQuotesRequestFactory $requestFactory,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (CalculateQuotesRequest::class !== $argument->getType()) {
            return [];
        }

        $content = $this->decodeJsonContent($request);
        $quotesRequest = $this->requestFactory->createFromArray($content);

        $this->validate($quotesRequest);

        yield $quotesRequest;
    }

    private function decodeJsonContent(Request $request): ?array
    {
        $content = json_decode($request->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        return $content;
    }

    private function validate(CalculateQuotesRequest $request): void
    {
        $violations = $this->validator->validate($request);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = sprintf(
                    '%s: %s',
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }

            throw new BadRequestHttpException(implode(', ', $errors));
        }
    }
}
