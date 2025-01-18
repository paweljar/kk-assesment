<?php

declare(strict_types=1);

namespace unit\Request\RequestResolver;

use App\Request\CalculateQuotesRequest;
use App\Request\Factory\CalculateQuotesRequestFactory;
use App\Request\RequestResolver\CalculateQuotesRequestResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CalculateQuotesRequestResolverTest extends TestCase
{
    private CalculateQuotesRequestResolver $resolver;
    private ValidatorInterface $validator;
    private CalculateQuotesRequestFactory $factory;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->factory = $this->createMock(CalculateQuotesRequestFactory::class);
        $this->resolver = new CalculateQuotesRequestResolver(
            $this->validator,
            $this->factory
        );
    }

    public function testResolveWithValidRequest(): void
    {
        $content = ['topics' => ['math' => 50]];
        $request = new Request([], [], [], [], [], [], json_encode($content));
        $argument = $this->createArgumentMetadata();

        $quotesRequest = new CalculateQuotesRequest($content);

        $this->factory->expects($this->once())
            ->method('createFromArray')
            ->with($content)
            ->willReturn($quotesRequest);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($quotesRequest)
            ->willReturn(new ConstraintViolationList());

        $result = iterator_to_array($this->resolver->resolve($request, $argument));

        $this->assertCount(1, $result);
        $this->assertInstanceOf(CalculateQuotesRequest::class, $result[0]);
    }

    public function testResolveWithInvalidJson(): void
    {
        $request = new Request([], [], [], [], [], [], '{invalid json}');
        $argument = $this->createArgumentMetadata();

        $this->expectException(BadRequestHttpException::class);
        iterator_to_array($this->resolver->resolve($request, $argument));
    }

    private function createArgumentMetadata(): ArgumentMetadata
    {
        return new ArgumentMetadata(
            'request',
            CalculateQuotesRequest::class,
            false,
            false,
            null
        );
    }
}
