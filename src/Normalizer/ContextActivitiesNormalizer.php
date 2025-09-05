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
use Xabbuh\XApi\Model\ContextActivities;

/**
 * Normalizes and denormalizes xAPI statement context activities.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class ContextActivitiesNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): ArrayObject|array|null
    {
        if (!$data instanceof ContextActivities) {
            return null;
        }

        $map = [];

        if (null !== $categoryActivities = $data->getCategoryActivities()) {
            $map['category'] = $this->normalizeAttribute($categoryActivities);
        }

        if (null !== $parentActivities = $data->getParentActivities()) {
            $map['parent'] = $this->normalizeAttribute($parentActivities);
        }

        if (null !== $groupingActivities = $data->getGroupingActivities()) {
            $map['grouping'] = $this->normalizeAttribute($groupingActivities);
        }

        if (null !== $otherActivities = $data->getOtherActivities()) {
            $map['other'] = $this->normalizeAttribute($otherActivities);
        }

        if ($map === []) {
            return new ArrayObject();
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ContextActivities;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): ContextActivities
    {
        $parentActivities = null;
        $groupingActivities = null;
        $categoryActivities = null;
        $otherActivities = null;

        if (isset($data['parent'])) {
            $parentActivities = $this->denormalizeData($data['parent'], 'Xabbuh\XApi\Model\Activity[]', $format, $context);
        }

        if (isset($data['grouping'])) {
            $groupingActivities = $this->denormalizeData($data['grouping'], 'Xabbuh\XApi\Model\Activity[]', $format, $context);
        }

        if (isset($data['category'])) {
            $categoryActivities = $this->denormalizeData($data['category'], 'Xabbuh\XApi\Model\Activity[]', $format, $context);
        }

        if (isset($data['other'])) {
            $otherActivities = $this->denormalizeData($data['other'], 'Xabbuh\XApi\Model\Activity[]', $format, $context);
        }

        return new ContextActivities($parentActivities, $groupingActivities, $categoryActivities, $otherActivities);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return ContextActivities::class === $type;
    }
}
