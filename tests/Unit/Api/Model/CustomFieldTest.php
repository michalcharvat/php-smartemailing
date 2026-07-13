<?php
declare(strict_types=1);

namespace SmartEmailing\Test\Unit\Api\Model;

use PHPUnit\Framework\TestCase;
use SmartEmailing\Api\Model\CustomField;
use SmartEmailing\Exception\AllowedTypeException;

class CustomFieldTest extends TestCase
{
    public function testAcceptsBoolAndNumericTypes(): void
    {
        $this->assertSame(CustomField::BOOL, (new CustomField('Vip', CustomField::BOOL))->getType());
        $this->assertSame(CustomField::NUMERIC, (new CustomField('Score', CustomField::NUMERIC))->getType());
        $this->assertSame('bool', CustomField::BOOL);
        $this->assertSame('numeric', CustomField::NUMERIC);
    }

    public function testRejectsUnknownType(): void
    {
        $this->expectException(AllowedTypeException::class);
        new CustomField('Broken', 'blob');
    }
}
