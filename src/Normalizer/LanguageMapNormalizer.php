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

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\Model\LanguageMap;

/**
 * Normalizes and denormalizes {@link LanguageMap} instances.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class LanguageMapNormalizer implements DenormalizerInterface, NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): ?array
    {
        if (!$object instanceof LanguageMap) {
            return null;
        }

        $map = [];

        foreach ($object->languageTags() as $languageTag) {
            $map[$languageTag] = $object[$languageTag];
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof LanguageMap;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return LanguageMap::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return LanguageMap::class === $type;
    }
}
