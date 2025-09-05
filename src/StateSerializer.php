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

use Exception;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Xabbuh\XApi\Model\State;
use Xabbuh\XApi\Serializer\Exception\StateDeserializationException;
use Xabbuh\XApi\Serializer\Exception\StateSerializationException;
use Xabbuh\XApi\Serializer\StateSerializerInterface;

/**
 * Serializes and deserializes {@link State states} using the Symfony Serializer component.
 */
final readonly class StateSerializer implements StateSerializerInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function serializeState(State $state): string
    {
        try {
            return $this->serializer->serialize($state, 'json');
        } catch (Exception|ExceptionInterface $exception) {
            throw new StateSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    public function deserializeState(mixed $state, ?string $data = null): State
    {
        try {
            $stateObject = $this->serializer->deserialize(
                $state,
                State::class,
                'json',
                ['data' => $data]
            );

            if ($stateObject instanceof State) {
                return $stateObject;
            }

        } catch (Exception|ExceptionInterface $exception) {
            throw new StateDeserializationException($exception->getMessage(), 0, $exception);
        }

        throw new StateDeserializationException('Try to unserialized an empty State.', 0);
    }
}