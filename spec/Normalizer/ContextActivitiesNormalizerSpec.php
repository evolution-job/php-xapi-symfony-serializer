<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Xabbuh\XApi\DataFixtures\ContextActivitiesFixtures;
use Xabbuh\XApi\Model\ContextActivities;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ActorNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ObjectNormalizer;

class ContextActivitiesNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_normalize_denormalizes_context_activities(): void
    {
        $this->setSerializer(new Serializer([
            new ActorNormalizer(),
            new ObjectNormalizer(),
            new ArrayDenormalizer(),
        ]));

        $original = ContextActivitiesFixtures::getAllPropertiesActivities();

        $normalized = $this->normalize($original, ContextActivities::class);

        $denormalized = $this->denormalize($normalized, ContextActivities::class);

        $denormalized->shouldBeAnInstanceOf(ContextActivities::class);

        $denormalized->getCategoryActivities()->shouldBeArray();
        $denormalized->getCategoryActivities()[0]->getId()->getValue()->shouldBeEqualTo($original->getCategoryActivities()[0]->getId()->getValue());

        $denormalized->getGroupingActivities()->shouldBeArray();
        $denormalized->getGroupingActivities()[0]->getId()->getValue()->shouldBeEqualTo($original->getGroupingActivities()[0]->getId()->getValue());

        $denormalized->getOtherActivities()->shouldBeArray();
        $denormalized->getOtherActivities()[0]->getId()->getValue()->shouldBeEqualTo($original->getOtherActivities()[0]->getId()->getValue());

        $denormalized->getParentActivities()->shouldBeArray();
        $denormalized->getParentActivities()[0]->getId()->getValue()->shouldBeEqualTo($original->getParentActivities()[0]->getId()->getValue());
    }

    public function it_supports_normalizing_context_activities(): void
    {
        $this->supportsNormalization(ContextActivitiesFixtures::getAllPropertiesActivities())->shouldBe(true);
    }

    public function it_supports_denormalizing_context_activities(): void
    {
        $this->supportsDenormalization(ContextActivitiesFixtures::getAllPropertiesActivities(), ContextActivities::class)->shouldBe(true);
    }
}
