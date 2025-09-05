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
    public function normalize(mixed $data, ?string $format = null, array $context = []): ?array
    {
        if (!$data instanceof InteractionComponent) {
            return null;
        }

        $map = ['id' => $data->getId()];

        if (($description = $data->getDescription()) instanceof LanguageMap) {
            $map['description'] = $this->normalizeAttribute($description, $format, $context);
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof InteractionComponent;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): InteractionComponent
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
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return InteractionComponent::class === $type;
    }
}
