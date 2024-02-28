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
use Xabbuh\XApi\Model\Statement;
use Xabbuh\XApi\Serializer\Exception\StatementDeserializationException;
use Xabbuh\XApi\Serializer\Exception\StatementSerializationException;
use Xabbuh\XApi\Serializer\StatementSerializerInterface;

/**
 * Serializes and deserializes {@link Statement statements} using the Symfony Serializer component.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 * @see \Xabbuh\XApi\Serializer\Symfony\Tests\StatementSerializerTest
 */
final class StatementSerializer implements StatementSerializerInterface
{
    public function __construct(private readonly SerializerInterface $serializer) { }

    /**
     * {@inheritDoc}
     */
    public function serializeStatement(Statement $statement): string
    {
        try {
            return $this->serializer->serialize($statement, 'json');
        } catch (ExceptionInterface $exception) {
            throw new StatementSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function serializeStatements(array $statements): string
    {
        try {
            return $this->serializer->serialize($statements, 'json');
        } catch (ExceptionInterface $exception) {
            throw new StatementSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserializeStatement($data, array $attachments = []): Statement
    {
        try {
            return $this->serializer->deserialize(
                $data,
                Statement::class,
                'json',
                ['xapi_attachments' => $attachments]
            );
        } catch (ExceptionInterface $exception) {
            throw new StatementDeserializationException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserializeStatements($data, array $attachments = []): array
    {
        try {
            return $this->serializer->deserialize(
                $data,
                'Xabbuh\XApi\Model\Statement[]',
                'json',
                ['xapi_attachments' => $attachments]
            );
        } catch (ExceptionInterface $exception) {
            throw new StatementDeserializationException($exception->getMessage(), 0, $exception);
        }
    }
}
