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
    /**
     * @var SerializerInterface The underlying serializer
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serializeState(State $state): string
    {
        try {
            return $this->serializer->serialize($state, 'json');
        } catch (Exception $e) {
            throw new StateSerializationException($e->getMessage(), 0, $e);
        }
    }

    public function serializeStates(array $states): string
    {
        try {
            return $this->serializer->serialize($states, 'json');
        } catch (Exception $e) {
            throw new StateSerializationException($e->getMessage(), 0, $e);
        }
    }

    public function deserializeState($state, $data = null)
    {
        $json = json_decode((string)$data, true);
        $state['data'] = $json ?: $data;
        $stateEncode = json_encode($state);

        try {
            return $this->serializer->deserialize(
                $stateEncode,
                State::class,
                'json'
            );

        } catch (Exception $e) {
            throw new StateDeserializationException($e->getMessage(), 0, $e);
        }
    }

    public function deserializeStates($state, $data = null)
    {
        try {
            return $this->serializer->deserialize(
                $state,
                'Xabbuh\XApi\Model\State[]',
                'json'
            );
        } catch (Exception $e) {
            throw new StateDeserializationException($e->getMessage(), 0, $e);
        }
    }
}