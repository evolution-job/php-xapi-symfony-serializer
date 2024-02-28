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
    public function normalize($object, $format = null, array $context = []): ?array
    {

        if (!$object instanceof State) {
            return null;
        }

        $array = [];

        if (null !== $activity = $object->getActivity()->getId()->getValue()) {
            $array['activityId'] = $this->normalizeAttribute($activity, $format, $context);
        }

        $agent = $object->getAgent();
        $array['agent'] = $this->normalizeAttribute($agent, $format, $context);

        if (null !== $stateId = $object->getStateId()) {
            $array['stateId'] = $this->normalizeAttribute($stateId, $format, $context);
        }

        if (null !== $registrationId = $object->getRegistrationId()) {
            $array['registrationId'] = $this->normalizeAttribute($registrationId, $format, $context);
        }

        if (null !== $data = $object->getData()) {
            $array['data'] = $this->normalizeAttribute($data, $format, $context);
        }

        return $array;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof State;
    }

    /**
     * @throws JsonException
     * @throws ExceptionInterface
     */
    public function denormalize($data, $type, $format = null, array $context = []): array|State
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
     * @param ?string $format
     * @throws JsonException|ExceptionInterface
     */
    public function denormarlizeState(?array $state, string $format = null): State
    {
        $activity = null;
        if (isset($state['activityId'])) {
            $activity = $this->denormalizeData(['id' => $state['activityId']], Activity::class, $format);
        }

        $agent = null;
        if (isset($state['agent'])) {
            $agent = $this->denormalizeData(json_decode((string)$state['agent'], true), Agent::class, $format);
        }

        $stateId = $state['stateId'] ?? null;

        $registrationId = $state['registration'] ?? null;

        if (!is_array($state['data']) && !is_null($decoded = json_decode((string)$state['data'], true))) {
            $data = $decoded;
        } else {
            $data = $state['data'];
        }

        return new State($activity, $agent, $stateId, $registrationId, $data);
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return State::class === $type;
    }
}
