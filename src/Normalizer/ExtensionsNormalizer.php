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
use SplObjectStorage;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\Model\Extensions;
use Xabbuh\XApi\Model\IRI;

/**
 * Normalizes and denormalizes xAPI extensions.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class ExtensionsNormalizer implements DenormalizerInterface, NormalizerInterface
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
    public function normalize($object, $format = null, array $context = []): ArrayObject|array|null
    {
        if (!$object instanceof Extensions) {
            return null;
        }

        $extensions = $object->getExtensions();

        if (count($extensions) === 0) {
            return new ArrayObject();
        }

        $data = [];

        foreach ($extensions as $extension) {
            $data[$extension->getValue()] = $extensions[$extension];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $extensions = new SplObjectStorage();

        foreach ($data as $iri => $value) {
            $extensions->attach(IRI::fromString($iri), $value);
        }

        return new Extensions($extensions);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Extensions::class === $type;
    }
}
