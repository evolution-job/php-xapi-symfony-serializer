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
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritDoc}
     */
    public function serializePerson(Person $person)
    {
        try {
            return $this->serializer->serialize($person, 'json');
        } catch (SerializationException $e) {
            throw new PersonSerializationException($e->getMessage(), 0, $e);
        }
    }
}
