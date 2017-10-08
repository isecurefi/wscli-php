#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Humbug\SelfUpdate\Updater;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Docopt\Docopt;
use IsecureFi\WsCliPhpSdk\WsCli;
use Swagger\Client\ApiException;

global $doc;
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
  wscli account verifyEmail     [-c CONF] [-e EMAIL] [-m MODE] --code=<code> [--noninteractive] [--accesstoken=<token>]
  wscli account verifyPhone     [-c CONF] [-e EMAIL] [-m MODE] [--phone=<phone>] --code=<code> [--noninteractive]
  wscli account passwordReset   [-c CONF] [-e EMAIL] [-m MODE] [--noninteractive]
  wscli account register        [-c CONF] [-e EMAIL] [-m MODE] [-a APIKEY] [--name=<name>] [--company=<company>]
                                [--password=<password>] [--phone=<phone>] [--noninteractive]
  wscli session logout          [-c CONF] [-a APIKEY] [-i IDTOKEN]
  wscli session login           [-c CONF] [-e EMAIL] [-m MODE] [--password=<password>]
                                [--environment=<environment>] [--code=<mfacode>] [--session=<session>] [--noninteractive]
  wscli files listFiles         [-c CONF] [-a APIKEY] [-i IDTOKEN] [-b BANK] [--filetype=<filetype>] [--filestatus=<filestatus>]
  wscli files downloadFile      [-c CONF] [-a APIKEY] [-i IDTOKEN] [-b BANK] [--filetype=<filetype>] --filereference=<fileref>
  wscli files deleteFile        [-c CONF] [-a APIKEY] [-i IDTOKEN] [-b BANK] [--filetype=<filetype>] --filereference=<fileref>
  wscli files uploadFile        [-c CONF] [-a APIKEY] [-i IDTOKEN] [-b BANK] [--filetype=<filetype>] --filename=<filename> --filecontents=<filecontents> --signatures=<signatures>
  wscli files downloadFiles     [-c CONF] [-a APIKEY] [-i IDTOKEN] [-b BANK] [--filetype=<filetype>] [--filestatus=<filestatus>]
                                [--filereference=<fileref>] [--syncdir=<dir>]
  wscli certs listCerts         [-c CONF] [-a APIKEY] [-i IDTOKEN]
  wscli certs exportCert        [-c CONF] [-a APIKEY] [-i IDTOKEN] [-b BANK] [--pgpkeyid=<pgpkeyid>]
  wscli certs shareCerts        [-c CONF] [-a APIKEY] [-i IDTOKEN] [--extemail=<extemail>]
  wscli certs unshareCerts      [-c CONF] [-a APIKEY] [-i IDTOKEN] [--extemail=<extemail>]
  wscli certs enrollCert        [-c CONF] [-a APIKEY] [-i IDTOKEN] [-b BANK] [--pincode=<pincode>] [--company=<company>] [--wsuserid=<wsuserid>]
  wscli certs importCert        [-c CONF] [-a APIKEY] [-i IDTOKEN] [-b BANK] [--certificate=<pem>] [--privatekey=<pem>]
                                [--wsuserid=<wsuserid>] [--enccertificate=<pem>] [--encprivatekey=<pem>]
  wscli pgp listKeys            [-c CONF] [-a APIKEY] [-i IDTOKEN]
  wscli pgp deleteKey           [-c CONF] [-a APIKEY] [-i IDTOKEN] [--pgpkeyid=<pgpkeyid>]
  wscli pgp uploadKey           [-c CONF] [-a APIKEY] [-i IDTOKEN]
                                [--pgpkeycontents=<pgpkeycontents>] [--pgpkeypurpose=<purpose>]
  wscli integrator listAccounts [-c CONF] [-a APIKEY] [-i IDTOKEN]


