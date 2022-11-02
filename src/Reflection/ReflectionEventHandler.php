<?php

namespace AutomaNet\EventBus\Reflection;

use AutomaNet\EventBus\Contracts\Event\EventInterface;

class ReflectionEventHandler
{
    private \ReflectionMethod $reflectionMethod;

    public function __construct(\ReflectionMethod $reflectionMethod)
    {
        $this->reflectionMethod = $reflectionMethod;
    }

    public function getListenerClass(): string
    {
        return $this->reflectionMethod->getDeclaringClass()->getName();
    }

    public function getMethodName(): string
    {
        return $this->reflectionMethod->getName();
    }

    public function getEventName(): string
    {
        return substr($this->getMethodName(), strlen('handle'));
    }

    public function getPropertyEventClass(): string
    {
        foreach ($this->reflectionMethod->getParameters() as $parameter) {
            $parameterType = $parameter->getType();

            if ($parameterType instanceof \ReflectionNamedType && is_subclass_of($parameterType->getName(), EventInterface::class)) {
                return $parameterType->getName();
            }
        }

        throw new \Exception('No property event class not found');
    }

    public function getClosure(object $object): \Closure
    {
        return $this->reflectionMethod->getClosure($object);
    }
}
