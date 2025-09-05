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

use ArrayObject;
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
    public function normalize(mixed $data, ?string $format = null, array $context = []): ArrayObject|array|null
    {
        if (!$data instanceof Context) {
            return null;
        }

        $map = [];

        if (null !== $registration = $data->getRegistration()) {
            $map['registration'] = $registration;
        }

        if (($instructor = $data->getInstructor()) instanceof Actor) {
            $map['instructor'] = $this->normalizeAttribute($instructor, $format, $context);
        }

        if (($team = $data->getTeam()) instanceof Group) {
            $map['team'] = $this->normalizeAttribute($team, $format, $context);
        }

        if (($contextActivities = $data->getContextActivities()) instanceof ContextActivities) {
            $map['contextActivities'] = $this->normalizeAttribute($contextActivities, $format, $context);
        }

        if (null !== $revision = $data->getRevision()) {
            $map['revision'] = $revision;
        }

        if (null !== $platform = $data->getPlatform()) {
            $map['platform'] = $platform;
        }

        if (null !== $language = $data->getLanguage()) {
            $map['language'] = $language;
        }

        if (($statement = $data->getStatement()) instanceof StatementReference) {
            $map['statement'] = $this->normalizeAttribute($statement, $format, $context);
        }

        if (($extensions = $data->getExtensions()) instanceof Extensions) {
            $map['extensions'] = $this->normalizeAttribute($extensions, $format, $context);
        }

        if ($map === []) {
            return new ArrayObject();
        }

        return $map;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Context;
    }

    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): Context
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

    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return Context::class === $type;
    }
}
