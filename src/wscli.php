#!/usr/bin/env php
<?php
/**
 * This file is part of the IsecureFi.WsCliPhp
 */
namespace IsecureFi\WsCliPhp;

require __DIR__ . '/../vendor/autoload.php';

use Humbug\SelfUpdate\Updater;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Docopt\Docopt;
use IsecureFi\WsCli;

$doc = <<<DOC

ISECure WS-Channel command line client for transferring files with
banks in Finland. Includes registration, enrollemnt and sharing of
certificates, and managing settings with admin account whilst doing
file transfers with data account. Admin account requires and
additional one-time SMS password.

https://isecurefi.github.io/wscli-php/

Usage:
  wscli (-h | --help)
  wscli (-v | --version)
  wscli --update
  wscli --rollback
  wscli account verifyEmail     [--email=<email>] [--mode=<mode>] [--code=<code>] [--config=<file>]
  wscli account verifyPhone     [--email=<email>] [--mode=<mode>] [--phone=<phone>] [--config=<file>]
  wscli account passwordReset   [--email=<email>] [--mode=<mode>]
  wscli account register        [--email=<email>] [--mode=<mode>] [--name=<name>] [--apikey=<apikey>]
                                [--company=<company>] [--password=<password>] [--phone=<phone>] [--config=<file>]
  wscli session logout          [--apikey=<apikey>] [--idtoken=<idtoken>]
  wscli session login           [--email=<email>] [--mode=<mode>] [--password=<password>]
                                [--environment=<environment>] [--config=<file>]
  wscli files listFiles         [--apikey=<apikey>] [--idtoken=<idtoken>] [--bank=<bank>] [--filetype=<filetype>] [--filestatus=<filestatus>]
  wscli files downloadFiles     [--apikey=<apikey>] [--idtoken=<idtoken>] [--bank=<bank>] [--filetype=<filetype>] [--filestatus=<filestatus>]
  wscli files downloadFile      [--apikey=<apikey>] [--idtoken=<idtoken>] [--bank=<bank>] [--fileref=<fileref>]
  wscli files deleteFile        [--apikey=<apikey>] [--idtoken=<idtoken>] [--bank=<bank>] [--fileref=<fileref>]
  wscli files uploadFile        [--apikey=<apikey>] [--idtoken=<idtoken>] [--bank=<bank>] [--filetype=<filetype>]
                                [--filecontents=<filecontents>]
  wscli certs enrollCert        [--apikey=<apikey>] [--idtoken=<idtoken>] [--bank=<bank>]
  wscli certs importCert        [--apikey=<apikey>] [--idtoken=<idtoken>]
  wscli certs exportCert        [--apikey=<apikey>] [--idtoken=<idtoken>] [--bank=<bank>] [--pgpkeyid=<pgpkeyid>]
  wscli certs shareCerts        [--apikey=<apikey>] [--idtoken=<idtoken>] [--extemail=<extemail>]
  wscli certs unshareCerts      [--apikey=<apikey>] [--idtoken=<idtoken>] [--extemail=<extemail>]
  wscli certs listCerts         [--apikey=<apikey>] [--idtoken=<idtoken>] [--pincode=<pincode>]
                                [--company=<company>] [--wsuserid=<wsuserid>] [--wstargetid=<wstargetid>]
  wscli pgp listKeys            [--apikey=<apikey>] [--idtoken=<idtoken>]
  wscli pgp uploadKey           [--apikey=<apikey>] [--idtoken=<idtoken>] [--pgpkeycontents=<pgpkeycontents>]
  wscli pgp deleteKey           [--apikey=<apikey>] [--idtoken=<idtoken>] [--pgpkeyid=<pgpkeyid>]


