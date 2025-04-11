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

namespace Tobento\App\Card\Test;

use PHPUnit\Framework\TestCase;
use Tobento\App\Card\Card;
use Tobento\App\Card\CardInterface;
use Tobento\App\Card\Cards;
use Tobento\App\Card\CardsInterface;
use Tobento\App\Card\Factory;
use Tobento\App\Card\Test\Factory as ObjFactory;

class CardsTest extends TestCase
{
    public function testThatImplementsCardsInterfaces()
    {
        $cards = new Cards(container: ObjFactory::createContainer());
        
        $this->assertInstanceof(CardsInterface::class, $cards);
    }
    
    public function testAddMethodWithCard()
    {
        $cards = new Cards(container: ObjFactory::createContainer());
        
        $this->assertFalse($cards->has('foo'));
        $this->assertSame([], $cards->names());
        
        $card = new Card\NullCard();
        
        $cards->add('foo', $card);
        
        $this->assertTrue($cards->has('foo'));
        $this->assertSame($card, $cards->get('foo'));
        $this->assertSame(['foo'], $cards->names());
    }
    
    public function testAddMethodWithCardFactory()
    {
        $cards = new Cards(container: ObjFactory::createContainer());
        
        $this->assertFalse($cards->has('foo'));
        $this->assertSame([], $cards->names());
        
        $card = new Factory\Table(rows: ['foo', 'bar']);
        
        $cards->add('foo', $card);
        
        $this->assertTrue($cards->has('foo'));
        $this->assertInstanceof(CardInterface::class, $cards->get('foo'));
        $this->assertSame(['foo'], $cards->names());
    }
    
    public function testAddMethodWithClassString()
    {
        $cards = new Cards(container: ObjFactory::createContainer());
        
        $this->assertFalse($cards->has('foo'));
        $this->assertSame([], $cards->names());
        
        $cards->add('foo', Card\NullCard::class);
        
        $this->assertTrue($cards->has('foo'));
        $this->assertInstanceof(CardInterface::class, $cards->get('foo'));
        $this->assertSame(['foo'], $cards->names());
    }
    
    public function testAddMethodWithCallable()
    {
        $cards = new Cards(container: ObjFactory::createContainer());
        
        $this->assertFalse($cards->has('foo'));
        $this->assertSame([], $cards->names());
        
        $cards->add('foo', static function (string $name): CardInterface {
            return new Card\NullCard();
        });
        
        $this->assertTrue($cards->has('foo'));
        $this->assertInstanceof(CardInterface::class, $cards->get('foo'));
        $this->assertSame(['foo'], $cards->names());
    }
    
    public function testConstructCards()
    {
        $cards = new Cards(container: ObjFactory::createContainer(), cards: [
            'table' => new Factory\Table(rows: ['foo', 'bar']),
            'null' => new Card\NullCard(),
            'string' => Card\NullCard::class,
            'foo' => static function (string $name): CardInterface {
                return new Card\NullCard();
            },
        ]);
        
        $this->assertTrue($cards->has('table'));
        $this->assertTrue($cards->has('null'));
        $this->assertTrue($cards->has('string'));
        $this->assertTrue($cards->has('foo'));
        $this->assertFalse($cards->has('bar'));
        $this->assertSame(['table', 'null', 'string', 'foo'], $cards->names());
        $this->assertInstanceof(CardInterface::class, $cards->get('table'));
        $this->assertInstanceof(CardInterface::class, $cards->get('null'));
        $this->assertInstanceof(CardInterface::class, $cards->get('string'));
        $this->assertInstanceof(CardInterface::class, $cards->get('foo'));
    }
    
    public function testFilterMethod()
    {
        $cards = new Cards(container: ObjFactory::createContainer(), cards: [
            'foo' => new Factory\Table(rows: ['foo'], priority: 1),
            'bar' => new Factory\Table(rows: ['foo'], priority: 2),
            'baz' => new Factory\Table(rows: ['foo'], priority: 3),
        ]);
        
        $cardsNew = $cards->filter(
            fn(CardInterface $c): bool => $c->priority() > 1
        );
        
        $this->assertFalse($cards === $cardsNew);
        $this->assertSame(2, count($cardsNew->all()));
    }
    
    public function testGroupMethod()
    {
        $cards = new Cards(container: ObjFactory::createContainer(), cards: [
            'foo' => new Factory\Table(rows: ['foo'], group: 'a'),
            'bar' => new Factory\Table(rows: ['foo'], group: 'b'),
            'baz' => new Factory\Table(rows: ['foo'], group: 'a'),
        ]);
        
        $cardsNew = $cards->group('a');
        
        $this->assertFalse($cards === $cardsNew);
        $this->assertSame(2, count($cardsNew->all()));
    }
    
    public function testOnlyMethod()
    {
        $cards = new Cards(container: ObjFactory::createContainer(), cards: [
            'foo' => new Factory\Table(rows: ['foo']),
            'bar' => new Factory\Table(rows: ['foo']),
            'baz' => new Factory\Table(rows: ['foo']),
        ]);
        
        $cardsNew = $cards->only('foo', 'baz');
        
        $this->assertFalse($cards === $cardsNew);
        $this->assertSame(2, count($cardsNew->all()));
    }
    
    public function testExceptMethod()
    {
        $cards = new Cards(container: ObjFactory::createContainer(), cards: [
            'foo' => new Factory\Table(rows: ['foo']),
            'bar' => new Factory\Table(rows: ['foo']),
            'baz' => new Factory\Table(rows: ['foo']),
        ]);
        
        $cardsNew = $cards->except('foo', 'baz');
        
        $this->assertFalse($cards === $cardsNew);
        $this->assertSame(1, count($cardsNew->all()));
    }
    
    public function testCountMethod()
    {
        $cards = new Cards(container: ObjFactory::createContainer(), cards: [
            'foo' => new Factory\Table(rows: ['foo']),
            'bar' => new Factory\Table(rows: ['foo']),
        ]);
        
        $this->assertSame(2, $cards->count());
    }
    
    public function testGetIteratorMethod()
    {
        $cards = new Cards(container: ObjFactory::createContainer(), cards: [
            'foo' => new Factory\Table(rows: ['foo'], group: 'a'),
            'bar' => new Factory\Table(rows: ['foo'], group: 'b'),
            'baz' => new Factory\Table(rows: ['foo'], group: 'c'),
        ]);
        
        $iterated = [];
        
        foreach($cards as $card) {
            $iterated[] = $card->group();
        }
        
        $this->assertSame(['a', 'b', 'c'], $iterated);
    }
    
    public function testAllMethodSortsByPriority()
    {
        $cards = new Cards(container: ObjFactory::createContainer(), cards: [
            'foo' => new Factory\Table(rows: ['foo'], priority: 2),
            'bar' => new Factory\Table(rows: ['foo'], priority: 1),
            'baz' => new Factory\Table(rows: ['foo'], priority: 3),
        ]);
        
        $this->assertSame(['baz', 'foo', 'bar'], array_keys($cards->all()));
    }
}