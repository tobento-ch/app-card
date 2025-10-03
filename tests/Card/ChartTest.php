<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\App\Card\Test\Card;

use Maantje\Charts\Chart;
use Maantje\Charts\Line\Line;
use Maantje\Charts\Line\Lines;
use Maantje\Charts\Line\Point;
use PHPUnit\Framework\TestCase;
use Tobento\App\Card\Card;
use Tobento\App\Card\CardInterface;
use Tobento\App\Card\Test\Factory;

class ChartTest extends TestCase
{
    protected function chart(): Chart
    {
        return new Chart(
            series: [
                new Lines(
                    lines: [
                        new Line(
                            points: [
                                [0, 0],
                                [100, 4],
                                [200, 12],
                                [300, 8],
                            ]
                        ),
                        new Line(
                            points: [
                                new Point(x: 0, y: 4, color: 'red', size: 5),
                                new Point(x: 100, y: 12, color: 'red', size: 5),
                                new Point(x: 200, y: 24, color: 'red', size: 5),
                                new Point(x: 300, y: 7, color: 'red', size: 5),
                            ],
                            color: 'blue'
                        ),
                    ]
                ),
            ],
        );
    }
    
    public function testThatImplementsCardInterfaces()
    {
        $card = new Card\Chart(
            view: Factory::createView(),
            chart: $this->chart(),
        );
        
        $this->assertInstanceof(CardInterface::class, $card);
    }
    
    public function testGetterMethods()
    {
        $chart = $this->chart();
        $card = new Card\Chart(
            view: Factory::createView(),
            chart: $chart,
            title: 'Title',
            group: 'foo',
            priority: 150,
        );

        $this->assertSame('Title', $card->title());
        $this->assertSame('foo', $card->group());
        $this->assertSame(150, $card->priority());
        $this->assertSame($chart, $card->chart());
    }
    
    public function testRenderMethod()
    {
        $card = new Card\Chart(
            view: Factory::createView(),
            chart: $this->chart(),
            title: 'Title',
        );
        
        $this->assertStringContainsString('Title', $card->render());
        $this->assertStringContainsString('<svg xmlns="http://www.w3.org/2000/svg"', $card->render());
    }
}