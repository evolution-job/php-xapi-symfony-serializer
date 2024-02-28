<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerSpec extends ObjectBehavior
{
    public function it_creates_a_serializer(): void
    {
        self::createSerializer()->shouldBeAnInstanceOf(SerializerInterface::class);
    }
}
