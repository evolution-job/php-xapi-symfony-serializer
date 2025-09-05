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
    public function normalize(mixed $data, ?string $format = null, array $context = []): ?array
    {
        if (!$data instanceof Attachment) {
            return null;
        }

        $map = [
            'usageType'   => $data->getUsageType()->getValue(),
            'contentType' => $data->getContentType(),
            'length'      => $data->getLength(),
            'sha2'        => $data->getSha2(),
            'display'     => $this->normalizeAttribute($data->getDisplay(), $format, $context)
        ];

        if (($description = $data->getDescription()) instanceof LanguageMap) {
            $map['description'] = $this->normalizeAttribute($description, $format, $context);
        }

        if (($fileUrl = $data->getFileUrl()) instanceof IRL) {
            $map['fileUrl'] = $fileUrl->getValue();
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Attachment;
    }

    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): Attachment
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

    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return Attachment::class === $type;
    }
}
