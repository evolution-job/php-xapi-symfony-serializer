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

use Xabbuh\XApi\Model\Interaction\InteractionComponent;
use Xabbuh\XApi\Model\LanguageMap;

/**
 * Denormalizes xAPI statement activity {@link InteractionComponent interaction components}.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class InteractionComponentNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): ?array
    {
        if (!$object instanceof InteractionComponent) {
            return null;
        }

        $data = ['id' => $object->getId()];

        if (($description = $object->getDescription()) instanceof LanguageMap) {
            $data['description'] = $this->normalizeAttribute($description, $format, $context);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof InteractionComponent;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $description = null;

        if (isset($data['description'])) {
            $description = $this->denormalizeData($data['description'], LanguageMap::class, $format, $context);
        }

        return new InteractionComponent($data['id'], $description);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return InteractionComponent::class === $type;
    }
}
