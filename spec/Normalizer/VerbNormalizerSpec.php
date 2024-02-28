<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\Verb;

class VerbNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_supports_normalizing_verb_objects(): void
    {
        $this->supportsNormalization(new Verb(IRI::fromString('http://tincanapi.com/conformancetest/verbid')))->shouldReturn(true);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_supports_denormalizing_to_verb_objects(): void
    {
        $this->supportsDenormalization([], Verb::class)->shouldReturn(true);
    }
}
