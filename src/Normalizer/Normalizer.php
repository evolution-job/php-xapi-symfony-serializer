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
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class Normalizer implements DenormalizerInterface, NormalizerInterface, SerializerAwareInterface
{
    private ?SerializerInterface $serializer = null;

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
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @throws ExceptionInterface
     */
    protected function normalizeAttribute($value, ?string $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException('Cannot normalize attribute because the injected serializer is not a normalizer');
        }

        return $this->serializer->normalize($value, $format, $context);
    }

    /**
     * @throws ExceptionInterface
     */
    protected function denormalizeData(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        if (!$this->serializer instanceof DenormalizerInterface) {
            throw new LogicException('Cannot denormalize because the injected serializer is not a denormalizer');
        }

        return $this->serializer->denormalize($data, $type, $format, $context);
    }
}
