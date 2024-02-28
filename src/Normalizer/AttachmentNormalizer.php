<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Serializer\Symfony\Normalizer;

use stdClass;
use Xabbuh\XApi\Model\Attachment;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\IRL;
use Xabbuh\XApi\Model\LanguageMap;

/**
 * Denormalizes PHP arrays to {@link Attachment} objects.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class AttachmentNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): ?array
    {
        if (!$object instanceof Attachment) {
            return null;
        }

        $data = ['usageType' => $object->getUsageType()->getValue(), 'contentType' => $object->getContentType(), 'length' => $object->getLength(), 'sha2' => $object->getSha2(), 'display' => $this->normalizeAttribute($object->getDisplay(), $format, $context)];

        if (($description = $object->getDescription()) instanceof LanguageMap) {
            $data['description'] = $this->normalizeAttribute($description, $format, $context);
        }

        if (($fileUrl = $object->getFileUrl()) instanceof IRL) {
            $data['fileUrl'] = $fileUrl->getValue();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Attachment;
    }

    public function denormalize($data, $type, $format = null, array $context = []): Attachment
    {
        $display = $this->denormalizeData($data['display'], LanguageMap::class, $format, $context);
        $description = null;
        $fileUrl = null;
        $content = null;

        if (isset($data['description'])) {
            $description = $this->denormalizeData($data['description'], LanguageMap::class, $format, $context);
        }

        if (isset($data['fileUrl'])) {
            $fileUrl = IRL::fromString($data['fileUrl']);
        }

        if (isset($context['xapi_attachments'][$data['sha2']])) {
            $content = $context['xapi_attachments'][$data['sha2']]['content'];
        }

        return new Attachment(IRI::fromString($data['usageType']), $data['contentType'], $data['length'], $data['sha2'], $display, $description, $fileUrl, $content);
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Attachment::class === $type;
    }
}
