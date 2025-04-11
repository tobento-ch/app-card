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

class NullCardTest extends TestCase
{
    public function testThatImplementsCardInterfaces()
    {
        $card = new Card\NullCard();
        
        $this->assertInstanceof(CardInterface::class, $card);
    }
    
    public function testMethods()
    {
        $card = new Card\NullCard();
        $this->assertSame('', $card->group());
        $this->assertSame(0, $card->priority());
        $this->assertSame('', $card->render());
    }
}