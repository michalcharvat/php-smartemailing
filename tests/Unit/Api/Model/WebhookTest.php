<?php
declare(strict_types=1);

namespace SmartEmailing\Test\Unit\Api\Model;

use PHPUnit\Framework\TestCase;
use SmartEmailing\Api\Model\Webhook;

class WebhookTest extends TestCase
{
    public function testToArrayWithoutBasicAuth(): void
    {
        $webhook = new Webhook('https://example.com/hook', Webhook::EVENT_UNSUBSCRIBE_CONTACT);
        $this->assertEquals([
            'target_url' => 'https://example.com/hook',
            'event' => 'unsubscribed_contact',
        ], $webhook->toArray());
    }

    public function testToArrayWithBasicAuth(): void
    {
        $webhook = (new Webhook('https://example.com/hook', Webhook::EVENT_UNSUBSCRIBE_CONTACT))
            ->setBasicAuth('login-user', 'secret-password');
        $this->assertEquals([
            'target_url' => 'https://example.com/hook',
            'event' => 'unsubscribed_contact',
            'basic_auth' => [
                'login' => 'login-user',
                'password' => 'secret-password',
            ],
        ], $webhook->toArray());
    }
}
