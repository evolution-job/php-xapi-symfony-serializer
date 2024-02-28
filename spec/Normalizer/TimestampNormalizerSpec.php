<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use DateTime;
use DateTimeZone;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TimestampNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_can_normalize_datetime_objects(): void
    {
        $this->supportsNormalization(new DateTime())->shouldBe(true);
    }

    public function it_cannot_normalize_datetime_like_string(): void
    {
        $this->supportsNormalization('2004-02-12T15:19:21+00:00')->shouldBe(false);
    }

    public function it_normalizes_datetime_objects_as_iso_8601_formatted_strings(): void
    {
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        $dateTime->setDate(2004, 2, 12);
        $dateTime->setTime(15, 19, 21);

        $this->normalize($dateTime)->shouldReturn('2004-02-12T15:19:21+00:00');
    }

    public function it_throws_an_exception_when_data_other_than_datetime_objects_are_passed(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('normalize', ['2004-02-12T15:19:21+00:00']);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_can_denormalize_to_datetime_objects(): void
    {
        $this->supportsDenormalization('2004-02-12T15:19:21+00:00', 'DateTime')->shouldBe(true);
    }

    public function it_denormalizes_iso_8601_formatted_strings_to_datetime_objects(): void
    {
        $date = $this->denormalize('2004-02-12T15:19:21+00:00', 'DateTime');
        $date->getTimezone()->shouldBeLike(new DateTimeZone('+00:00'));

        $date->format('Y-m-d H:i:s')->shouldReturn('2004-02-12 15:19:21');
    }
}
