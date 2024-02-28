<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\DataFixtures\AccountFixtures;
use Xabbuh\XApi\Model\Account;
use Xabbuh\XApi\Model\IRL;
use XApi\Fixtures\Json\AccountJsonFixtures;

class AccountNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_supports_normalizing_accounts(): void
    {
        $this->supportsNormalization(AccountFixtures::getTypicalAccount())->shouldBe(true);
    }

    public function it_denormalizes_accounts(): void
    {
        $account = $this->denormalize(['homePage' => 'https://tincanapi.com', 'name' => 'test'], Account::class);

        $account->shouldBeAnInstanceOf(Account::class);
        $account->getHomePage()->equals(IRL::fromString('https://tincanapi.com'))->shouldReturn(true);
        $account->getName()->shouldReturn('test');
    }

    public function it_supports_denormalizing_accounts(): void
    {
        $this->supportsDenormalization(AccountJsonFixtures::getTypicalAccount(), Account::class)->shouldBe(true);
    }
}
