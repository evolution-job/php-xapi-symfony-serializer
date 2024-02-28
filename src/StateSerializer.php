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
use Symfony\Component\Serializer\SerializerInterface;
use Xabbuh\XApi\Model\State;
use Xabbuh\XApi\Serializer\Exception\StateDeserializationException;
use Xabbuh\XApi\Serializer\Exception\StateSerializationException;
use Xabbuh\XApi\Serializer\StateSerializerInterface;

/**
 * Serializes and deserializes {@link State states} using the Symfony Serializer component.
 */
final class StateSerializer implements StateSerializerInterface
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function serializeState(State $state): string
    {
        try {
            return $this->serializer->serialize($state, 'json');
        } catch (Exception $exception) {
            throw new StateSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    public function serializeStates(array $states): string
    {
        try {
            return $this->serializer->serialize($states, 'json');
        } catch (Exception $exception) {
            throw new StateSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    public function deserializeState($state, $data = null): State
    {
        try {

            $json = json_decode((string)$data, true);
            $state['data'] = $json ?: $data;

            $stateEncode = json_encode($state, JSON_THROW_ON_ERROR);

            return $this->serializer->deserialize(
                $stateEncode,
                State::class,
                'json'
            );

        } catch (Exception $exception) {
            throw new StateDeserializationException($exception->getMessage(), 0, $exception);
        }
    }

    public function deserializeStates($state, $data = null): array
    {
        try {
            return $this->serializer->deserialize(
                $state,
                'Xabbuh\XApi\Model\State[]',
                'json'
            );
        } catch (Exception $exception) {
            throw new StateDeserializationException($exception->getMessage(), 0, $exception);
        }
    }
}