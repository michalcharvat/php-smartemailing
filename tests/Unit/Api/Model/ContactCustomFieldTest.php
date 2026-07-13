<?php
declare(strict_types=1);

namespace SmartEmailing\Test\Unit\Api\Model;

use PHPUnit\Framework\TestCase;
use SmartEmailing\Api\Model\Contact\CustomField;

class ContactCustomFieldTest extends TestCase
{
    public function testZeroValueIsNotDropped(): void
    {
        $field = new CustomField(12, [], '0');
        $this->assertEquals(['id' => 12, 'value' => '0'], $field->toArray());
    }

    public function testEmptyValueAndOptionsAreOmitted(): void
    {
        $field = new CustomField(12);
        $this->assertEquals(['id' => 12], $field->toArray());
    }

    public function testOptionsSerialized(): void
    {
        $field = new CustomField(12, [3, 4]);
        $this->assertEquals(['id' => 12, 'options' => [3, 4]], $field->toArray());
    }
}
