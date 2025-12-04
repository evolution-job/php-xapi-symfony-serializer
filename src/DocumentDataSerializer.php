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
use Xabbuh\XApi\Model\DocumentData;
use Xabbuh\XApi\Serializer\DocumentDataSerializerInterface;
use Xabbuh\XApi\Serializer\Exception\DocumentDataDeserializationException;
use Xabbuh\XApi\Serializer\Exception\DocumentDataSerializationException;

/**
 * Serializes and deserializes {@link Document documents} using the Symfony Serializer component.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final readonly class DocumentDataSerializer implements DocumentDataSerializerInterface
{
    public function __construct(private SerializerInterface $serializer) { }

    /**
     * {@inheritDoc}
     */
    public function serializeDocumentData(DocumentData $documentData): string
    {
        try {
            return $this->serializer->serialize($documentData, 'json');
        } catch (ExceptionInterface $exception) {
            throw new DocumentDataSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserializeDocumentData($data): DocumentData
    {
        try {
            $documentData = $this->serializer->deserialize($data, DocumentData::class, 'json');

            if ($documentData instanceof DocumentData) {
                return $documentData;
            }

        } catch (ExceptionInterface $exception) {
            throw new DocumentDataDeserializationException($exception->getMessage(), 0, $exception);
        }

        throw new DocumentDataDeserializationException('Try to unserialized an empty DocumentData.', 0);
    }
}
