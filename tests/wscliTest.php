<?php

use IsecureFi\wscli\wscli;
use PHPUnit\Framework\TestCase;

class WsCliPhpSdkTest extends TestCase
{
    /**
     * @var WsCliPhp
     */
    protected $wscli;

    protected function setUp()
    {
        parent::setUp();
    }

    public function testDummy()
    {
        $this->assertContains(true, [ true ]);
    }

}
