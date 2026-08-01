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
    
    public function testRenderValueWithArray()
    {
        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [[['a' => 'b']]],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        // JSON pretty print is escaped
        $this->assertStringContainsString('&quot;a&quot;:&quot;b&quot;', $html);
    }
    
    public function testRenderValueWithCardInterface()
    {
        $innerCard = new Card\Html(
            view: Factory::createView(),
            html: '<span>Inner</span>',
        );

        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [[$innerCard]],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        // Html card content must appear inside the table cell
        $this->assertStringContainsString('<span>Inner</span>', $html);
    }
    
    public function testRenderValueWithRenderable()
    {
        $renderable = new class implements \Tobento\Service\Support\Renderable {
            public function render(): string
            {
                return '<b>R</b>';
            }
        };

        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [[$renderable]],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        $this->assertStringContainsString('<b>R</b>', $html);
    }
    
    public function testRenderValueWithHtmlable()
    {
        $htmlable = new class implements \Tobento\Service\Support\Htmlable {
            public function toHtml(): string
            {
                return '<strong>Bar</strong>';
            }
        };

        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [[$htmlable]],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        // Raw HTML, not escaped
        $this->assertStringContainsString('<strong>Bar</strong>', $html);
    }
    
    public function testRenderValueWithStringable()
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return '<x>';
            }
        };

        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [[$stringable]],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        // Escaped
        $this->assertStringContainsString('&lt;x&gt;', $html);
    }
    
    public function testRenderValueWithNumeric()
    {
        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [[123]],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        $this->assertStringContainsString('123', $html);
    }
    
    public function testRenderValueWithBool()
    {
        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [[true]],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        $this->assertStringContainsString('1', $html);
    }
    
    public function testRenderValueWithNull()
    {
        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [[null]],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        $this->assertStringContainsString('null', $html);
    }
    
    public function testRenderValueWithUnsupportedType()
    {
        $card = new Card\Table(
            view: Factory::createView(),
            headers: ['foo'],
            rows: [[new \stdClass()]],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        // Unsupported → empty string → empty <td></td>
        $this->assertStringContainsString('<td></td>', $html);
    }
}