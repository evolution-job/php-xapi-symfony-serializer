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

use Symfony\Component\Serializer\SerializerInterface;
use Xabbuh\XApi\Model\Person;
use Xabbuh\XApi\Serializer\Exception\PersonSerializationException;
use Xabbuh\XApi\Serializer\Exception\SerializationException;
use Xabbuh\XApi\Serializer\PersonSerializerInterface;

/**
 * Serializes and deserializes {@link Person persons} using the Symfony Serializer component.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class PersonSerializer implements PersonSerializerInterface
{
    public function __construct(private readonly SerializerInterface $serializer) { }

    /**
     * {@inheritDoc}
     */
    public function serializePerson(Person $person): string
    {
        try {
            return $this->serializer->serialize($person, 'json');
        } catch (SerializationException $serializationException) {
            throw new PersonSerializationException($serializationException->getMessage(), 0, $serializationException);
        }
    }
}
