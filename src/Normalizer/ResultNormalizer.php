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

use ArrayObject;
use Xabbuh\XApi\Model\Extensions;
use Xabbuh\XApi\Model\Result;
use Xabbuh\XApi\Model\Score;

/**
 * Normalizes and denormalizes xAPI statement results.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class ResultNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): ArrayObject|array|null
    {
        if (!$object instanceof Result) {
            return null;
        }

        $data = [];

        if ($object->getScore() instanceof Score) {
            $data['score'] = $this->normalizeAttribute($object->getScore(), Score::class, $context);
        }

        if (null !== $success = $object->getSuccess()) {
            $data['success'] = $success;
        }

        if (null !== $completion = $object->getCompletion()) {
            $data['completion'] = $completion;
        }

        if (null !== $response = $object->getResponse()) {
            $data['response'] = $response;
        }

        if (null !== $duration = $object->getDuration()) {
            $data['duration'] = $duration;
        }

        if (($extensions = $object->getExtensions()) instanceof Extensions) {
            $data['extensions'] = $this->normalizeAttribute($extensions, Extensions::class, $context);
        }

        if ($data === []) {
            return new ArrayObject();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Result;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $score = isset($data['score']) ? $this->denormalizeData($data['score'], Score::class, $format, $context) : null;
        $success = $data['success'] ?? null;
        $completion = $data['completion'] ?? null;
        $response = $data['response'] ?? null;
        $duration = $data['duration'] ?? null;
        $extensions = isset($data['extensions']) ? $this->denormalizeData($data['extensions'], Extensions::class, $format, $context) : null;

        return new Result($score, $success, $completion, $response, $duration, $extensions);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Result::class === $type;
    }
}
