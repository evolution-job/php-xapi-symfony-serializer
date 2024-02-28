<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\DataFixtures\ActorFixtures;
use Xabbuh\XApi\Model\Agent;
use XApi\Fixtures\Json\ActorJsonFixtures;

class ActorNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_requires_an_iri_when_denormalizing_an_agent(): void
    {
        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('denormalize', [['objectType' => 'Agent'], Actor::class]);
    }

    public function it_can_denormalize_agents_with_mbox_sha1_sum(): void
    {
        $data = ['mbox_sha1sum' => 'db77b9104b531ecbb0b967f6942549d0ba80fda1'];

        $agent = $this->denormalize($data, Actor::class);

        $agent->shouldBeAnInstanceOf(Agent::class);
        $agent->getInverseFunctionalIdentifier()->getMboxSha1Sum()->shouldReturn('db77b9104b531ecbb0b967f6942549d0ba80fda1');
    }

    public function it_supports_normalizing_agents(): void
    {
        $this->supportsNormalization(ActorFixtures::getTypicalAgent())->shouldBe(true);
    }

    public function it_supports_normalizing_groups(): void
    {
        $this->supportsNormalization(ActorFixtures::getTypicalGroup())->shouldBe(true);
    }

    public function it_supports_denormalizing_agents(): void
    {
        $this->supportsDenormalization(ActorJsonFixtures::getTypicalAgent(), Actor::class)->shouldBe(true);
    }

    public function it_supports_denormalizing_groups(): void
    {
        $this->supportsDenormalization(ActorJsonFixtures::getTypicalGroup(), Actor::class)->shouldBe(true);
    }
}
