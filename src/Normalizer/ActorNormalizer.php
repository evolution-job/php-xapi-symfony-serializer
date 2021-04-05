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

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Xabbuh\XApi\Common\Exception\XApiException;
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
    public function normalize($object, $format = null, array $context = array())
    {
        if (!$object instanceof Actor) {
            return null;
        }

        $data = array();

        $this->normalizeInverseFunctionalIdentifier($object->getInverseFunctionalIdentifier(), $data, $format, $context);

        if (null !== $name = $object->getName()) {
            $data['name'] = $name;
        }

        if ($object instanceof Group) {
            $members = array();

            foreach ($object->getMembers() as $member) {
                $members[] = $this->normalize($member);
            }

            if (count($members) > 0) {
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
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Actor;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $inverseFunctionalIdentifier = $this->denormalizeInverseFunctionalIdentifier($data, $format, $context);
        $name = isset($data['name']) ? $data['name'] : null;

        if (array_key_exists('name', $data)) {
            if (null === $name) {
                throw new InvalidArgumentException('Missing name for actor.');
            }

            if (!is_string($name)) {
                throw new UnexpectedValueException('Actor name is not a string.');
            }
        }

        if (isset($data['objectType'])) {
            if (!in_array($data['objectType'], ['Agent', 'Group'])) {
                throw new UnexpectedValueException('The actor of statement is not an Agent or Group.');
            }

            if ('Group' === $data['objectType']) {
                return $this->denormalizeGroup($inverseFunctionalIdentifier, $name, $data, $format, $context);
            }
        }

        if (null === $inverseFunctionalIdentifier) {
            throw new InvalidArgumentException('Missing inverse functional identifier for agent.');
        }

        return new Agent($inverseFunctionalIdentifier, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return 'Xabbuh\XApi\Model\Actor' === $type || 'Xabbuh\XApi\Model\Agent' === $type || 'Xabbuh\XApi\Model\Group' === $type;
    }

    private function normalizeInverseFunctionalIdentifier(InverseFunctionalIdentifier $iri = null, &$data, $format = null, array $context = array())
    {
        if (null === $iri) {
            return;
        }

        if (null !== $mbox = $iri->getMbox()) {
            $data['mbox'] = $mbox->getValue();
        }

        if (null !== $mboxSha1Sum = $iri->getMboxSha1Sum()) {
            $data['mbox_sha1sum'] = $mboxSha1Sum;
        }

        if (null !== $openId = $iri->getOpenId()) {
            $data['openid'] = $openId;
        }

        if (null !== $account = $iri->getAccount()) {
            $data['account'] = $this->normalizeAttribute($account, $format, $context);
        }
    }

    private function denormalizeInverseFunctionalIdentifier($data, $format = null, array $context = array())
    {
        $mbox = isset($data['mbox']) ? 1 : 0;
        $mbox_sha1sum = isset($data['mbox_sha1sum']) ? 1 : 0;
        $openid = isset($data['openid']) ? 1 : 0;
        $account = isset($data['account']) ? 1 : 0;

        if ($mbox + $mbox_sha1sum + $openid + $account > 1) {
            throw new XApiException('An Agent must not include more than one Inverse Functional Identifier');
        }

        if (isset($data['mbox'])) {
            if (!$this->isMBoxValid($data['mbox'])) {
                throw new \UnexpectedValueException('Actor mbox has not the form "mailto:email"');
            }

            return InverseFunctionalIdentifier::withMbox(IRI::fromString($data['mbox']));
        }

        if (isset($data['mbox_sha1sum'])) {
            if (!$this->isMBoxSha1SumValid($data['mbox_sha1sum'])) {
                throw new \UnexpectedValueException('Actor mbox_sha1sum is not a string');
            }

            return InverseFunctionalIdentifier::withMboxSha1Sum($data['mbox_sha1sum']);
        }

        if (isset($data['openid'])) {
            if (!$this->isOpenIdValid($data['openid'])) {
                throw new \UnexpectedValueException('Actor openid is not a URI.');
            }

            return InverseFunctionalIdentifier::withOpenId($data['openid']);
        }

        if (isset($data['account'])) {
            return InverseFunctionalIdentifier::withAccount($this->denormalizeAccount($data, $format, $context));
        }
    }

    private function denormalizeAccount($data, $format = null, array $context = array())
    {
        if (empty($data['account']) || empty($data['account']['homePage'])) {
            throw new \UnexpectedValueException('Actor account is not valid.');
        }

        if (!$this->isAccountHomePageValid($data['account']['homePage'])) {
            throw new \UnexpectedValueException('Account homepage is not an IRL.');
        }

        return $this->denormalizeData($data['account'], 'Xabbuh\XApi\Model\Account', $format, $context);
    }

    private function denormalizeGroup(InverseFunctionalIdentifier $iri = null, $name, $data, $format = null, array $context = array())
    {
        if (null === $iri
            && (!isset($data['member']) || !is_array($data['member']))
        ) {
            throw new XApiException('The group does not use the "member" property or it is not valid value.');
        }

        $members = array();

        if (isset($data['member'])) {
            foreach ($data['member'] as $member) {
                $members[] = $this->denormalize($member, 'Xabbuh\XApi\Model\Agent', $format, $context);
            }
        }

        return new Group($iri, $name, $members);
    }

    private function isMBoxValid($mbox): bool
    {
        $parts = explode(':', $mbox);
        $parts = array_filter($parts);

        if (2 !== count($parts)) {
            return false;
        }

        list($mailto, $mail) = $parts;

        if ('mailto' !== $mailto) {
            return false;
        }

        if (false === filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    private function isMBoxSha1SumValid($mBoxSha1sum): bool
    {
        if (!is_string($mBoxSha1sum)) {
            return false;
        }

        return true;
    }

    private function isOpenIdValid($openId): bool
    {
        if (false === filter_var($openId, FILTER_VALIDATE_URL)) {
            return false;
        }

        return true;
    }

    private function isAccountHomePageValid($homePage): bool
    {
        return $this->isOpenIdValid($homePage);
    }
}
