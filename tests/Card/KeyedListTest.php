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
}