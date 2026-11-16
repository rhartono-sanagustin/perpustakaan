<?php

namespace App\Support;

use InvalidArgumentException;
use Picqer\Barcode\BarcodeGeneratorSVG;

class MemberBarcode
{
    /**
     * Maximum barcode payload length that still fits an ID-1 card at a
     * scannable module width (1 px ≈ 0.26 mm at print size).
     */
    public const MAX_LENGTH = 20;

    /**
     * Render a Code 128 barcode as an inline SVG string.
     *
     * The barcode width is capped so it fits inside an ID-1 member card.
     */
    public function svg(string $value, int $height = 40): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Nomor induk tidak boleh kosong.');
        }

        if (preg_match('/[^\x20-\x7E]/', $value) === 1) {
            throw new InvalidArgumentException('Nomor induk mengandung karakter yang tidak didukung barcode.');
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Nomor induk lebih dari '.self::MAX_LENGTH.' karakter sehingga barcode tidak dapat dicetak dengan ukuran yang terbaca.');
        }

        $modules = max(1, (strlen($value) * 11) + 35);
        $widthFactor = max(1, min(3, intdiv(300, $modules)));

        $generator = new BarcodeGeneratorSVG;
        $svg = $generator->getBarcode(
            $value,
            BarcodeGeneratorSVG::TYPE_CODE_128,
            $widthFactor,
            $height,
        );

        // Remove the XML prolog and doctype so the SVG can be embedded in HTML.
        $svg = preg_replace('/<\?xml.*?\?>\s*/s', '', $svg) ?? $svg;
        $svg = preg_replace('/<!DOCTYPE.*?>\s*/s', '', $svg) ?? $svg;

        return preg_replace(
            '/<svg /',
            '<svg class="member-card-barcode" aria-hidden="true" focusable="false" ',
            $svg,
            1,
        ) ?? $svg;
    }
}
