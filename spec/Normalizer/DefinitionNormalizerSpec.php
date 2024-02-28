<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\Model\Definition;
use Xabbuh\XApi\Model\Interaction\ChoiceInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\FillInInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\LikertInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\LongFillInInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\MatchingInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\NumericInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\OtherInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\PerformanceInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\SequencingInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\TrueFalseInteractionDefinition;

class DefinitionNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_supports_normalizing_definition_objects(): void
    {
        $this->supportsNormalization(new Definition())->shouldReturn(true);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_supports_denormalizing_to_definition_objects(): void
    {
        $this->supportsDenormalization([], Definition::class)->shouldReturn(true);
    }

    public function it_supports_denormalizing_to_interaction_definition_objects(): void
    {
        $this->supportsDenormalization([], ChoiceInteractionDefinition::class)->shouldReturn(true);
        $this->supportsDenormalization([], FillInInteractionDefinition::class)->shouldReturn(true);
        $this->supportsDenormalization([], LikertInteractionDefinition::class)->shouldReturn(true);
        $this->supportsDenormalization([], LongFillInInteractionDefinition::class)->shouldReturn(true);
        $this->supportsDenormalization([], MatchingInteractionDefinition::class)->shouldReturn(true);
        $this->supportsDenormalization([], NumericInteractionDefinition::class)->shouldReturn(true);
        $this->supportsDenormalization([], OtherInteractionDefinition::class)->shouldReturn(true);
        $this->supportsDenormalization([], PerformanceInteractionDefinition::class)->shouldReturn(true);
        $this->supportsDenormalization([], SequencingInteractionDefinition::class)->shouldReturn(true);
        $this->supportsDenormalization([], TrueFalseInteractionDefinition::class)->shouldReturn(true);
    }

    public function it_throws_an_exception_when_an_unknown_interaction_type_should_be_denormalized(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('denormalize', [['interactionType' => 'foo'], Definition::class]);
    }
}
