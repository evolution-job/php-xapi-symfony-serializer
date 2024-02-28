<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\Model\LanguageMap;

class LanguageMapNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_supports_normalizing_language_map_objects(): void
    {
        $this->supportsNormalization(new LanguageMap())->shouldReturn(true);
    }

    public function it_normalizes_language_map_instances_to_arrays(): void
    {
        $map = ['de-DE' => 'teilgenommen', 'en-GB' => 'attended', 'en-US' => 'attended'];

        $normalizedMap = $this->normalize(LanguageMap::create($map));

        $normalizedMap->shouldBeArray();
        $normalizedMap->shouldHaveCount(3);
        $normalizedMap->shouldHaveKeyWithValue('de-DE', 'teilgenommen');
        $normalizedMap->shouldHaveKeyWithValue('en-GB', 'attended');
        $normalizedMap->shouldHaveKeyWithValue('en-US', 'attended');
    }

    public function it_supports_denormalizing_to_language_map_objects(): void
    {
        $this->supportsDenormalization([], LanguageMap::class)->shouldReturn(true);
    }

    public function it_denormalizes_arrays_to_language_map_instances(): void
    {
        $map = ['de-DE' => 'teilgenommen', 'en-GB' => 'attended', 'en-US' => 'attended'];
        $languageMap = LanguageMap::create($map);

        $this->denormalize($map, LanguageMap::class)->equals($languageMap)->shouldReturn(true);
    }
}
