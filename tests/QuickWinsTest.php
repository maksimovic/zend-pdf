<?php

use PHPUnit\Framework\TestCase;

class Zend_Pdf_QuickWinsTest extends TestCase
{
    // --- Zend_Pdf ---

    public function testPdfDate(): void
    {
        $date = Zend_Pdf::pdfDate(mktime(12, 0, 0, 6, 15, 2025));
        $this->assertStringStartsWith('D:', $date);
        $this->assertStringContainsString('2025', $date);
    }

    public function testGetSetMemoryManager(): void
    {
        $mm = Zend_Pdf::getMemoryManager();
        $this->assertInstanceOf(Zend_Memory_Manager::class, $mm);
        Zend_Pdf::setMemoryManager($mm);
        $this->assertSame($mm, Zend_Pdf::getMemoryManager());
    }

    public function testRevisionsOnLoadedPdf(): void
    {
        $pdf = Zend_Pdf::load(__DIR__ . '/_files/pdfarchiving.pdf');
        $this->assertGreaterThanOrEqual(1, $pdf->revisions());
    }

    public function testGetOpenActionDefault(): void
    {
        $pdf = new Zend_Pdf();
        $this->assertNull($pdf->getOpenAction());
    }

    public function testGetNamedDestinationsEmpty(): void
    {
        $pdf = new Zend_Pdf();
        $this->assertIsArray($pdf->getNamedDestinations());
        $this->assertEmpty($pdf->getNamedDestinations());
    }

    public function testGetMetadataEmpty(): void
    {
        $pdf = new Zend_Pdf();
        $this->assertNull($pdf->getMetadata());
    }

    public function testParseRenderedPdf(): void
    {
        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
        $page->drawText('Parse test', 72, 720);
        $pdf->pages[] = $page;
        $data = $pdf->render();
        $pdf2 = Zend_Pdf::parse($data);
        $this->assertCount(1, $pdf2->pages);
    }

    public function testProperties(): void
    {
        $pdf = new Zend_Pdf();
        $pdf->properties['Title'] = 'Test';
        $pdf->properties['Author'] = 'PHPUnit';
        $this->assertSame('Test', $pdf->properties['Title']);
        $this->assertSame('PHPUnit', $pdf->properties['Author']);
    }

