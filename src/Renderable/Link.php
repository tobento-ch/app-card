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

namespace Tobento\App\Card\Renderable;

use Tobento\Service\Tag\Attributes;
use Tobento\Service\Tag\Tag;
use Tobento\Service\Tag\TagInterface;
use Tobento\Service\Support\Renderable;
use Tobento\Service\Support\Str;
use Stringable;

/**
 * Link
 */
class Link implements Renderable
{
    /**
     * Create a new Link.
     *
     * @param string $url
     * @param string $label
     * @param array $attributes
     */
    final public function __construct(
        protected string $url,
        protected string $label,
        protected array $attributes = [],
    ) {}

    /**
     * Returns the link tag.
     *
     * @return TagInterface
     */
    public function tag(): TagInterface
    {
        $attributes = new Attributes($this->attributes);
        $attributes->set('href', $this->url);
        
        return new Tag(
            name: 'a',
            html: Str::esc($this->label),
            attributes: $attributes,
        );
    }
    
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render(): string
    {
        return (string)$this->tag();
    }
}