<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Serializer\Symfony\Normalizer;

use DateTime;
use Xabbuh\XApi\Common\Exception\UnsupportedStatementVersionException;
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Context;
use Xabbuh\XApi\Model\Result;
use Xabbuh\XApi\Model\Statement;
use Xabbuh\XApi\Model\StatementId;
use Xabbuh\XApi\Model\StatementObject;
use Xabbuh\XApi\Model\Verb;

/**
 * Normalizes and denormalizes xAPI statements.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class StatementNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): ?array
    {
        if (!$data instanceof Statement) {
            return null;
        }

        $map = ['actor' => $this->normalizeAttribute($data->getActor(), $format, $context), 'verb' => $this->normalizeAttribute($data->getVerb(), $format, $context), 'object' => $this->normalizeAttribute($data->getObject(), $format, $context)];

        if (($id = $data->getId()) instanceof StatementId) {
            $map['id'] = $id->getValue();
        }

        if (($authority = $data->getAuthority()) instanceof Actor) {
            $map['authority'] = $this->normalizeAttribute($authority, $format, $context);
        }

        if (($result = $data->getResult()) instanceof Result) {
            $map['result'] = $this->normalizeAttribute($result, $format, $context);
        }

        if (($result = $data->getCreated()) instanceof DateTime) {
            $map['timestamp'] = $this->normalizeAttribute($result, $format, $context);
        }

        if (($result = $data->getStored()) instanceof DateTime) {
            $map['stored'] = $this->normalizeAttribute($result, $format, $context);
        }

        if ($data->getContext() instanceof Context) {
            $map['context'] = $this->normalizeAttribute($data->getContext(), $format, $context);
        }

        if (null !== $attachments = $data->getAttachments()) {
            $map['attachments'] = $this->normalizeAttribute($attachments, $format, $context);
        }

        if (null !== $version = $data->getVersion()) {
            $map['version'] = $version;
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Statement;
    }

    /**
     * {@inheritdoc}
     * @throws UnsupportedStatementVersionException
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): Statement
    {
        $version = null;

        if (isset($data['version'])) {
            $version = $data['version'];

            if (preg_match('/^1\.0(?:\.\d+)?$/', (string)$version) === 0 || preg_match('/^1\.0(?:\.\d+)?$/', (string)$version) === 0 || preg_match('/^1\.0(?:\.\d+)?$/', (string)$version) === false) {
                throw new UnsupportedStatementVersionException(sprintf('Statements at version "%s" are not supported.', $version));
            }
        }

        $id = isset($data['id']) ? StatementId::fromString($data['id']) : null;
        $actor = $this->denormalizeData($data['actor'], Actor::class, $format, $context);
        $verb = $this->denormalizeData($data['verb'], Verb::class, $format, $context);
        $object = $this->denormalizeData($data['object'], StatementObject::class, $format, $context);

        $result = null;
        $authority = null;
        $created = null;
        $stored = null;
        $statementContext = null;
        $attachments = null;

        if (isset($data['result'])) {
            $result = $this->denormalizeData($data['result'], Result::class, $format, $context);
        }

        if (isset($data['authority'])) {
            $authority = $this->denormalizeData($data['authority'], Actor::class, $format, $context);
        }

        if (isset($data['timestamp'])) {
            $created = $this->denormalizeData($data['timestamp'], 'DateTime', $format, $context);
        }

        if (isset($data['stored'])) {
            $stored = $this->denormalizeData($data['stored'], 'DateTime', $format, $context);
        }

        if (isset($data['context'])) {
            $statementContext = $this->denormalizeData($data['context'], Context::class, $format, $context);
        }

        if (isset($data['attachments'])) {
            $attachments = $this->denormalizeData($data['attachments'], 'Xabbuh\XApi\Model\Attachment[]', $format, $context);
        }

        return new Statement($id, $actor, $verb, $object, $result, $authority, $created, $stored, $statementContext, $attachments, $version);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return Statement::class === $type;
    }
}
