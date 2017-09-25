<?php

use IsecureFi\WsCliPhpSdk\WsCli;
use PHPUnit\Framework\TestCase;

class WsCliPhpSdkTest extends TestCase
{
    /**
     * @var WsCliPhpSdk
     */
    protected $wscli;

    protected function setUp()
    {
        parent::setUp();
        $opts = [];
        $opts['version'] = 'test 0.0.1';
        $this->wscli = new WsCli($opts);
    }

    public function testNew()
    {
        $this->assertInstanceOf('\IsecureFi\WsCliPhpSdk\WsCli', $this->wscli);
        echo $this->wscli->version();
    }
}
