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

class HtmlTest extends TestCase
{
    public function testThatImplementsCardInterfaces()
    {
        $card = new Card\Html(
            view: Factory::createView(),
            html: '',
        );
        
        $this->assertInstanceof(CardInterface::class, $card);
    }
    
    public function testGetterMethods()
    {
        $card = new Card\Html(
            view: Factory::createView(),
            html: '<p>lorem ipsum</p>',
            title: 'Title',
            group: 'foo',
            priority: 150,
        );

        $this->assertSame('Title', $card->title());
        $this->assertSame('foo', $card->group());
        $this->assertSame(150, $card->priority());
        $this->assertSame('<p>lorem ipsum</p>', $card->html());
    }
    
    public function testRenderMethod()
    {
        $card = new Card\Html(
            view: Factory::createView(),
            html: '<p>lorem ipsum</p>',
            title: 'Title',
        );
        
        $this->assertStringContainsString('Title', $card->render());
        $this->assertStringContainsString('<p>lorem ipsum</p>', $card->render());
    }
}