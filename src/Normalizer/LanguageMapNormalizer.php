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
    public function getSupportedTypes(?string $format): array
    {
        return [
            'json'   => true,
            'object' => true,
            '*'      => false
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): ?array
    {
        if (!$data instanceof LanguageMap) {
            return null;
        }

        $map = [];

        foreach ($data->languageTags() as $languageTag) {
            $map[$languageTag] = $data[$languageTag];
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof LanguageMap;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): LanguageMap
    {
        return LanguageMap::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return LanguageMap::class === $type;
    }
}
