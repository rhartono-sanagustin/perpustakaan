<?php

namespace Tests\Unit;

use App\Support\MemberBarcode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MemberBarcodeTest extends TestCase
{
    public function test_it_renders_an_inline_code_128_svg(): void
    {
        $svg = (new MemberBarcode)->svg('NIS12345');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('class="member-card-barcode"', $svg);
        $this->assertStringContainsString('aria-hidden="true"', $svg);
        $this->assertStringNotContainsString('<?xml', $svg);
        $this->assertStringNotContainsString('<!DOCTYPE', $svg);
    }

    public function test_it_rejects_an_empty_value(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MemberBarcode)->svg('   ');
    }

    public function test_it_rejects_non_ascii_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MemberBarcode)->svg('NIS-Ä');
    }

    public function test_it_rejects_values_that_are_too_long_for_a_scannable_card_barcode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MemberBarcode)->svg(str_repeat('9', MemberBarcode::MAX_LENGTH + 1));
    }

    public function test_it_accepts_the_maximum_length(): void
    {
        $svg = (new MemberBarcode)->svg(str_repeat('9', MemberBarcode::MAX_LENGTH));

        $this->assertStringContainsString('class="member-card-barcode"', $svg);
    }
}