    public function testSaveAndReloadPreservesProperties(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'zend_pdf_props_') . '.pdf';
        try {
            $pdf = new Zend_Pdf();
            $pdf->properties['Title'] = 'Saved';
            $pdf->pages[] = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->save($file);

            $pdf2 = Zend_Pdf::load($file);
            $this->assertSame('Saved', $pdf2->properties['Title']);
        } finally {
            @unlink($file);
        }
    }

    public function testSetAndGetMetadata(): void
    {
        $pdf = new Zend_Pdf();
        $pdf->pages[] = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $xml = '<?xml version="1.0"?><metadata>test</metadata>';
        $pdf->setMetadata($xml);
        $this->assertSame($xml, $pdf->getMetadata());
    }

    // --- Zend_Pdf_Page ---

    public function testPageGetWidthAndHeight(): void
    {
        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->assertEquals(595, $page->getWidth());
        $this->assertEquals(842, $page->getHeight());
    }

    public function testPageGetWidthAndHeightLetter(): void
    {
        $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_LETTER);
        $this->assertEquals(612, $page->getWidth());
        $this->assertEquals(792, $page->getHeight());
    }

    public function testPageCustomSize(): void
    {
        $page = new Zend_Pdf_Page(100, 200);
        $this->assertEquals(100, $page->getWidth());
        $this->assertEquals(200, $page->getHeight());
    }

    // --- Zend_Pdf_Color ---

    public function testColorHtmlHex(): void
    {
        $color = Zend_Pdf_Color_Html::color('#FF0000');
        $this->assertInstanceOf(Zend_Pdf_Color_Rgb::class, $color);

        $color2 = Zend_Pdf_Color_Html::color('#9999cc');
        $this->assertInstanceOf(Zend_Pdf_Color_Rgb::class, $color2);
    }

    public function testColorRgb(): void
    {
        $color = new Zend_Pdf_Color_Rgb(1, 0, 0);
        $this->assertInstanceOf(Zend_Pdf_Color_Rgb::class, $color);
    }

    public function testColorGrayScale(): void
    {
        $color = new Zend_Pdf_Color_GrayScale(0.5);
        $this->assertInstanceOf(Zend_Pdf_Color_GrayScale::class, $color);
    }

    public function testColorCmyk(): void
    {
        $color = new Zend_Pdf_Color_Cmyk(0, 0, 0, 1);
        $this->assertInstanceOf(Zend_Pdf_Color_Cmyk::class, $color);
    }

    // --- Zend_Pdf_Image ---

    public function testImageJpeg(): void
    {
        $image = Zend_Pdf_Image::imageWithPath(__DIR__ . '/_files/stamp.jpg');
        $this->assertInstanceOf(Zend_Pdf_Resource_Image::class, $image);

        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page->drawImage($image, 50, 500, 200, 700);
        $pdf->pages[] = $page;
        $data = $pdf->render();
        $this->assertStringStartsWith('%PDF', $data);
    }

    public function testImageTiff(): void
    {
        $image = Zend_Pdf_Image::imageWithPath(__DIR__ . '/_files/stamp.tif');
        $this->assertInstanceOf(Zend_Pdf_Resource_Image::class, $image);
    }

    public function testImagePng(): void
    {
        $image = Zend_Pdf_Image::imageWithPath(__DIR__ . '/_files/stamp.png');
        $this->assertInstanceOf(Zend_Pdf_Resource_Image::class, $image);
    }

    // --- Zend_Pdf_Font ---

    public function testAllStandardFonts(): void
    {
        $fonts = [
            Zend_Pdf_Font::FONT_COURIER,
            Zend_Pdf_Font::FONT_COURIER_BOLD,
            Zend_Pdf_Font::FONT_COURIER_OBLIQUE,
            Zend_Pdf_Font::FONT_COURIER_BOLD_OBLIQUE,
            Zend_Pdf_Font::FONT_HELVETICA,
            Zend_Pdf_Font::FONT_HELVETICA_BOLD,
            Zend_Pdf_Font::FONT_HELVETICA_OBLIQUE,
            Zend_Pdf_Font::FONT_HELVETICA_BOLD_OBLIQUE,
            Zend_Pdf_Font::FONT_TIMES,
            Zend_Pdf_Font::FONT_TIMES_BOLD,
            Zend_Pdf_Font::FONT_TIMES_ITALIC,
            Zend_Pdf_Font::FONT_TIMES_BOLD_ITALIC,
            Zend_Pdf_Font::FONT_SYMBOL,
            Zend_Pdf_Font::FONT_ZAPFDINGBATS,
        ];
        foreach ($fonts as $fontName) {
            $font = Zend_Pdf_Font::fontWithName($fontName);
            $this->assertInstanceOf(Zend_Pdf_Resource_Font::class, $font);
        }
    }

    public function testTtfFont(): void
    {
        $font = Zend_Pdf_Font::fontWithPath(__DIR__ . '/_fonts/Vera.ttf');
        $this->assertInstanceOf(Zend_Pdf_Resource_Font::class, $font);
    }

    public function testFontMetrics(): void
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $this->assertIsNumeric($font->getAscent());
        $this->assertIsNumeric($font->getDescent());
        $this->assertIsNumeric($font->getLineGap());
        $this->assertGreaterThan(0, $font->getUnitsPerEm());
    }

    // --- Canvas drawing methods ---

    public function testDrawingMethodsReturnPage(): void
    {
        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);

        // All drawing methods should return $page for fluent interface
        $result = $page->drawText('test', 72, 720);
        $this->assertSame($page, $result);

        $result = $page->drawLine(0, 0, 100, 100);
        $this->assertSame($page, $result);

        $result = $page->drawRectangle(10, 10, 100, 100);
        $this->assertSame($page, $result);

        $result = $page->drawRoundedRectangle(10, 10, 100, 100, 5);
        $this->assertSame($page, $result);

        $result = $page->drawCircle(100, 100, 50);
        $this->assertSame($page, $result);

        $result = $page->drawEllipse(50, 50, 200, 150);
        $this->assertSame($page, $result);
    }

    public function testGraphicsStateSaveRestore(): void
    {
        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);

        $page->saveGS();
        $page->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 0));
        $page->setLineColor(new Zend_Pdf_Color_Rgb(0, 0, 1));
        $page->setLineWidth(3);
        $page->drawRectangle(10, 10, 100, 100);
        $page->restoreGS();

        $page->drawText('After restore', 72, 500);

        $pdf->pages[] = $page;
        $data = $pdf->render();
        $this->assertStringStartsWith('%PDF', $data);
    }

    public function testLineDashingPatterns(): void
    {
        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);

        $page->setLineDashingPattern(Zend_Pdf_Page::LINE_DASHING_SOLID);
        $page->drawLine(0, 700, 500, 700);

        $page->setLineDashingPattern(array(3, 2, 3, 4), 1.6);
        $page->drawLine(0, 680, 500, 680);

        $pdf->pages[] = $page;
        $data = $pdf->render();
        $this->assertStringStartsWith('%PDF', $data);
    }

    public function testRotateAndClip(): void
    {
        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);

        $page->saveGS();
        $page->rotate(300, 400, deg2rad(45));
        $page->drawText('Rotated text', 300, 400);
        $page->restoreGS();

        $page->clipRectangle(50, 50, 500, 500);
        $page->drawRectangle(0, 0, 600, 600);

        $pdf->pages[] = $page;
        $data = $pdf->render();
        $this->assertStringStartsWith('%PDF', $data);
    }
}
