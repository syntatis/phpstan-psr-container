<?php
declare(strict_types=1);

namespace Syntatis\PHPStan\Psr11;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use Psr\Container\ContainerInterface;

class ContainerDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    private ?string $interface = null;

    public function __construct(?string $interface = null)
    {
        $interface = is_string($interface) ? ltrim($interface, '\\') : null;
 
        if (str_ends_with($interface, '\\' . ContainerInterface::class)) {
            $this->interface = $interface;
        }
    }

    public function getClass(): string
    {
        return $this->interface ?? ContainerInterface::class;
    }

    public function isMethodSupported(MethodReflection $reflection): bool
    {
        return $reflection->getName() === 'get';
    }

    public function getTypeFromMethodCall(MethodReflection $reflection, MethodCall $methodCall, Scope $scope): Type
    {
        $args = $methodCall->getArgs();
        if (count($args) === 0) {
            return ParametersAcceptorSelector::selectFromArgs($scope, $methodCall->getArgs(), $reflection->getVariants())->getReturnType();
        }
        $arg = $args[0]->value;
        // Care only for ::class parameters, we can not guess types for random strings.
        if (!$arg instanceof ClassConstFetch) {
            return ParametersAcceptorSelector::selectFromArgs($scope, $methodCall->getArgs(), $reflection->getVariants())->getReturnType();
        }

        $argType = $scope->getType($args[0]->value);
        if (!$argType instanceof ConstantStringType) {
            return ParametersAcceptorSelector::selectFromArgs($scope, $methodCall->getArgs(), $reflection->getVariants())->getReturnType();
        }

        return new ObjectType($argType->getValue());
    }
}
