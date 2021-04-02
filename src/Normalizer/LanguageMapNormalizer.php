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

use Symfony\Component\Serializer\Exception\UnexpectedValueException;
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
    public function normalize($object, $format = null, array $context = array())
    {
        if (!$object instanceof LanguageMap) {
            return;
        }

        $map = array();

        foreach ($object->languageTags() as $languageTag) {
            $map[$languageTag] = $object[$languageTag];
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof LanguageMap;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Language map is not valid.');
        }

        foreach ($data as $key => $value) {
            if (!LanguageMap::isValidTag($key)) {
                throw new UnexpectedValueException(sprintf('Language code "%s" is not valid.', $key));
            }
        }

        return LanguageMap::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return 'Xabbuh\XApi\Model\LanguageMap' === $type;
    }
}
