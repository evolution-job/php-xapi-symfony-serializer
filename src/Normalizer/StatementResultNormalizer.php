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

use Xabbuh\XApi\Model\IRL;
use Xabbuh\XApi\Model\StatementResult;

/**
 * Normalizes and denormalizes xAPI statement collections.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class StatementResultNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): ?array
    {
        if (!$data instanceof StatementResult) {
            return null;
        }

        $map = ['statements' => []];

        foreach ($data->getStatements() as $statement) {
            $map['statements'][] = $this->normalizeAttribute($statement, $format, $context);
        }

        if (($moreUrlPath = $data->getMoreUrlPath()) instanceof IRL) {
            $map['more'] = $moreUrlPath->getValue();
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof StatementResult;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): StatementResult
    {
        $statements = $this->denormalizeData($data['statements'], 'Xabbuh\XApi\Model\Statement[]', $format, $context);
        $moreUrlPath = null;

        if (isset($data['more'])) {
            $moreUrlPath = IRL::fromString($data['more']);
        }

        return new StatementResult($statements, $moreUrlPath);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return StatementResult::class === $type;
    }
}
