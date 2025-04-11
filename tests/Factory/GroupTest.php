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

namespace Tobento\App\Card\Test\Factory;

use PHPUnit\Framework\TestCase;
use Tobento\App\Card\Card;
use Tobento\App\Card\Factory;
use Tobento\App\Card\CardFactoryInterface;
use Tobento\App\Card\Test\Factory as ObjFactory;

class GroupTest extends TestCase
{
    public function testThatImplementsCardFactoryInterfaces()
    {
        $factory = new Factory\Group(
            cards: [],
        );
        
        $this->assertInstanceof(CardFactoryInterface::class, $factory);
    }
    
    public function testCreateCard()
    {
        $factory = new Factory\Group(
            cards: [new Factory\Html(html: 'lorem ipsum')],
            title: 'Title',
            group: 'foo',
            priority: 150,
        );
        
        $card = $factory->createCard(name: '', container: ObjFactory::createContainer());
        
        $this->assertInstanceof(Card\Group::class, $card);
        $this->assertSame('Title', $card->title());
        $this->assertSame('foo', $card->group());
        $this->assertSame(150, $card->priority());
        $this->assertStringContainsString('lorem ipsum', $card->render());
    }
}