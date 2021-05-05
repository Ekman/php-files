<?php

namespace Nekman\EsPagination\CursorFactories;

use Elasticsearch\Client;
use Nekman\EsPagination\functions;
use PHPUnit\Framework\TestCase;

class EsScrollCursorFactoryTest extends TestCase
{
    public function testResponses()
    {
        $size = 3;
        $scroll = "5m";
        $scrollId = "foo,bar";

        $client = $this->createMock(Client::class);

        $client->expects($this->once())
            ->method("search")
            ->with(
                $this->equalTo(["size" => $size, "scroll" => $scroll]),
            )
            ->willReturn(
                functions::response(
                    [
                        functions::hit(1),
                        functions::hit(2),
                        functions::hit(3),
                    ],
                    $scrollId
                ),
            );

        $client->expects($this->exactly(2))
            ->method("scroll")
            ->withConsecutive(
                [$this->equalTo(["scroll" => $scroll, "body" => ["scroll_id" => $scrollId]])],
                [$this->equalTo(["scroll" => $scroll, "body" => ["scroll_id" => $scrollId]])],
            )
            ->willReturn(
                functions::response([
                    functions::hit(4),
                    functions::hit(5),
                    functions::hit(6),
                ]),
                functions::response()
            );

        $client->expects($this->once())
            ->method("clearScroll")
            ->with(
                $this->equalTo(["body" => ["scroll_id" => $scrollId]]),
            );

        $cursorFactory = new EsScrollCursorFactory($client, 3, "5m");

        $hits = functions::hitsId($cursorFactory->hits());
        $hits = iterator_to_array($hits);

        $this->assertEquals([1, 2, 3, 4, 5, 6], $hits);
    }
}
