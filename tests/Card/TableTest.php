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

use PHPUnit\Framework\TestCase;
use Tobento\App\Card\Card;
use Tobento\App\Card\CardInterface;
use Tobento\App\Card\Test\Factory;

class TableTest extends TestCase
{
    public function testThatImplementsCardInterfaces()
    {
        $card = new Card\Table(
            view: Factory::createView(),
        );
        
        $this->assertInstanceof(CardInterface::class, $card);
    }
    
    public function testGetterMethods()
    {
        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [['Foo']],
            title: 'Title',
            group: 'foo',
            priority: 150,
        );

        $this->assertSame('Title', $card->title());
        $this->assertSame('foo', $card->group());
        $this->assertSame(150, $card->priority());
        $this->assertSame(['foo'], $card->headers());
        $this->assertSame([['Foo']], $card->rows());
    }
    
    public function testRenderMethod()
    {
        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [['Foo']],
            title: 'Title',
        );
        
        $this->assertStringContainsString('Title', $card->render());
        $this->assertStringContainsString(
            '<table><tr><th>foo</th></tr><tr><td>Foo</td></tr></table>',
            preg_replace('/\s+/', '', $card->render())
        );
    }
    
    public function testRenderMethodWithHeadersOnly()
    {
        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo', 'bar'],
        );
        
        $this->assertStringContainsString(
            '<table><tr><th>foo</th><th>bar</th></tr></table>',
            preg_replace('/\s+/', '', $card->render())
        );
    }
    
    public function testRenderMethodWithRowsOnly()
    {
        $card = new Card\Table(
            view: Factory::createView(),
            rows: [['Foo', 'Bar'], ['Baz', 'Lor']],
        );
        
        $this->assertStringContainsString(
            '<table><tr><td>Foo</td><td>Bar</td></tr><tr><td>Baz</td><td>Lor</td></tr></table>',
            preg_replace('/\s+/', '', $card->render())
        );
    }
}