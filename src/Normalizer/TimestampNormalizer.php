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

use DateMalformedStringException;
use DateTime;
use DateTimeInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes and denormalizes xAPI statement timestamps.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class TimestampNormalizer implements DenormalizerInterface, NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): ?DateTime
    {
        try {
            return new DateTime($data);
        } catch (DateMalformedStringException) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return 'DateTime' === $type;
    }

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
    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        if (!$data instanceof DateTimeInterface) {
            throw new InvalidArgumentException(sprintf('Expected \DateTime object or object implementing \DateTimeInterface (got "%s").', get_debug_type($data)));
        }

        return $data->format('c');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof DateTimeInterface;
    }
}
