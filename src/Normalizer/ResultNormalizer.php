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
    public function normalize(mixed $data, ?string $format = null, array $context = []): ArrayObject|array|null
    {
        if (!$data instanceof Result) {
            return null;
        }

        $map = [];

        if ($data->getScore() instanceof Score) {
            $map['score'] = $this->normalizeAttribute($data->getScore(), Score::class, $context);
        }

        if (null !== $success = $data->getSuccess()) {
            $map['success'] = $success;
        }

        if (null !== $completion = $data->getCompletion()) {
            $map['completion'] = $completion;
        }

        if (null !== $response = $data->getResponse()) {
            $map['response'] = $response;
        }

        if (null !== $duration = $data->getDuration()) {
            $map['duration'] = $duration;
        }

        if (($extensions = $data->getExtensions()) instanceof Extensions) {
            $map['extensions'] = $this->normalizeAttribute($extensions, Extensions::class, $context);
        }

        if ($map === []) {
            return new ArrayObject();
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Result;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): Result
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
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return Result::class === $type;
    }
}
