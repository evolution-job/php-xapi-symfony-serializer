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
    public function normalize(mixed $data, ?string $format = null, array $context = []): ?array
    {
        if (!$data instanceof Verb) {
            return null;
        }

        $map = ['id' => $data->getId()->getValue()];

        if (($display = $data->getDisplay()) instanceof LanguageMap) {
            $map['display'] = $this->normalizeAttribute($display, $format, $context);
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Verb;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): Verb
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
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return Verb::class === $type;
    }
}
