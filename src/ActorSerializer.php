<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Serializer\Symfony;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Serializer\ActorSerializerInterface;
use Xabbuh\XApi\Serializer\Exception\ActorDeserializationException;
use Xabbuh\XApi\Serializer\Exception\ActorSerializationException;

/**
 * Serializes and deserializes {@link Actor actors} using the Symfony Serializer component.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 * @see \Xabbuh\XApi\Serializer\Symfony\Tests\ActorSerializerTest
 */
final readonly class ActorSerializer implements ActorSerializerInterface
{
    public function __construct(private SerializerInterface $serializer) { }

    /**
     * {@inheritDoc}
     */
    public function serializeActor(Actor $actor): string
    {
        try {
            return $this->serializer->serialize($actor, 'json');
        } catch (ExceptionInterface $exception) {
            throw new ActorSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserializeActor($data): Actor
    {
        try {
            $actor = $this->serializer->deserialize($data, Actor::class, 'json');

            if ($actor instanceof Actor) {
                return $actor;
            }
        } catch (ExceptionInterface $exception) {
            throw new ActorDeserializationException($exception->getMessage(), 0, $exception);
        }

        throw new ActorDeserializationException('Try to unserialized an empty Actor.', 0);
    }
}
