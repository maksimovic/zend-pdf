<?php

use PHPUnit\Framework\TestCase;

class Zend_Pdf_Filter_Compression_FlateTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('zlib')) {
            $this->markTestSkipped('This test requires zlib');
        }
    }

    public function testEncodeException()
    {
        $this->expectException(Zend_Pdf_Exception::class);
        $this->expectExceptionMessageMatches('/^(?!.*Not implemented yet).+$/s');

        Zend_Pdf_Filter_Compression_Flate::encode('test data', ['Predictor' => 99]);
    }

    public function testDecodeException()
    {
        $this->expectException(Zend_Pdf_Exception::class);
        $this->expectExceptionMessageMatches('/^(?!.*Not implemented yet).+$/s');

        Zend_Pdf_Filter_Compression_Flate::decode(null);
    }
}
