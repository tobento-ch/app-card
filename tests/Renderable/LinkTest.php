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
use Tobento\Service\Support\Renderable;

class LinkTest extends TestCase
{
    public function testThatImplementsRenderable()
    {
        $this->assertInstanceof(Renderable::class, new Link(url: 'url', label: 'Label'));
    }
    
    public function testRenderMethod()
    {
        $this->assertSame(
            '<a href="url">Label</a>',
            (new Link(url: 'url', label: 'Label'))->render()
        );
    }
    
    public function testRenderMethodLabelIsEscaped()
    {
        $this->assertSame(
            '<a href="url">&lt;p&gt;Label&lt;/p&gt;</a>',
            (new Link(url: 'url', label: '<p>Label</p>'))->render()
        );
    }
    
    public function testRenderMethodWithAttributes()
    {
        $this->assertSame(
            '<a class="button" href="url">Label</a>',
            (new Link(url: 'url', label: 'Label', attributes: ['class' => 'button']))->render()
        );
    }
}