<?php

namespace spec\Xabbuh\XApi\Serializer\Symfony\Normalizer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Xabbuh\XApi\Model\Attachment;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\LanguageMap;

class AttachmentNormalizerSpec extends ObjectBehavior
{
    public function it_is_a_normalizer(): void
    {
        $this->shouldHaveType(NormalizerInterface::class);
    }

    public function it_supports_normalizing_attachment_objects(): void
    {
        $attachment = new Attachment(
            IRI::fromString('http://id.tincanapi.com/attachment/supporting_media'),
            'text/plain',
            18,
            'bd1a58265d96a3d1981710dab8b1e1ed04a8d7557ea53ab0cf7b44c04fd01545',
            LanguageMap::create(['en-US' => 'Text attachment']),
            null,
            null,
            'some text content'
        );

        $this->supportsNormalization($attachment)->shouldReturn(true);
    }

    public function it_is_a_denormalizer(): void
    {
        $this->shouldHaveType(DenormalizerInterface::class);
    }

    public function it_supports_denormalizing_to_attachment_objects(): void
    {
        $this->supportsDenormalization([], Attachment::class)->shouldReturn(true);
    }
}
