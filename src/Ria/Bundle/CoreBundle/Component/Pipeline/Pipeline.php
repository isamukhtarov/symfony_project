<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Component\Pipeline;

use Closure;
use JetBrains\PhpStorm\Pure;
use Throwable;
use RuntimeException;
use Psr\Container\ContainerInterface;

class Pipeline implements PipelineInterface
{
    protected ContainerInterface $container;

    /**
     * The object being passed through the pipeline.
     *
     * @var mixed
     */
    protected $passable;

    protected array $pipes = [];
    protected string $method = 'handle';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function send(mixed $traveler): self
    {
        $this->passable = $traveler;

        return $this;
    }

    public function through(array $stops): self
    {
        $this->pipes = is_array($stops) ? $stops : func_get_args();

        return $this;
    }

    public function via(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes()), $this->carry(), $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    public function thenReturn()
    {
        return $this->then(function ($passable) {
            return $passable;
        });
    }

    protected function prepareDestination(Closure $destination): Closure
    {
        return function ($passable) use ($destination) {
            try {
                return $destination($passable);
            } catch (Throwable $e) {
                return $this->handleException($passable, $e);
            }
        };
    }

    protected function carry(): Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                try {
                    if (is_callable($pipe)) {
                        // If the pipe is a callable, then we will call it directly, but otherwise we
                        // will resolve the pipes out of the dependency container and call it with
                        // the appropriate method and arguments, returning the results back out.
                        return $pipe($passable, $stack);
                    } elseif (! is_object($pipe)) {
                        [$name, $parameters] = $this->parsePipeString($pipe);

                        // If the pipe is a string we will parse the string and resolve the class out
                        // of the dependency injection container. We can then build a callable and
                        // execute the pipe function giving in the parameters that are required.
                        $pipe = $this->getContainer()->get($name);

                        $parameters = array_merge([$passable, $stack], $parameters);
                    } else {
                        // If the pipe is already an object we'll just make a callable and pass it to
                        // the pipe as-is. There is no need to do any extra parsing and formatting
                        // since the object we're given was already a fully instantiated object.
                        $parameters = [$passable, $stack];
                    }

                    $carry = method_exists($pipe, $this->method)
                        ? $pipe->{$this->method}(...$parameters)
                        : $pipe(...$parameters);

                    return $this->handleCarry($carry);
                } catch (Throwable $e) {
                    return $this->handleException($passable, $e);
                }
            };
        };
    }

    #[Pure] protected function parsePipeString($pipe): array
    {
        [$name, $parameters] = array_pad(explode(':', $pipe, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

    protected function pipes(): array
    {
        return $this->pipes;
    }

    public function getContainer(): ContainerInterface
    {
        if ($this->container === null)
            throw new RuntimeException('A container instance has not been passed to the Pipeline.');

        return $this->container;
    }

    protected function handleCarry($carry)
    {
        return $carry;
    }

    protected function handleException($passable, Throwable $e)
    {
        return throw $e;
    }
}