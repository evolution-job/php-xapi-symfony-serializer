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
use Xabbuh\XApi\Model\StatementResult;
use Xabbuh\XApi\Serializer\Exception\StatementResultDeserializationException;
use Xabbuh\XApi\Serializer\Exception\StatementResultSerializationException;
use Xabbuh\XApi\Serializer\StatementResultSerializerInterface;

/**
 * Serializes and deserializes {@link StatementResult statement results} using the Symfony Serializer component.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 * @see \Xabbuh\XApi\Serializer\Symfony\Tests\StatementResultSerializerTest
 */
final readonly class StatementResultSerializer implements StatementResultSerializerInterface
{
    public function __construct(private SerializerInterface $serializer) { }

    /**
     * {@inheritDoc}
     */
    public function serializeStatementResult(StatementResult $statementResult): string
    {
        try {
            return $this->serializer->serialize($statementResult, 'json');
        } catch (ExceptionInterface $exception) {
            throw new StatementResultSerializationException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserializeStatementResult($data, array $attachments = []): StatementResult
    {
        try {
            $statementResult = $this->serializer->deserialize(
                $data,
                StatementResult::class,
                'json',
                ['xapi_attachments' => $attachments]
            );

            if ($statementResult instanceof StatementResult) {
                return $statementResult;
            }
        } catch (ExceptionInterface $exception) {
            throw new StatementResultDeserializationException($exception->getMessage(), 0, $exception);
        }

        throw new StatementResultDeserializationException('Try to unserialized an empty StatementResult.', 0);
    }
}
