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
    public function normalize(mixed $data, ?string $format = null, array $context = []): ArrayObject|array|null
    {
        if (!$data instanceof Extensions) {
            return null;
        }

        $extensions = $data->getExtensions();

        if (count($extensions) === 0) {
            return new ArrayObject();
        }

        $map = [];

        foreach ($extensions as $extension) {
            $map[$extension->getValue()] = $extensions[$extension];
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): Extensions
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
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return Extensions::class === $type;
    }
}
