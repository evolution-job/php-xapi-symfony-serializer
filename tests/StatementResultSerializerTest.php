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

use Xabbuh\XApi\Serializer\Symfony\Serializer;
use Xabbuh\XApi\Serializer\Symfony\StatementResultSerializer;
use Xabbuh\XApi\Serializer\Tests\StatementResultSerializerTestCase;

class StatementResultSerializerTest extends StatementResultSerializerTestCase
{
    protected function createStatementResultSerializer(): StatementResultSerializer
    {
        return new StatementResultSerializer(Serializer::createSerializer());
    }
}
