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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Normalizer wrapping Symfony's PropertyNormalizer to filter null values.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class FilterNullValueNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    private readonly PropertyNormalizer $propertyNormalizer;

    public function __construct()
    {
        $this->propertyNormalizer = new PropertyNormalizer();
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
    public function normalize($object, $format = null, array $context = []): ArrayObject
    {
        $data = $this->propertyNormalizer->normalize($object, $format, $context);
        $arrayObject = new ArrayObject();

        foreach ($data as $key => $value) {
            if (null !== $value) {
                $arrayObject[$key] = $value;
            }
        }

        return $arrayObject;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $this->propertyNormalizer->supportsNormalization($data, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->propertyNormalizer->setSerializer($serializer);
    }
}
