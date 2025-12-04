<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Xabbuh\XApi\DataFixtures\StateFixtures;
use Xabbuh\XApi\Model\State;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ActorNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ObjectNormalizer;
use XApi\Fixtures\Json\StateJsonFixtures;

class StateNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_normalize_denormalizes_state(): void
    {
        $this->setSerializer(new Serializer([
            new ActorNormalizer(),
            new ObjectNormalizer()
        ]));

        $original = StateFixtures::getAllPropertiesState();

        $normalized = $this->normalize($original, State::class);

        $denormalized = $this->denormalize($normalized, State::class);

        $denormalized->shouldBeAnInstanceOf(State::class);
        $denormalized->equals($original)->shouldReturn(true);
    }

    public function it_supports_normalizing_state(): void
    {
        $this->supportsNormalization(StateFixtures::getMinimalState())->shouldBe(true);
    }

    public function it_supports_denormalizing_state(): void
    {
        $this->supportsDenormalization(StateJsonFixtures::getMinimalState(), State::class)->shouldBe(true);
    }
}
