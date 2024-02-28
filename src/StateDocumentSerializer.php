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

use JsonException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Xabbuh\XApi\Model\StateDocument;
use Xabbuh\XApi\Serializer\Exception\StateDocumentDeserializationException;
use Xabbuh\XApi\Serializer\Exception\StateDocumentSerializationException;
use Xabbuh\XApi\Serializer\StateDocumentSerializerInterface;

/**
 * Serializes and deserializes {@link use StateDocument stateDocument} using the Symfony Serializer component.
 */
final class StateDocumentSerializer implements StateDocumentSerializerInterface
{
    public function __construct(private readonly SerializerInterface $serializer) { }

    public function serializeStateDocument(StateDocument $stateDocument): string
    {
        try {
            return $this->serializer->serialize($stateDocument, 'json');
        } catch (ExceptionInterface $exception) {
            throw new StateDocumentSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    public function deserializeStateDocument($data): StateDocument
    {
        try {

            $json = json_decode((string)$data, true);
            $state['data'] = $json ?: $data;
            $state = json_encode($state, JSON_THROW_ON_ERROR);

            return $this->serializer->deserialize(
                $state,
                StateDocument::class,
                'json'
            );

        } catch (JsonException $exception) {
            throw new StateDocumentDeserializationException($exception->getMessage(), 0, $exception);
        }
    }
}