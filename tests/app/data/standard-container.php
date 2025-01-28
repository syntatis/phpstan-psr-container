<?php

declare(strict_types=1);

namespace Syntatis\Tests;

use Psr\Container\ContainerInterface;

use function PHPStan\Testing\assertType;

class Foo
{
	public function doFoo(): void
	{
		$container = new Container();
		$service = $container->get(BazService::class);

		assertType(BazService::class, $service);

		$service = $container->get('bar');

		assertType('object|null', $service);
	}
}

class BazService
{
}

class Container implements ContainerInterface
{
	private array $services = [];

	public function __construct()
	{
		$this->services = [
			BazService::class => new BazService(),
		];
	}

	public function get(string $id): ?object
	{
		return $this->services[$id] ?? null;
	}

	public function has(string $id): bool
	{
		return isset($this->services[$id]);
	}
}
