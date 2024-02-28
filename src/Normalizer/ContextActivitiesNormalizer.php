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
    public function normalize($object, $format = null, array $context = []): stdClass|array|null
    {
        if (!$object instanceof ContextActivities) {
            return null;
        }

        $data = [];

        if (null !== $categoryActivities = $object->getCategoryActivities()) {
            $data['category'] = $this->normalizeAttribute($categoryActivities);
        }

        if (null !== $parentActivities = $object->getParentActivities()) {
            $data['parent'] = $this->normalizeAttribute($parentActivities);
        }

        if (null !== $groupingActivities = $object->getGroupingActivities()) {
            $data['grouping'] = $this->normalizeAttribute($groupingActivities);
        }

        if (null !== $otherActivities = $object->getOtherActivities()) {
            $data['other'] = $this->normalizeAttribute($otherActivities);
        }

        if ($data === []) {
            return new stdClass();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof ContextActivities;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
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
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ContextActivities::class === $type;
    }
}
