<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\Model\Interaction\InteractionComponent;

class InteractionComponentNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_supports_normalizing_interaction_component_objects(): void
    {
        $this->supportsNormalization(new InteractionComponent('test'))->shouldReturn(true);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_supports_denormalizing_to_interaction_component_objects(): void
    {
        $this->supportsDenormalization([], InteractionComponent::class)->shouldReturn(true);
    }
}
