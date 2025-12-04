<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Xabbuh\XApi\DataFixtures\ContextFixtures;
use Xabbuh\XApi\Model\Context;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ActorNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ContextActivitiesNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ExtensionsNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ObjectNormalizer;

class ContextNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_normalize_denormalizes_context(): void
    {
        $this->setSerializer(new Serializer([
            new ActorNormalizer(),
            new ContextActivitiesNormalizer(),
            new ExtensionsNormalizer(),
            new ObjectNormalizer(),
            new ArrayDenormalizer(),
        ]));

        $original = ContextFixtures::getAllPropertiesContext();

        $normalized = $this->normalize($original, Context::class);

        $denormalized = $this->denormalize($normalized, Context::class);

        $denormalized->shouldBeAnInstanceOf(Context::class);
        $denormalized->equals($original)->shouldReturn(true);
    }

    public function it_supports_normalizing_context_objects(): void
    {
        $this->supportsNormalization(new Context())->shouldReturn(true);
    }

    public function it_supports_denormalizing_to_context_objects(): void
    {
        $this->supportsDenormalization([], Context::class)->shouldReturn(true);
    }
}
