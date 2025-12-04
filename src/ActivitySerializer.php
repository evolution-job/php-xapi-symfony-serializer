<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Serializer\Symfony;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Serializer\ActivitySerializerInterface;
use Xabbuh\XApi\Serializer\Exception\ActivitySerializationException;
use Xabbuh\XApi\Serializer\Exception\SerializationException;

/**
 * Serializes {@link Activity activities} using the Symfony Serializer component.
 *
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
final  readonly class ActivitySerializer implements ActivitySerializerInterface
{
    public function __construct(private SerializerInterface $serializer) { }

    /**
     * {@inheritdoc}
     * @throws ExceptionInterface
     */
    public function serializeActivity(Activity $activity): string
    {
        try {
            return $this->serializer->serialize($activity, 'json');
        } catch (SerializationException $serializationException) {
            throw new ActivitySerializationException($serializationException->getMessage(), 0, $serializationException);
        }
    }
}