Options:
  -h --help                              Show this screen
  -v --version                           Show version
  --update                               Update wscli to latest version
  --rollback                             Rollback wscli to previous version, after an update
  --name=<name>                          Account users's name
  --email=<email>                        Account user's email
  --phone=<phone>                        Account user's phone number with country code, e.g. +358401234567
  --company=<company>                    Account user's company
  --password=<password>                  Account user's password for <mode> account
  --apikey=<apikey>                      Integrator's API Key
  --idtoken=<idtoken>                    Session authorization token received from successful login
  --mode=<mode>                          admin or data account
  --environment=<env>                    Environment, test or production
  --bank=<bank>                          Target bank, e.g. nordea
  --filetype=<filetype>                  Bank's file type
  --filestatus=<filestatus>              Bank's file status, e.g. ALL
  --filecontents=<filecontents>          Upload file contents
  --filereference=<filereference>        Bank's file reference from listFiles entry
  --extemail=<extemail>                  Link extemail to this account for certificates sharing
  --pincode=<pincode>                    PIN code for WS certificate enrollment
  --wstargetid=<wstargetid>              Use wstargetid in certificate enrollment
  --wsuserid=<wsuserid>                  Use wsuserid in certificate enrollment
  --pgpkeyid=<pgpkeyid>                  PGP/GPG Key Id
  --pgpkeycontents=<pgpkeycontents>      PGP key contents for PGP key upload
  --config=<file>                        YAML configuration file containing all required parameters in 'settings' block

DOC;

function createLogger()
{
    $log = new Logger('WsCliPhp');
    $stream = new StreamHandler('wscli.log', Logger::DEBUG);
    $stream->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n", 'Y-m-d H:i:s', true, true));
    $log->pushHandler($stream);

    return $log;
}

function getArgs()
{
    global $doc;

    $args = \Docopt::handle($doc, [
        'version' => '@package_version@',
        'help' => true,
    ]);

    // Remove '--' from keys
    foreach ($args as $k => $v) {
        $args[str_replace('--', '', $k)] = $v;
        if (substr($k, 0, 2) == "--") {
            unset($args[$k]);
        }
    }

    return $args;
}

function update($log)
{
    $updater = new Updater();
    /*
      $updater->setStrategy(Updater::STRATEGY_GITHUB);
      $updater->getStrategy()->setPackageName('isecurefi/wscli-php');
      $updater->getStrategy()->setPharName('wscli.phar');
      $updater->getStrategy()->setCurrentLocalVersion('@package_version@');
    */
    $updater->getStrategy()->setPharUrl('https://isecurefi.github.io/wscli-php/wscli.phar');
    $updater->getStrategy()->setVersionUrl('https://isecurefi.github.io/wscli-php/wscli.phar.version');
    try {
        $result = $updater->update();
        $log->debug($result ? "Updated!\n" : "No update needed!\n");
        echo $result ? "Updated!\n" : "No update needed!\n";
        return 0;
    } catch (\Exception $e) {
        $log->error("Update failed" . print_r($e, true));
        echo "Update failed!" . PHP_EOL;
        echo $e->getMessage() . PHP_EOL;
        echo $e->getTraceAsString() . PHP_EOL;
        return 1;
    }
}

function rollback($log)
{
    $updater = new Updater();
    try {
        $result = $updater->rollback();
        $log->debug($result ? "Version rolled back!\n" : "Rollback failed!\n");
        echo $result ? "Version rolled back!\n" : "Rollback failed!\n";
        return 0;
    } catch (\Exception $e) {
        $log->error("Rollback failed" . print_r($e, true));
        echo "Rollback failed!" . PHP_EOL;
        echo $e->getMessage() . PHP_EOL;
        echo $e->getTraceAsString() . PHP_EOL;
        return 1;
    }
}

function main()
{
    global $argv;

    date_default_timezone_set("Europe/Helsinki");

    $args = getArgs();
    $log = createLogger();

    $args['<api>'] = array_key_exists(1, $argv) ? $argv[1] : "n/a";
    $args['<cmd>'] = array_key_exists(2, $argv) ? $argv[2] : "n/a";
    $cmd = $args['<cmd>'];
    $log->debug("api: " . $args['<api>']);
    $log->debug("cmd: " . $cmd);

    if ($args['update']) {
        $log->debug("Running update");
        return update($log);
    }
    if ($args['rollback']) {
        $log->debug("Running rollback");
        return rollback($log);
    }

    $sdk = new \IsecureFi\WsCliPhpSdk\WsCli($args->args);
    $res = $sdk->${"cmd"}();
    $log->debug("response:\n" . $res);
    if ($res === 1) {
        echo "Error, see log" . PHP_EOL;
        return $res;
    }
    if ($res === 2) {
        echo "Session expired. Please re-login" . PHP_EOL;
        return $res;
    }
    if ($res === 3) {
        echo "API feature not yet implemented." . PHP_EOL;
        return $res;
    }

    echo $res . PHP_EOL;
    return 0;
}

return main();