<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

use PHPUnit\Framework\TestCase;

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_Element_ObjectTest extends TestCase
{
    public function testPDFObject()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 1, 0, new Zend_Pdf_ElementFactory(1));

        $this->assertTrue($obj instanceof Zend_Pdf_Element_Object);
    }

    public function testPDFObjectBadObjectType1()
    {
        $this->expectException(Zend_Pdf_Exception::class);
        $this->expectExceptionMessageMatches('/must not be an instance of Zend_Pdf_Element_Object/i');
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj1   = new Zend_Pdf_Element_Object($intObj, 1, 0, new Zend_Pdf_ElementFactory(1));
        $obj2 = new Zend_Pdf_Element_Object($obj1, 1, 0, new Zend_Pdf_ElementFactory(1));
    }

    public function testPDFObjectBadGenNumber1()
    {
        $this->expectException(Zend_Pdf_Exception::class);
        $this->expectExceptionMessageMatches('/non-negative integer/i');
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 1, -1, new Zend_Pdf_ElementFactory(1));
    }

    public function testPDFObjectBadGenNumber2()
    {
        $this->expectException(Zend_Pdf_Exception::class);
        $this->expectExceptionMessageMatches('/non-negative integer/i');
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 1, 1.2, new Zend_Pdf_ElementFactory(1));
    }

    public function testPDFObjectBadObjectNumber1()
    {
        $this->expectException(Zend_Pdf_Exception::class);
        $this->expectExceptionMessageMatches('/positive integer/i');
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 0, 0, new Zend_Pdf_ElementFactory(1));
    }

    public function testPDFObjectBadObjectNumber2()
    {
        $this->expectException(Zend_Pdf_Exception::class);
        $this->expectExceptionMessageMatches('/positive integer/i');
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, -1, 0, new Zend_Pdf_ElementFactory(1));
    }

    public function testPDFObjectBadObjectNumber3()
    {
        $this->expectException(Zend_Pdf_Exception::class);
        $this->expectExceptionMessageMatches('/positive integer/i');
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 1.2, 0, new Zend_Pdf_ElementFactory(1));
    }

    public function testGetType()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 1, 0, new Zend_Pdf_ElementFactory(1));

        $this->assertEquals($obj->getType(), $intObj->getType());
    }

    public function testToString()
    {
        $intObj = new Zend_Pdf_Element_Numeric(100);
        $obj    = new Zend_Pdf_Element_Object($intObj, 55, 3, new Zend_Pdf_ElementFactory(1));

        $this->assertEquals($obj->toString(), '55 3 R');
    }

    public function testDump()
    {
        $factory = new Zend_Pdf_ElementFactory(1);

        $intObj  = new Zend_Pdf_Element_Numeric(100);
        $obj     = new Zend_Pdf_Element_Object($intObj, 55, 3, $factory);

        $this->assertEquals($obj->dump($factory), "55 3 obj \n100\nendobj\n");
    }
}
