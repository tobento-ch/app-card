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

use Stringable;
use Tobento\Service\Support\Renderable;
use Tobento\Service\Tag\Attributes;
use Tobento\Service\Tag\Tag;

/**
 * Links
 */
class Links implements Renderable
{
    /**
     * @var array<array-key, Link>
     */
    protected array $links = [];
    
    /**
     * @var array
     */
    protected array $attributes = [];
    
    /**
     * Create a new Links instance.
     *
     * @param Link ...$links
     */
    final public function __construct(
        Link ...$links,
    ) {
        $this->links = $links;
        $this->attributes = ['class' => 'buttons spaced'];
    }

    /**
     * Sets the attributes for the parent tag.
     *
     * @param array $attributes
     * @return static $this
     */
    public function attributes(array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
    }
    
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render(): string
    {
        if (empty($this->links)) {
            return '';
        }
        
        $html = '';
        
        foreach($this->links as $link) {
            $html .= $link->render();
        }

        $tag = new Tag(
            name: 'div',
            html: $html,
            attributes: new Attributes($this->attributes),
        );
        
        return (string)$tag;
    }
}