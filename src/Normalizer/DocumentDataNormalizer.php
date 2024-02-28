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
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\Model\DocumentData;

/**
 * Normalizes and denormalizes xAPI statement documents.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class DocumentDataNormalizer implements DenormalizerInterface, NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): ?array
    {
        if (!$object instanceof DocumentData) {
            return null;
        }

        return $object->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof DocumentData;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new DocumentData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return DocumentData::class === $type;
    }
}
