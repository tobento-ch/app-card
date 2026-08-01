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

class KeyedListTest extends TestCase
{
    public function testThatImplementsCardInterfaces()
    {
        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: [],
        );
        
        $this->assertInstanceof(CardInterface::class, $card);
    }
    
    public function testGetterMethods()
    {
        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => 'Foo', 'bar' => 'Bar'],
            title: 'Title',
            group: 'foo',
            priority: 150,
        );

        $this->assertSame('Title', $card->title());
        $this->assertSame('foo', $card->group());
        $this->assertSame(150, $card->priority());
        $this->assertSame(['foo' => 'Foo', 'bar' => 'Bar'], $card->items());
    }
    
    public function testRenderMethod()
    {
        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => 'Foo', 'bar' => 'Bar'],
            title: 'Title',
        );
        
        $html = $card->render();
        $this->assertStringContainsString('Title', $html);
        $this->assertStringContainsString('foo', $html);
        $this->assertStringContainsString('Foo', $html);
        $this->assertStringContainsString('bar', $html);
        $this->assertStringContainsString('Bar', $html);
    }
    
    public function testRenderMethodWithEmptyAndZeroValue()
    {
        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['zero' => 0, 'empty' => ''],
            title: 'Title',
        );
        
        $html = $card->render();
        $this->assertStringContainsString('zero', $html);
        $this->assertStringContainsString('empty', $html);
        $this->assertStringContainsString('0', $html);
        $this->assertStringContainsString('-', $html);
    }

    public function testRenderValueWithArray()
    {
        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => ['a' => 'b']],
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

        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => $innerCard],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        // Html card content must appear
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

        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => $renderable],
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

        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => $htmlable],
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

        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => $stringable],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        // Escaped
        $this->assertStringContainsString('&lt;x&gt;', $html);
    }
    
    public function testRenderValueWithNumeric()
    {
        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => 123],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        $this->assertStringContainsString('123', $html);
    }
    
    public function testRenderValueWithBool()
    {
        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => true],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        // true → "1"
        $this->assertStringContainsString('1', $html);
    }
    
    public function testRenderValueWithNull()
    {
        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => null],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        $this->assertStringContainsString('null', $html);
    }
    
    public function testRenderValueWithUnsupportedType()
    {
        $card = new Card\KeyedList(
            view: Factory::createView(),
            items: ['foo' => new \stdClass()],
        );

        $html = preg_replace('/\s+/', '', $card->render());

        // Unsupported → empty string → KeyedList view renders "-" for empty values
        $this->assertStringContainsString('<divclass="text-700mt-xxsmb-s">-</div>', $html);
    }
}