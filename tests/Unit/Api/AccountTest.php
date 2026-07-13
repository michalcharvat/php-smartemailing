<?php
declare(strict_types=1);

namespace SmartEmailing\Test\Unit\Api;

use GuzzleHttp\Psr7\Response;
use SmartEmailing\Api\Account;
use SmartEmailing\Test\TestCase;

class AccountTest extends TestCase
{
    public function testShouldGetInfo(): void
    {
        $expectedArray = '{
            "status": "ok",
            "meta": [],
            "data": {
                "account_id": 12345,
                "guid": "d3v3l0p3r-t35t-4cc0unt-gu1d",
                "username": "developer@smartemailing.cz",
                "firstname": "Developer",
                "surname": "Test",
                "company": "SmartEmailing",
                "phone": "+420777888999"
            }
        }';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('account-info')
            ->will($this->returnValue(
                new Response(200, [], $expectedArray))
            )
        ;

        /** @var Account $api */
        $response = $api->getInfo();
        $expectedObject = json_decode($expectedArray);
        $this->assertEquals(
            (array)$expectedObject->data,
            $response->getData()
        );
        $this->assertEquals(
            $expectedObject->status,
            $response->getStatus()
        );
        $this->assertTrue($response->isSuccess());
    }

    protected function getApiClass(): string
    {
        return Account::class;
    }
}
