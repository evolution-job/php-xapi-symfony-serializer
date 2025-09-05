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

use JsonException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\Agent;
use Xabbuh\XApi\Model\State;

/**
 * Normalizes and denormalizes xAPI statements.
 *
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
final class StateNormalizer extends Normalizer
{
    public function normalize(mixed $data, ?string $format = null, array $context = []): ?array
    {

        if (!$data instanceof State) {
            return null;
        }

        $map = [];

        if (null !== $activity = $data->getActivity()->getId()->getValue()) {
            $map['activityId'] = $this->normalizeAttribute($activity, $format, $context);
        }

        $agent = $data->getAgent();
        $map['agent'] = $this->normalizeAttribute($agent, $format, $context);

        if (null !== $stateId = $data->getStateId()) {
            $map['stateId'] = $this->normalizeAttribute($stateId, $format, $context);
        }

        if (null !== $registrationId = $data->getRegistrationId()) {
            $map['registrationId'] = $this->normalizeAttribute($registrationId, $format, $context);
        }

        if (null !== $data = $data->getData()) {
            $map['data'] = $this->normalizeAttribute($data, $format, $context);
        }

        return $map;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof State;
    }

    /**
     * @throws JsonException
     * @throws ExceptionInterface
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): array|State
    {
        // set of States
        if (isset($data[0])) {
            $stateIds = [];
            foreach ($data as $d) {
                $stateIds[] = $this->denormarlizeState($d, $format);
            }

            return $stateIds;
        }

        // Once
        return $this->denormarlizeState($data, $format);
    }

    /**
     * @throws ExceptionInterface
     * @throws JsonException
     */
    public function denormarlizeState(?array $state, ?string $format = null): State
    {
        $activity = null;
        if (isset($state['activityId'])) {
            $activity = $this->denormalizeData(['id' => $state['activityId']], Activity::class, $format);
        }

        $agent = null;
        if (isset($state['agent'])) {
            $agent = $this->denormalizeData(json_decode((string)$state['agent'], true, 512, JSON_THROW_ON_ERROR), Agent::class, $format);
        }

        $stateId = $state['stateId'] ?? null;

        $registrationId = $state['registration'] ?? null;

        if (!is_array($state['data']) && !is_null($decoded = json_decode((string)$state['data'], true, 512, JSON_THROW_ON_ERROR))) {
            $data = $decoded;
        } else {
            $data = $state['data'];
        }

        return new State($activity, $agent, $stateId, $registrationId, $data);
    }

    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return State::class === $type;
    }
}
