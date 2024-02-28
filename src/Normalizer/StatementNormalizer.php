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
    public function normalize($object, $format = null, array $context = []): ?array
    {
        if (!$object instanceof Statement) {
            return null;
        }

        $data = ['actor' => $this->normalizeAttribute($object->getActor(), $format, $context), 'verb' => $this->normalizeAttribute($object->getVerb(), $format, $context), 'object' => $this->normalizeAttribute($object->getObject(), $format, $context)];

        if (($id = $object->getId()) instanceof StatementId) {
            $data['id'] = $id->getValue();
        }

        if (($authority = $object->getAuthority()) instanceof Actor) {
            $data['authority'] = $this->normalizeAttribute($authority, $format, $context);
        }

        if (($result = $object->getResult()) instanceof Result) {
            $data['result'] = $this->normalizeAttribute($result, $format, $context);
        }

        if (($result = $object->getCreated()) instanceof DateTime) {
            $data['timestamp'] = $this->normalizeAttribute($result, $format, $context);
        }

        if (($result = $object->getStored()) instanceof DateTime) {
            $data['stored'] = $this->normalizeAttribute($result, $format, $context);
        }

        if ($object->getContext() instanceof Context) {
            $data['context'] = $this->normalizeAttribute($object->getContext(), $format, $context);
        }

        if (null !== $attachments = $object->getAttachments()) {
            $data['attachments'] = $this->normalizeAttribute($attachments, $format, $context);
        }

        if (null !== $version = $object->getVersion()) {
            $data['version'] = $version;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Statement;
    }

    /**
     * {@inheritdoc}
     * @throws UnsupportedStatementVersionException
     */
    public function denormalize($data, $type, $format = null, array $context = [])
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
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Statement::class === $type;
    }
}
