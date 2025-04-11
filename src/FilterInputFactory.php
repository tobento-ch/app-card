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

namespace Tobento\App\Card;

use JsonException;
use Tobento\Service\Requester\RequesterInterface;
use Tobento\Service\Cookie\CookiesInterface;
use Tobento\Service\Cookie\CookieValuesInterface;
use Tobento\Service\Session\SessionInterface;

/**
 * FilterInputFactory
 */
final class FilterInputFactory implements FilterInputFactoryInterface
{
    /**
     * Create a new FilterInputFactory
     *
     * @param RequesterInterface $requester
     */
    public function __construct(
        private RequesterInterface $requester,
    ) {}

    /**
     * Returns the created filter input.
     *
     * @param array $config
     * @return FilterInputInterface
     */
    public function createFilterInput(array $config = []): FilterInputInterface
    {
        $storage = $config['storage'] ?? 'cookie';
        
        if ($storage === 'cookie') {
            return $this->createUsingCookieStorage(config: $config);
        }
        
        return $this->createUsingSessionStorage();
    }
    
    /**
     * Returns the created filter input using session storage.
     *
     * @return FilterInputInterface
     */
    private function createUsingSessionStorage(): FilterInputInterface
    {
        $session = $this->requester->request()->getAttribute(SessionInterface::class);
        $input = $this->requester->input();
        $resourceName = sha1($this->requester->request()->getUri()->getPath());
        
        if ($input->has('card')) {
            // we combine session card data with input data
            // so that indiviual filter forms can be sumbitted
            // without losing previously filtered values.
            $sessionData = $session->get('card.'.$resourceName, []);
            $data = array_replace_recursive($sessionData, $input->get('card', []));
            
            $session->set('card.'.$resourceName, $data);
        } else {
            $data = $session->get('card.'.$resourceName, []);
        }
        
        return new FilterInput(input: $data);
    }
    
    /**
     * Returns the created filter input using cookie storage.
     *
     * @param array $config
     * @return FilterInputInterface
     */
    private function createUsingCookieStorage(array $config = []): FilterInputInterface
    {
        $cookieValues = $this->requester->request()->getAttribute(CookieValuesInterface::class);
        $cookies = $this->requester->request()->getAttribute(CookiesInterface::class);
        $input = $this->requester->input();
        $resourceName = sha1($this->requester->request()->getUri()->getPath());
        
        try {
            $data = json_decode($cookieValues->get('card-'.$resourceName, '{}'), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $data = [];
        }
        
        if ($input->has('card')) {
            // we combine cookie card data with input data
            // so that indiviual filter forms can be sumbitted
            // without losing previously filtered values.
            $data = array_replace_recursive($data, $input->get('card', []));
            
            $cookies->add(
                name: 'card-'.$resourceName,
                value: json_encode($data),
                lifetime: $config['lifetime'] ?? null,
                sameSite: $config['sameSite'] ?? 'LAX',
            );
        }
        
        return new FilterInput(input: $data);
    }
}