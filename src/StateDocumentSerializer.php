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
use Xabbuh\XApi\Model\StateDocument;
use Xabbuh\XApi\Serializer\Exception\StateDocumentDeserializationException;
use Xabbuh\XApi\Serializer\Exception\StateDocumentSerializationException;
use Xabbuh\XApi\Serializer\StateDocumentSerializerInterface;

/**
 * Serializes and deserializes {@link use StateDocument} using the Symfony Serializer component.
 *
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
final readonly class StateDocumentSerializer implements StateDocumentSerializerInterface
{
    public function __construct(private SerializerInterface $serializer) { }

    public function serializeStateDocument(StateDocument $stateDocument): string
    {
        try {
            return $this->serializer->serialize($stateDocument, 'json');
        } catch (ExceptionInterface $exception) {
            throw new StateDocumentSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    public function deserializeStateDocument(string $data): StateDocument
    {
        try {

            $stateDocument = $this->serializer->deserialize(
                $data,
                StateDocument::class,
                'json'
            );

            if ($stateDocument instanceof StateDocument) {
                return $stateDocument;
            }

        } catch (ExceptionInterface $exception) {
            throw new StateDocumentDeserializationException($exception->getMessage(), 0, $exception);
        }

        throw new StateDocumentDeserializationException('Try to unserialized an empty StateDocument.', 0);
    }
}