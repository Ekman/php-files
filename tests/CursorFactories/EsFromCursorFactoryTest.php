<?php

namespace Nekman\EsPagination\CursorFactories;

use Elasticsearch\Client;
use Nekman\EsPagination\functions;
use PHPUnit\Framework\TestCase;

class EsFromCursorFactoryTest extends TestCase
{
    public function testResponses()
    {
        $size = 3;

        $client = $this->createMock(Client::class);

        $client->expects($this->exactly(3))
            ->method("search")
            ->withConsecutive(
                [$this->equalTo(["size" => $size])],
                [$this->equalTo(["size" => $size, "from" => $size])],
                [$this->equalTo(["size" => $size, "from" => $size * 2])],
            )
            ->willReturn(
                functions::response([
                    functions::hit(1),
                    functions::hit(2),
                    functions::hit(3),
                ]),
                functions::response([
                    functions::hit(4),
                    functions::hit(5),
                    functions::hit(6),
                ]),
                functions::response()
            );

        $cursorFactory = new EsFromCursorFactory($client, $size);

        $hits = functions::hitsId($cursorFactory->hits());
        $hits = iterator_to_array($hits);

        $this->assertEquals([1, 2, 3, 4, 5, 6], $hits);
    }
}
