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

namespace Tobento\App\Card\Test\Renderable;

use PHPUnit\Framework\TestCase;
use Tobento\App\Card\Renderable\Link;
use Tobento\App\Card\Renderable\Links;
use Tobento\Service\Support\Renderable;

class LinksTest extends TestCase
{
    public function testThatImplementsRenderable()
    {
        $this->assertInstanceof(Renderable::class, new Links());
    }
    
    public function testRenderMethod()
    {
        $links = new Links(
            new Link(url: 'url', label: 'Label'),
            new Link(url: 'url1', label: 'Label1')
        );
        
        $this->assertSame(
            '<div class="buttons spaced"><a href="url">Label</a><a href="url1">Label1</a></div>',
            $links->render()
        );
    }
    
    public function testRenderMethodWithoutLinks()
    {
        $this->assertSame('', (new Links())->render());
    }
    
    public function testRenderMethodWithAttributes()
    {
        $links = (new Links(
            new Link(url: 'url', label: 'Label'),
        ))->attributes(['class' => 'custom']);
        
        $this->assertSame(
            '<div class="custom"><a href="url">Label</a></div>',
            $links->render()
        );
    }
}