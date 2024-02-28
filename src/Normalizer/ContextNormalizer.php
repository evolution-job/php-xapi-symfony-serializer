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

use stdClass;
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Context;
use Xabbuh\XApi\Model\ContextActivities;
use Xabbuh\XApi\Model\Extensions;
use Xabbuh\XApi\Model\Group;
use Xabbuh\XApi\Model\StatementReference;

/**
 * Normalizes and denormalizes xAPI statement contexts.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class ContextNormalizer extends Normalizer
{
    public function normalize($object, $format = null, array $context = []): stdClass|array|null
    {
        if (!$object instanceof Context) {
            return null;
        }

        $data = [];

        if (null !== $registration = $object->getRegistration()) {
            $data['registration'] = $registration;
        }

        if (($instructor = $object->getInstructor()) instanceof Actor) {
            $data['instructor'] = $this->normalizeAttribute($instructor, $format, $context);
        }

        if (($team = $object->getTeam()) instanceof Group) {
            $data['team'] = $this->normalizeAttribute($team, $format, $context);
        }

        if (($contextActivities = $object->getContextActivities()) instanceof ContextActivities) {
            $data['contextActivities'] = $this->normalizeAttribute($contextActivities, $format, $context);
        }

        if (null !== $revision = $object->getRevision()) {
            $data['revision'] = $revision;
        }

        if (null !== $platform = $object->getPlatform()) {
            $data['platform'] = $platform;
        }

        if (null !== $language = $object->getLanguage()) {
            $data['language'] = $language;
        }

        if (($statement = $object->getStatement()) instanceof StatementReference) {
            $data['statement'] = $this->normalizeAttribute($statement, $format, $context);
        }

        if (($extensions = $object->getExtensions()) instanceof Extensions) {
            $data['extensions'] = $this->normalizeAttribute($extensions, $format, $context);
        }

        if ($data === []) {
            return new stdClass();
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Context;
    }

    public function denormalize($data, $type, $format = null, array $context = []): Context
    {
        $statementContext = new Context();

        if (isset($data['registration'])) {
            $statementContext = $statementContext->withRegistration($data['registration']);
        }

        if (isset($data['instructor'])) {
            $statementContext = $statementContext->withInstructor($this->denormalizeData($data['instructor'], Actor::class, $format, $context));
        }

        if (isset($data['team'])) {
            $statementContext = $statementContext->withTeam($this->denormalizeData($data['team'], Group::class, $format, $context));
        }

        if (isset($data['contextActivities'])) {
            $statementContext = $statementContext->withContextActivities($this->denormalizeData($data['contextActivities'], ContextActivities::class, $format, $context));
        }

        if (isset($data['revision'])) {
            $statementContext = $statementContext->withRevision($data['revision']);
        }

        if (isset($data['platform'])) {
            $statementContext = $statementContext->withPlatform($data['platform']);
        }

        if (isset($data['language'])) {
            $statementContext = $statementContext->withLanguage($data['language']);
        }

        if (isset($data['statement'])) {
            $statementContext = $statementContext->withStatement($this->denormalizeData($data['statement'], StatementReference::class, $format, $context));
        }

        if (isset($data['extensions'])) {
            return $statementContext->withExtensions($this->denormalizeData($data['extensions'], Extensions::class, $format, $context));
        }

        return $statementContext;
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Context::class === $type;
    }
}
