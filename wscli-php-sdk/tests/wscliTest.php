<?php

use IsecureFi\WsCliPhpSdk\WsCliAccount;
use PHPUnit\Framework\TestCase;

class WsCliPhpSdkTest extends TestCase
{
    /**
     * @var WsCliPhpSdk
     */
    protected $wsclisdk;

    protected function setUp()
    {
        parent::setUp();
        $this->wscli_account = new WsCliAccount;
    }

    public function testNew()
    {
        $actual = $this->wscli_account;
        $this->assertInstanceOf('\IsecureFi\WsCliPhpSdk\WsCliAccount', $actual);
    }
}
