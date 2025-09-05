<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\DataFixtures\StatementFixtures;
use Xabbuh\XApi\Model\Statement;
use XApi\Fixtures\Json\StatementJsonFixtures;

class StatementNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_supports_normalizing_statements(): void
    {
        $this->supportsNormalization(StatementFixtures::getMinimalStatement())->shouldBe(true);
    }

    public function it_supports_denormalizing_statements(): void
    {
        $this->supportsDenormalization(StatementJsonFixtures::getMinimalStatement(), Statement::class)->shouldBe(true);
    }
}
