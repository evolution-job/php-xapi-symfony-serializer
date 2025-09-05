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

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Context;
use Xabbuh\XApi\Model\Definition;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\Result;
use Xabbuh\XApi\Model\StatementId;
use Xabbuh\XApi\Model\StatementObject;
use Xabbuh\XApi\Model\StatementReference;
use Xabbuh\XApi\Model\SubStatement;
use Xabbuh\XApi\Model\Verb;

/**
 * Normalizes and denormalizes xAPI statement objects.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class ObjectNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): ?array
    {
        if ($data instanceof Activity) {
            $activityData = ['objectType' => 'Activity', 'id' => $data->getId()->getValue()];

            if (($definition = $data->getDefinition()) instanceof Definition) {
                $activityData['definition'] = $this->normalizeAttribute($definition, $format, $context);
            }

            return $activityData;
        }

        if ($data instanceof StatementReference) {
            return ['objectType' => 'StatementRef', 'id' => $data->getStatementId()->getValue()];
        }

        if ($data instanceof SubStatement) {

            $map = [
                'objectType' => 'SubStatement',
                'actor'      => $this->normalizeAttribute($data->getActor(), $format, $context),
                'verb'       => $this->normalizeAttribute($data->getVerb(), $format, $context),
                'object'     => $this->normalizeAttribute($data->getObject(), $format, $context)
            ];

            if (($result = $data->getResult()) instanceof Result) {
                $map['result'] = $this->normalizeAttribute($result, $format, $context);
            }

            if (($statementContext = $data->getContext()) instanceof Context) {
                $map['context'] = $this->normalizeAttribute($statementContext, $format, $context);
            }

            return $map;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof StatementObject;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): mixed
    {
        if (!isset($data['objectType']) || 'Activity' === $data['objectType']) {
            return $this->denormalizeActivity($data, $format, $context);
        }

        if (('Agent' === $data['objectType'] || 'Group' === $data['objectType'])) {
            return $this->denormalizeData($data, Actor::class, $format, $context);
        }

        if ('SubStatement' === $data['objectType']) {
            return $this->denormalizeSubStatement($data, $format, $context);
        }

        if ('StatementRef' === $data['objectType']) {
            return new StatementReference(StatementId::fromString($data['id']));
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return in_array($type, [Activity::class, StatementObject::class, StatementReference::class, SubStatement::class], true);
    }

    /**
     * @throws ExceptionInterface
     */
    private function denormalizeActivity(array $data, ?string $format = null, array $context = []): Activity
    {
        $definition = null;

        if (isset($data['definition'])) {
            $definition = $this->denormalizeData($data['definition'], Definition::class, $format, $context);
        }

        return new Activity(IRI::fromString($data['id']), $definition);
    }

    /**
     * @throws ExceptionInterface
     */
    private function denormalizeSubStatement(array $data, ?string $format = null, array $context = []): SubStatement
    {
        $actor = $this->denormalizeData($data['actor'], Actor::class, $format, $context);
        $verb = $this->denormalizeData($data['verb'], Verb::class, $format, $context);
        $object = $this->denormalizeData($data['object'], StatementObject::class, $format, $context);

        $result = null;
        $statementContext = null;

        if (isset($data['result'])) {
            $result = $this->denormalizeData($data['result'], Result::class, $format, $context);
        }

        if (isset($data['context'])) {
            $statementContext = $this->denormalizeData($data['context'], Context::class, $format, $context);
        }

        return new SubStatement($actor, $verb, $object, $result, $statementContext);
    }
}
