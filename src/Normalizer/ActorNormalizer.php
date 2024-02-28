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
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Xabbuh\XApi\Model\Account;
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Agent;
use Xabbuh\XApi\Model\Group;
use Xabbuh\XApi\Model\InverseFunctionalIdentifier;
use Xabbuh\XApi\Model\IRI;

/**
 * Normalizes and denormalizes xAPI statement actors.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class ActorNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): ?array
    {
        if (!$object instanceof Actor) {
            return null;
        }

        $data = [];

        $this->normalizeInverseFunctionalIdentifier($data, $object->getInverseFunctionalIdentifier(), $format, $context);

        if (null !== $name = $object->getName()) {
            $data['name'] = $name;
        }

        if ($object instanceof Group) {
            $members = [];

            foreach ($object->getMembers() as $agent) {
                $members[] = $this->normalize($agent);
            }

            if ($members !== []) {
                $data['member'] = $members;
            }

            $data['objectType'] = 'Group';
        } else {
            $data['objectType'] = 'Agent';
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Actor;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $inverseFunctionalIdentifier = $this->denormalizeInverseFunctionalIdentifier($data, $format, $context);
        $name = $data['name'] ?? null;

        if (isset($data['objectType']) && 'Group' === $data['objectType']) {
            return $this->denormalizeGroup($name, $data, $inverseFunctionalIdentifier, $format, $context);
        }

        if (null === $inverseFunctionalIdentifier) {
            throw new InvalidArgumentException('Missing inverse functional identifier for agent.');
        }

        return new Agent($inverseFunctionalIdentifier, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Actor::class === $type || Agent::class === $type || Group::class === $type;
    }

    private function normalizeInverseFunctionalIdentifier(array &$data, InverseFunctionalIdentifier $inverseFunctionalIdentifier = null, ?string $format = null, array $context = []): void
    {
        if (!$inverseFunctionalIdentifier instanceof InverseFunctionalIdentifier) {
            return;
        }

        if (($mbox = $inverseFunctionalIdentifier->getMbox()) instanceof IRI) {
            $data['mbox'] = $mbox->getValue();
        }

        if (null !== $mboxSha1Sum = $inverseFunctionalIdentifier->getMboxSha1Sum()) {
            $data['mbox_sha1sum'] = $mboxSha1Sum;
        }

        if (null !== $openId = $inverseFunctionalIdentifier->getOpenId()) {
            $data['openid'] = $openId;
        }

        if (($account = $inverseFunctionalIdentifier->getAccount()) instanceof Account) {
            $data['account'] = $this->normalizeAttribute($account, $format, $context);
        }
    }

    private function denormalizeInverseFunctionalIdentifier(array $data, ?string $format = null, array $context = []): ?InverseFunctionalIdentifier
    {
        if (isset($data['mbox'])) {
            return InverseFunctionalIdentifier::withMbox(IRI::fromString($data['mbox']));
        }

        if (isset($data['mbox_sha1sum'])) {
            return InverseFunctionalIdentifier::withMboxSha1Sum($data['mbox_sha1sum']);
        }

        if (isset($data['openid'])) {
            return InverseFunctionalIdentifier::withOpenId($data['openid']);
        }

        if (isset($data['account'])) {
            return InverseFunctionalIdentifier::withAccount($this->denormalizeAccount($data, $format, $context));
        }

        return null;
    }

    private function denormalizeAccount(array $data, ?string $format = null, array $context = [])
    {
        if (!isset($data['account'])) {
            return null;
        }

        return $this->denormalizeData($data['account'], Account::class, $format, $context);
    }

    /**
     * @throws ExceptionInterface
     */
    private function denormalizeGroup($name, array $data, InverseFunctionalIdentifier $inverseFunctionalIdentifier = null, $format = null, array $context = []): Group
    {
        $members = [];

        if (isset($data['member'])) {
            foreach ($data['member'] as $member) {
                $members[] = $this->denormalize($member, Agent::class, $format, $context);
            }
        }

        return new Group($inverseFunctionalIdentifier, $name, $members);
    }
}
