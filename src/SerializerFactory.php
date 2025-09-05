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

use Symfony\Component\Serializer\SerializerInterface;
use Xabbuh\XApi\Serializer\ActivitySerializerInterface;
use Xabbuh\XApi\Serializer\ActorSerializerInterface;
use Xabbuh\XApi\Serializer\DocumentDataSerializerInterface;
use Xabbuh\XApi\Serializer\PersonSerializerInterface;
use Xabbuh\XApi\Serializer\SerializerFactoryInterface;
use Xabbuh\XApi\Serializer\StateDocumentSerializerInterface;
use Xabbuh\XApi\Serializer\StatementResultSerializerInterface;
use Xabbuh\XApi\Serializer\StatementSerializerInterface;

/**
 * Creates serializer instances that use the Symfony Serializer component.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final readonly class SerializerFactory implements SerializerFactoryInterface
{
    private SerializerInterface|\Symfony\Component\Serializer\Serializer $serializer;

    public function __construct(?SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer instanceof SerializerInterface ? $serializer : Serializer::createSerializer();
    }

    /**
     * {@inheritdoc}
     */
    public function createActivitySerializer(): ActivitySerializerInterface
    {
        return new ActivitySerializer($this->serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function createActorSerializer(): ActorSerializerInterface
    {
        return new ActorSerializer($this->serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function createDocumentDataSerializer(): DocumentDataSerializerInterface
    {
        return new DocumentDataSerializer($this->serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function createStatementSerializer(): StatementSerializerInterface
    {
        return new StatementSerializer($this->serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function createStatementResultSerializer(): StatementResultSerializerInterface
    {
        return new StatementResultSerializer($this->serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function createPersonSerializer(): PersonSerializerInterface
    {
        return new PersonSerializer($this->serializer);
    }

    public function createStateSerializer(): StateSerializer
    {
        return new StateSerializer($this->serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function createStateDocumentSerializer(): StateDocumentSerializerInterface
    {
        return new StateDocumentSerializer($this->serializer);
    }
}
