<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Serializer\Symfony\Normalizer;

use stdClass;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Xabbuh\XApi\Model\Score;

final class ScoreNormalizer extends Normalizer
{

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $score = new Score();

        if (isset($data['scaled'])) {
            if (!is_numeric($data['scaled']) || $data['scaled'] === (string) $data['scaled']) {
                throw new UnexpectedValueException('Score scaled is not a number.');
            }

            $score = $score->withScaled($data['scaled']);
        }

        if (isset($data['raw'])) {
            if (!is_numeric($data['raw']) || $data['raw'] === (string) $data['raw']) {
                throw new UnexpectedValueException('Score raw is not a number.');
            }

            $score = $score->withRaw($data['raw']);
        }

        if (isset($data['min'])) {
            if (!is_numeric($data['min']) || $data['min'] === (string) $data['min']) {
                throw new UnexpectedValueException('Score min is not a number.');
            }

            $score = $score->withMin($data['min']);
        }

        if (isset($data['max'])) {
            if (!is_numeric($data['max']) || $data['max'] === (string) $data['max']) {
                throw new UnexpectedValueException('Score max is not a number.');
            }

            $score = $score->withMax($data['max']);
        }

        return $score;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return 'Xabbuh\XApi\Model\Score' === $type;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        if (!$object instanceof Score) {
            return;
        }

        $data = [];

        if (null !== $scaled = $object->getScaled()) {
            $data['scaled'] = $scaled;
        }

        if (null !== $raw = $object->getRaw()) {
            $data['raw'] = $raw;
        }

        if (null !== $min = $object->getMin()) {
            $data['min'] = $min;
        }

        if (null !== $max = $object->getMax()) {
            $data['max'] = $max;
        }

        if (empty($data)) {
            return new stdClass();
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Score;
    }
}