Options:
  -h, --help                             Show this screen
  -v, --version                          Show version
  --update                               Update wscli to latest version
  --rollback                             Rollback wscli to previous version, after an update
  --noninteractive                       SDK will NOT interactively ask, e.g for password and MFA
  -c FILE, --config=FILE                 YAML configuration file containing all required parameters in 'settings' block
  -a APIKEY, --apikey=APIKEY             Integrator's API Key
  -i IDTOKEN, --idtoken=IDTOKEN          Identity carrying authorization token received from successful login
  -b BANK, --bank=BANK                   Target bank, e.g. nordea
  -e EMAIL, --email=EMAIL                Account user's email
  -m MODE, --mode=MODE                   admin or data account
  --name=<name>                          Account users's name
  --phone=<phone>                        Account user's phone number with country code, e.g. +358401234567
  --company=<company>                    Account user's company
  --password=<password>                  Account user's password for <mode> account
  --accesstoken=<token>                  When email is not verified, login returns access token that must be passed to emailVerify
  --session=<session>                    Session token, when e.g. SMS MFA is requested during login
  --environment=<env>                    Environment, test or production
  --filetype=<filetype>                  Bank's file type
  --filestatus=<filestatus>              Bank's file status, e.g. ALL
  --filecontents=<filecontents>          Upload file contents
  --filereference=<filereference>        Bank's file reference from listFiles entry
  --extemail=<extemail>                  Link extemail to this account for certificates sharing
  --code=<code>                          SMS MFA code or email verification code
  --pincode=<pincode>                    PIN code for WS certificate enrollment
  --wsuserid=<wsuserid>                  Use wsuserid in certificate enrollment
  --pgpkeyid=<pgpkeyid>                  PGP/GPG Key Id
  --outfilename=<outfilename>            Output filename for exported cert in PEM format
  --pgpkeycontents=<pgpkeycontents>      PGP key contents for PGP key upload
  --certificate=<pem>                    Imported certificate in PEM format
  --privatekey=<pem>                     Imported private key in PEM format, must match with certificate
  --enccertificate=<pem>                 DanskeBank only, imported encryption certificate
  --encprivatekey=<pem>                  DanskeBank only, imported private key for encryption, must match with enccertificate

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

function update($log, $phar)
{
    if (!is_writable(realpath($phar))) {
        $msg = "The PHAR is not writable by you and can not be updated."
             . PHP_EOL . "You may need to use e.g. sudo (" . realpath($phar) . ").";
        $log->error($msg);
        echo $msg . PHP_EOL;
        return 1;
    }
    if (!is_readable(realpath($phar) . ".pubkey")) {
        $msg = "The PHAR corresponding public key is not available and thus update can "
             . PHP_EOL . "not be verified. Did you install the public key too?"
             . PHP_EOL . "Missing: "
             . PHP_EOL . "   " . realpath($phar) . ".pubkey";
        $log->error($msg);
        echo $msg . PHP_EOL;
        return 1;
    }

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
        return update($log, $argv[0]);
    }
    if ($args['rollback']) {
        $log->debug("Running rollback");
        return rollback($log);
    }
    $args['version'] = '@package_version@';

    // API arguments are passed as an associative array when
    // instantiating the class.
    $sdk = new WsCli($args->args);
    try {
        $res = $sdk->${"cmd"}();
    } catch (ApiException $e) {
        echo json_encode($e->getResponseBody(), JSON_PRETTY_PRINT) . PHP_EOL;
        return 1;
    }
    $log->debug("response:\n" . $res);
    if (is_int($res)) {
        switch ($res) {
            case 0:
                echo "Session already exists, no need to login." . PHP_EOL;
                return $res;
            case 1:
                echo "Error, see log (wscli_sdk.log, wscli.log)" . PHP_EOL;
                return $res;
            case 2:
                echo "Session expired. Please re-login." . PHP_EOL;
                return $res;
            case 3:
                echo "API feature not yet implemented." . PHP_EOL;
                return $res;
            case 4:
                echo "Admin mode session required for this command, currently using data mode." . PHP_EOL;
                return $res;
        }
    }

    if ($cmd != "downloadFiles") {
        echo $res . PHP_EOL;
    }
    return 0;
}

return main();
