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
use Tobento\App\Card\Factory\Html;
use Tobento\App\Card\Test\Factory;

class GroupTest extends TestCase
{
    public function testThatImplementsCardInterfaces()
    {
        $card = new Card\Group(
            container: Factory::createContainer(),
            view: Factory::createView(),
            cards: [],
        );
        
        $this->assertInstanceof(CardInterface::class, $card);
    }
    
    public function testGetterMethods()
    {
        $card = new Card\Group(
            container: Factory::createContainer(),
            view: Factory::createView(),
            cards: [],
            title: 'Title',
            group: 'foo',
            priority: 150,
        );
        
        $this->assertSame('Title', $card->title());
        $this->assertSame('foo', $card->group());
        $this->assertSame(150, $card->priority());
        $this->assertSame([], $card->cards());
    }
    
    public function testRenderMethod()
    {
        $card = new Card\Group(
            container: Factory::createContainer(),
            view: Factory::createView(),
            cards: [],
        );
        
        $this->assertSame('', $card->render());
        
        $card = new Card\Group(
            container: Factory::createContainer(),
            view: Factory::createView(),
            cards: [
                new Html(html: 'lorem ipsum'),
            ],
            title: 'Title',
        );
        
        $this->assertStringContainsString('Title', $card->render());
        $this->assertStringContainsString('lorem ipsum', $card->render());
    }
}