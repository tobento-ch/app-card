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

namespace Tobento\App\Card\Test\Feature\App;

use Tobento\App\Card\CardInterface;
use Tobento\App\Card\FilterInputInterface;
use Tobento\Service\View\ViewInterface;

final class OrdersCard implements CardInterface
{
    public function __construct(
        private ViewInterface $view,
        private FilterInputInterface $input,
    ) {}

    public function group(): string
    {
        return '';
    }

    public function priority(): int
    {
        return 0;
    }
    
    public function render(): string
    {
        return implode(',', $this->getOrders());
    }
    
    public function statuses(): array
    {
        return ['paid' => 'Paid', 'unpaid' => 'Unpaid'];
    }
    
    public function activeStatus(): string
    {
        $value = $this->input->get('order.status', '');

        if (in_array($value, array_keys($this->statuses()))) {
            return $value;
        }
        
        return 'paid';
    }
    
    public function getOrders(): array
    {
        if ($this->activeStatus() === 'unpaid') {
            return ['unpaid:1', 'unpaid:2'];
        }

        return ['paid:1', 'paid:2'];
    }
}