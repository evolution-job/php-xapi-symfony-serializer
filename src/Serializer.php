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

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\AccountNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ActorNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\AttachmentNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ContextActivitiesNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ContextNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\DefinitionNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\DocumentDataNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ExtensionsNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\FilterNullValueNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\InteractionComponentNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\LanguageMapNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ObjectNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\ResultNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\StatementNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\StatementResultNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\StateNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\TimestampNormalizer;
use Xabbuh\XApi\Serializer\Symfony\Normalizer\VerbNormalizer;

/**
 * Entry point to set up the {@link SymfonySerializer Symfony Serializer component}
 * for the Experience API.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 * @see \Xabbuh\XApi\Serializer\Symfony\Tests\SerializerTest
 */
class Serializer
{
    /**
     * Creates a new Serializer.
     *
     * @return SymfonySerializer|SerializerInterface The Serializer
     */
    public static function createSerializer(): SymfonySerializer|SerializerInterface
    {
        $normalizers = [new AccountNormalizer(), new ActorNormalizer(), new AttachmentNormalizer(), new ContextNormalizer(), new ContextActivitiesNormalizer(), new DefinitionNormalizer(), new DocumentDataNormalizer(), new ExtensionsNormalizer(), new InteractionComponentNormalizer(), new LanguageMapNormalizer(), new ObjectNormalizer(), new ResultNormalizer(), new StateNormalizer(), new StatementNormalizer(), new StatementResultNormalizer(), new TimestampNormalizer(), new VerbNormalizer(), new ArrayDenormalizer(), new FilterNullValueNormalizer(), new PropertyNormalizer()];
        $encoders = [new JsonEncoder(),];

        return new SymfonySerializer($normalizers, $encoders);
    }
}
