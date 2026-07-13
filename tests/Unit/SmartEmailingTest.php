<?php
declare(strict_types=1);

namespace SmartEmailing\Test\Unit;

use PHPUnit\Framework\TestCase;
use SmartEmailing\SmartEmailing;

class SmartEmailingTest extends TestCase
{
    public function testDefaultRequestTimeoutIsSet(): void
    {
        $api = new SmartEmailing('username', 'api-key');
        $this->assertSame(30.0, $api->getClient()->getConfig('timeout'));
    }

    public function testCustomRequestTimeout(): void
    {
        $api = new SmartEmailing('username', 'api-key', null, 5.5);
        $this->assertSame(5.5, $api->getClient()->getConfig('timeout'));
    }
}
