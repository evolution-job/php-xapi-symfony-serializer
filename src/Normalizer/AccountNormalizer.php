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

use Xabbuh\XApi\Model\Account;
use Xabbuh\XApi\Model\IRL;

/**
 * Normalizes and denormalizes xAPI statement accounts.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class AccountNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): ?array
    {
        if (!$data instanceof Account) {
            return null;
        }

        return ['name' => $data->getName(), 'homePage' => $data->getHomePage()->getValue()];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Account;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): Account
    {
        $name = '';
        $homePage = '';

        if (isset($data['name'])) {
            $name = $data['name'];
        }

        if (isset($data['homePage'])) {
            $homePage = $data['homePage'];
        }

        return new Account($name, IRL::fromString($homePage));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        return Account::class === $type;
    }
}
