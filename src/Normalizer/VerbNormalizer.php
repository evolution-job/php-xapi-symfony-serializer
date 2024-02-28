<?php

namespace Xabbuh\XApi\Serializer\Symfony\Normalizer;

use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\LanguageMap;
use Xabbuh\XApi\Model\Verb;

/**
 * Denormalizes PHP arrays to {@link Verb} objects.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class VerbNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): ?array
    {
        if (!$object instanceof Verb) {
            return null;
        }

        $data = ['id' => $object->getId()->getValue()];

        if (($display = $object->getDisplay()) instanceof LanguageMap) {
            $data['display'] = $this->normalizeAttribute($display, $format, $context);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Verb;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $iri = IRI::fromString($data['id']);
        $display = null;

        if (isset($data['display'])) {
            $display = $this->denormalizeData($data['display'], LanguageMap::class, $format, $context);
        }

        return new Verb($iri, $display);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Verb::class === $type;
    }
}
