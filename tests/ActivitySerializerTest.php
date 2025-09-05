<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Serializer\Symfony\Tests;

use Xabbuh\XApi\Serializer\Symfony\ActivitySerializer;
use Xabbuh\XApi\Serializer\Symfony\Serializer;
use Xabbuh\XApi\Serializer\Tests\ActivitySerializerTestCase;

class ActivitySerializerTest extends ActivitySerializerTestCase
{
    protected function createActivitySerializer(): ActivitySerializer
    {
        return new ActivitySerializer(Serializer::createSerializer());
    }
}