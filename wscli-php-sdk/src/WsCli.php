<?php
/**
 * This file is part of the IsecureFi.WsCliPhpSdk
 */
namespace IsecureFi\WsCliPhpSdk;

use \Monolog\Formatter\LineFormatter;
use \Monolog\Handler\StreamHandler;
use \Monolog\Logger;
use \Swagger\Client\ApiClient;
use \Swagger\Client\Api\AccountApi;
use \Swagger\Client\Api\SessionApi;
use \Swagger\Client\Api\PgpApi;
use \Swagger\Client\Api\IntegratorApi;
use \Swagger\Client\Api\FilesApi;
use \Swagger\Client\Api\CertsApi;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use SebastianBergmann\Version;

// Extends swagger generated SDK
class WsCli
{
    private $configFilename = '';
    private $retries = 3;

    // APIs
    private $account = '';
    private $session = '';
    private $certs = '';
    private $pgp = '';
    private $files = '';
    private $WsCliSDKVersion = '';
    
    public function __construct($args)
    {
        $this->log = new Logger('WsCliPhpSdk');
        $stream = new StreamHandler('wscli_sdk.log', Logger::DEBUG);
        $stream->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n", 'Y-m-d H:i:s', true, true));
        $this->log->pushHandler($stream);

        $this->WsCliSDKVersion = new Version(trim(file_get_contents(__DIR__.'/VERSION')), __DIR__);
        $this->log->info($this->version());

        $this->opts = $args;
        $this->retries = 3;
        $this->log->debug("opts before merge: " . print_r($this->opts, true));
        $this->getOpts();

        $config = new \Swagger\Client\Configuration();
        $this->log->debug("x-api-key: " . $this->opts['apikey']);
        $config->setApiKey("x-api-key", $this->opts['apikey']);
        $config->setSSLVerification(true);
        $config->setUserAgent($this->version());
        if (array_key_exists('version', $this->opts)) {
            $ua = $this->version() ." wscli " . $this->opts['version'];
            $this->log->info($ua);
            $config->setUserAgent($ua);
        }
        // curl debug by default to the same log file
        $config->setDebug(true);
        $config->setDebugFile("wscli_sdk.log");
        // Assumes that production URL is testing URL when ".test" is
        // removed from it.
        if (array_key_exists('environment', $this->opts) &&
            $this->opts['environment'] === 'production') {
            $host = str_replace(".test", "", $config->getHost());
            $this->log->info("setting production host: " . $host);
            $config->setHost($host);
        }
        $apiClient = new \Swagger\Client\ApiClient($config);

        $this->account = new \Swagger\Client\Api\AccountApi($apiClient);
        $this->session = new \Swagger\Client\Api\SessionApi($apiClient);
        $this->files = new \Swagger\Client\Api\FilesApi($apiClient);
        $this->pgp = new \Swagger\Client\Api\PgpApi($apiClient);
        $this->integrator = new \Swagger\Client\Api\IntegratorApi($apiClient);
        $this->certs = new \Swagger\Client\Api\CertsApi($apiClient);
    }

    private function ensureAdminMode()
    {
        if (!array_key_exists('mode', $this->opts)) {
            $this->log->error("Missing mode parameter");
            return 1;
        }
        if (strtolower($this->opts['mode']) !== "admin") {
            $this->log->error("Must use admin mode (current mode: " . $this->opts['mode'] . ")!");
            return 1;
        }
        return 0;
    }

    private function checkArgs($required = [])
    {
        $error = 0;
        foreach ($required as $req) {
            if (!array_key_exists($req, $this->opts)) {
                $this->log->error("$req not specified!");
                $error = 1;
            }
        }
        return $error;
    }

    private function getPassword()
    {
        if (array_key_exists('password', $this->opts) &&
            strlen((string)$this->opts['password']) < 20) {
            $this->log->error("Password length shorter than 20");
            if (array_key_exists('noninteractive', $this->opts) &&
                $this->opts['noninteractive']) {
                return 1;
            }
        }
        if (!array_key_exists('password', $this->opts)) {
            $this->log->error("Password not specified!");
            if (array_key_exists('noninteractive', $this->opts) &&
                $this->opts['noninteractive']) {
                return 1;
            }
            $this->opts['password'] = '';
            while (strlen((string)$this->opts['password']) < 20) {
                $this->opts['password'] = readline("Give password (at least 20 chars, upper/lower letters and special chars): ");
            }
        }
        if (!$this->getEncryptedPassword()) {
            $this->log->error("Could not get encrypted password.");
            return 1;
        }
    }

    private function handleAccount($api, $cmd, $fromApi, $fromCmd)
    {
        $error = $this->checkArgs(['email', 'mode']);
        if (!$this->getChallenge()) {
            $this->log->error("Could not get challenge.");
            $error = 1;
        }
        if (!$this->getEncryptedPassword()) {
            $this->log->error("Could not get encrypted password.");
            $error = 1;
        }
        switch ($cmd) {
            case "register":
                if (!array_key_exists('password', $this->opts)) {
                    $this->log->error("password not specified!");
                    $error = 1;
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->getBodyParams(),
                    $this->opts['email'],
                    $this->opts['mode']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "verifyEmail":
                while (strlen((string)$this->opts['code']) != 6) {
                    $this->log->error("Code not long enough: " . $this->opts['code'] . ".");
                    if (array_key_exists('noninteractive', $this->opts) &&
                    $this->opts['noninteractive']) {
                        return 1;
                    }
                    $this->opts['code'] = readline("Give EMail verification code: ");
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->getBodyParams(),
                    $this->opts['email'],
                    $this->opts['mode']
                );
                    $this->log->debug(print_r($resp, true));
                if ($fromApi && $fromCmd && $fromCmd != $this->opts['<cmd>']) {
                    $this->log->debug("Callbacking to " . $fromApi . "->" . $fromCmd);
                    $this->opts['<cmd>'] = $fromCmd;
                    $this->opts['<api>'] = $fromApi;
                    $this->opts['code'] = '';
                    return $this->__call($cmd, [$api]);
                }
                return $resp;
            case "verifyPhone":
                while (strlen((string)$this->opts['code']) != 6) {
                    $this->log->error("Code not long enough: " . $this->opts['code'] . ".");
                    if (array_key_exists('noninteractive', $this->opts) &&
                    $this->opts['noninteractive']) {
                        return 1;
                    }
                    $this->opts['code'] = readline("Give phone registration code: ");
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->getBodyParams(),
                    $this->opts['email'],
                    $this->opts['mode'],
                    $this->opts['phone']
                );
                    $this->log->debug(print_r($resp, true));
                if ($fromApi && $fromCmd && $fromCmd != $this->opts['<cmd>']) {
                    $this->log->debug("Callbacking to " . $fromApi . "->" . $fromCmd);
                    $this->opts['<cmd>'] = $fromCmd;
                    $this->opts['<api>'] = $fromApi;
                }
                return $resp;
            case "passwordReset":
                if ($error) {
                    return $error;
                }
                if (!$this->getChallenge()) {
                    $this->log->error("Could not get challenge.");
                    return 1;
                }
                if (!array_key_exists('code', $this->opts)) {
                    $resp = $this->${"api"}->initPasswordReset(
                        $this->opts['email'],
                        $this->opts['mode']
                    );
                    if ($resp['response_code'] != "00") {
                        $this->log->error("Failed to init password reset");
                        $this->log->error(print_r($resp, true));
                        return $resp;
                    }
                    if (array_key_exists('noninteractive', $this->opts) &&
                        $this->opts['noninteractive']) {
                        return $resp;
                    }
                }
                if ($this->getPassword()) {
                    return 1;
                }
                if (!array_key_exists('code', $this->opts)) {
                    $this->log->error("Code not specified");
                    if (array_key_exists('noninteractive', $this->opts) &&
                    $this->opts['noninteractive']) {
                        return 1;
                    }
                    $this->opts['code'] = readline("Give password reset SMS code: ");
                }
                $resp = $this->${"api"}->passwordReset(
                    $this->getBodyParams(),
                    $this->opts['email'],
                    $this->opts['mode']
                );
                if ($resp['response_code'] != "00") {
                    $this->log->error("Failed to reset password!");
                    $this->log->error(print_r($resp, true));
                    return $resp;
                }
                    $this->log->debug("Password has been reset.");
                return $resp;
            default:
                $this->log->error("Unknown api command");
                break;
        }
    }

    private function handleSession($api, $cmd)
    {
        switch ($cmd) {
            case "logout":
                $error = $this->checkArgs(['email', 'mode', 'idtoken']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->opts['email'],
                    $this->opts['mode']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "loginMFA":
            case "login":
                if (array_key_exists('code', $this->opts)) {
                    if (strlen((string)$this->opts['code']) == 6) {
                        $this->log->debug("Setting to LoginMFA as we have 6 digit code.");
                        $cmd = "loginMFA";
                    }
                    if (strlen((string)$this->opts['code']) == 0) {
                        $this->log->debug("Removing code parameter as it is empty.");
                        unset($this->opts['code']);
                    }
                }
                if (array_key_exists('idtoken', $this->opts) &&
                strlen($this->opts['idtoken']) > 0 &&
                array_key_exists('mode', $this->opts) &&
                strlen($this->opts['mode']) > 0 &&
                array_key_exists('idtokenmode', $this->opts) &&
                strlen($this->opts['idtokenmode']) > 0 &&
                $this->opts['mode'] === $this->opts['idtokenmode']) {
                    $this->log->info("No need to do login, valid idtoken exists.");
                    return 0;
                }
                $error = $this->checkArgs(['email', 'mode']);
                if (!$this->getChallenge()) {
                    $this->log->error("Could not get challenge.");
                    $error = 1;
                }
                if ($this->getPassword()) {
                    $error = 1;
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->getBodyParams(),
                    $this->opts['email'],
                    $this->opts['mode']
                );
                    $this->opts['code'] = ''; // Reset code if any
                    $this->log->debug(print_r($resp, true));
                if ($resp['response_code'] == "00" &&
                    (!array_key_exists('noninteractive', $this->opts) ||
                     !$this->opts['noninteractive'])) {
                    if (strstr($resp['response_text'], "Verify phone number")) {
                        $this->opts['<api>'] = "account";
                        $this->opts['<cmd>'] = "verifyPhone";
                        return $this->__call("login", [$api]);
                    }
                    if (strstr($resp['response_text'], "Give SMS code")) {
                        while (strlen((string)$this->opts['code']) != 6) {
                            $this->opts['code'] = readline("Give SMS code: ");
                        }
                        $this->opts['session'] = $resp['session'];
                        $this->opts['<cmd>'] = "loginMFA";
                        return $this->__call("login", [$api]);
                    }
                    if (strstr($resp['response_text'], "Verify email address")) {
                        $this->opts['accesstoken'] = $resp['access_token'];
                        $this->opts['<api>'] = "account";
                        $this->opts['<cmd>'] = "verifyEmail";
                        return $this->__call("login", [$api]);
                    }
                }
                if ($resp['response_code'] == "00" &&
                    $resp['response_text'] == "Login OK") {
                    $this->updateConfig("idtoken: " . $resp['id_token']);
                    $exp = date("Y-m-d H:i:s", time()+$resp['expires_in']-10);
                    $this->log->debug("idtokenexpiry: " . $exp);
                    if ($exp === false) {
                        $this->log->error("Could not form idtoken expiry date. Setting to +3300s");
                        $exp = time() + 3300;
                    }
                    $this->updateConfig("idtokenexpiry: \"" . $exp . "\"");
                    $this->updateConfig("idtokenmode: \"" . $this->opts['mode'] . "\"");
                }
                return $resp;
            default:
                $this->log->error("Unknown api command");
                break;
        }
    }

    private function handleFiles($api, $cmd)
    {
        if (!array_key_exists('idtoken', $this->opts)) {
            $this->log->error("no valid idtoken specified! please try re-login.");
            return 2;
        }
        switch ($cmd) {
            case "listFiles":
                $error = $this->checkArgs(['apikey', 'idtoken', 'bank', 'filestatus', 'filetype']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->opts['bank'],
                    $this->opts['filestatus'],
                    $this->opts['filetype']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "downloadFile":
                $error = $this->checkArgs(['apikey', 'idtoken', 'bank', 'filereference']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->opts['bank'],
                    $this->opts['filetype'],
                    $this->opts['filereference']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "uploadFile":
                $error = $this->checkArgs(['apikey', 'idtoken', 'bank', 'filetype', 'filecontents', 'signatures']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->getBodyParams(),
                    $this->opts['bank']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "downloadFiles":
                $this->opts['<cmd>'] = "listFiles";
                $this->opts['<api>'] = "files";
                $fileref = "";
                $localDir = ".";
                if (array_key_exists('filereference', $this->opts)) {
                    $fileref = $this->opts['filereference'];
                }
                if (array_key_exists('syncdir', $this->opts) &&
                $this->opts['syncdir'] != "" &&
                is_dir($this->opts['syncdir'])) {
                    if (is_writable($this->opts['syncdir'])) {
                        $localDir = $this->opts['syncdir'];
                    }
                    if (!is_writable($this->opts['syncdir'])) {
                        $this->log->error("Syncdir is not writable: " . $this->opts['syncdir']);
                        return 1;
                    }
                }
                $localDir = str_replace("//", "/", $localDir . "/");
                $this->log->debug("Local (sync) dir: " . $localDir);
                $retryCount = $this->retries;
                do {
                    if ($retryCount < $this->retries) {
                        $this->log->debug("retry " . $retryCount . "...");
                        echo "Trying again: ";
                    }
                    $resp = $this->__call($cmd, [$api]);
                    if ($resp['response_code'] != "00") {
                        echo " FAILED" . PHP_EOL;
                        $retryCount--;
                    }
                } while ($resp['response_code'] != "00" && $retryCount > 0);
                if ($resp['response_code'] == "00") {
                    foreach ($resp['file_descriptors'] as $desc) {
                        if ($fileref != "" && $fileref != $desc['file_reference']) {
                            $this->log->debug("Skipping " . $desc['file_reference'] . " while searching for " . $fileref);
                            continue;
                        }
                        $localFilename = $localDir
                                    . str_replace("-", "", substr($desc['file_timestamp'], 0, 10))
                                    . "__" . $desc['file_type']
                                    . "_" . $desc['file_reference']
                                    . ".dat";
                        echo "Checking " . $localFilename . "...";
                        if (file_exists($localFilename)) {
                            $this->log->debug("Not downloading " . $localFilename. ", as it already exists");
                            echo " already exists, skipping" . PHP_EOL;
                            continue;
                        }
                        echo " downloading...";
                        $this->opts['<cmd>'] = "downloadFile";
                        $this->opts['<api>'] = "files";
                        $this->opts['filereference'] = $desc['file_reference'];
                        $retryCount = $this->retries;
                        do {
                            if ($retryCount < $this->retries) {
                                $this->log->debug("retry " . $retryCount . "...");
                                echo "Trying again: ";
                            }
                            $downloadResp = $this->__call($cmd, [$api]);
                            if ($downloadResp['response_code'] == "00") {
                                if (strlen($downloadResp['content']) <= 0) {
                                    $this->log->error("No file content for file " . $localFilename . "(" . $desc['file_reference'] . ")");
                                    echo " no file content!";
                                }
                                if (strlen($downloadResp['content']) > 0 &&
                                file_put_contents($localFilename, base64_decode($downloadResp['content'])) === false) {
                                    $this->log->error("Failed to write file " . $localFilename);
                                    echo " failed!" . PHP_EOL;
                                    return 1;
                                }
                                echo " OK" . PHP_EOL;
                                $this->log->debug("Downloaded and stored file " . $localFilename);
                                if ($fileref && $fileref === $desc['file_reference']) {
                                    return $downloadResp;
                                }
                                break; // while loop
                            }
                            if ($downloadResp['response_code'] != "00") {
                                echo " FAILED" . PHP_EOL;
                                $retryCount--;
                            }
                        } while ($downloadResp['response_code'] != "00" && $retryCount > 0);
                        if ($downloadResp['response_code'] != "00" && $retryCount <= 0) {
                            $this->log->error("Failed to download file " . $localFilename);
                            $this->log->error(print_r($downloadResp, true));
                        }
                    }
                }
                return $resp;
            default:
                $this->log->error("Unknown api command");
                return 3;
        }
    }

    private function handlePgp($api, $cmd)
    {
        $this->log->debug("$api->$cmd");
        if (!array_key_exists('idtoken', $this->opts) || !$this->opts['idtoken']) {
            $this->log->error("no valid idtoken specified! please try re-login.");
            return 2;
        }
        if ($cmd != "listKeys" && $this->ensureAdminMode()) {
            return 4;
        }
        switch ($cmd) {
            case "listKeys":
                $error = $this->checkArgs(['apikey', 'idtoken']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "uploadKey":
                $error = $this->checkArgs(['apikey', 'idtoken', 'pgpkeycontents', 'pgpkeypurpose']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->getBodyParams() // PgpKey
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "deleteKey":
                $error = $this->checkArgs(['apikey', 'idtoken', 'pgpkeyid']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->getBodyParams() // PgpKeyId
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            default:
                $this->log->error("Unknown api command");
                return 3;
        }
    }

    private function handleCerts($api, $cmd)
    {
        $this->log->debug("$api->$cmd");
        if (!array_key_exists('idtoken', $this->opts) || !$this->opts['idtoken']) {
            $this->log->error("no valid idtoken specified! please try re-login.");
            return 2;
        }
        if ($cmd != "listCerts" && $this->ensureAdminMode()) {
            return 4;
        }
        switch ($cmd) {
            case "enrollCert":
                $error = $this->checkArgs(['apikey', 'idtoken', 'pincode','company', 'wsuserid']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->getBodyParams(),
                    $this->opts['bank']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "importCert":
                $error = $this->checkArgs(['apikey', 'idtoken', 'bank', 'certificate', 'privatekey']);
                if ($this->opts['bank'] === "danskebank") {
                    $error = $this->checkArgs(['apikey', 'idtoken', 'bank', 'certificate', 'privatekey', 'enccertificate', 'encprivatekey']);
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->getBodyParams(),
                    $this->opts['bank']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "exportCert":
                $error = $this->checkArgs(['apikey', 'idtoken', 'pgpkeyid']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->opts['bank'],
                    $this->opts['pgpkeyid']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "shareCerts":
                $error = $this->checkArgs(['apikey', 'idtoken', 'extemail']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->opts['extemail']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "unshareCerts":
                $error = $this->checkArgs(['apikey', 'idtoken', 'extemail']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken'],
                    $this->opts['extemail']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            case "listCerts":
                $error = $this->checkArgs(['apikey', 'idtoken']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            default:
                $this->log->error("Unknown api command");
                return 3;
        }
    }

    private function handleIntegrator($api, $cmd)
    {
        $this->log->debug("$api->$cmd");
        if (!array_key_exists('idtoken', $this->opts) || !$this->opts['idtoken']) {
            $this->log->error("no valid idtoken specified! please try re-login.");
            return 2;
        }
        switch ($cmd) {
            case "listAccounts":
                $error = $this->checkArgs(['apikey', 'idtoken']);
                if ($error) {
                    return $error;
                }
                $resp = $this->${"api"}->${"cmd"}(
                    $this->opts['idtoken']
                );
                    $this->log->debug(print_r($resp, true));
                return $resp;
            default:
                $this->log->error("Unknown api command");
                return 3;
        }
    }

    public function version()
    {
        $ver = "WsCli SDK " . $this->WsCliSDKVersion->getVersion();
        $this->log->info($ver);
        return $ver;
    }

    public function __call($methodCmd, $methodApi)
    {
        // $methodApi array is populated on our internal calls to
        // __call().
        $api = strtolower($this->opts['<api>']);
        $cmd = $this->opts['<cmd>'];
        $this->log->debug("__call method: " . $cmd);
        $this->log->debug("__call   args: " . implode(',', $methodApi));
        $this->log->debug("opts api->cmd: " . $api . "->" . $cmd);
        $methodApi = is_array($methodApi) && array_key_exists(0, $methodApi) ? $methodApi[0] : "";
        
        switch ($api) {
            case "account":
                return $this->handleAccount($api, $cmd, $methodApi, $methodCmd);
            case "session":
                return $this->handleSession($api, $cmd, $methodApi, $methodCmd);
            case "files":
                return $this->handleFiles($api, $cmd, $methodApi, $methodCmd);
            case "pgp":
                return $this->handlePgp($api, $cmd, $methodApi, $methodCmd);
            case "certs":
                return $this->handleCerts($api, $cmd, $methodApi, $methodCmd);
            case "integrator":
                return $this->handleIntegrator($api, $cmd, $methodApi, $methodCmd);
            default:
                return 3;
        }
    }

    private function updateConfig($yamlString)
    {
        if (!file_exists($this->configFilename)) {
            $this->log->error("Configuration file does not exist " . $this->configFilename);
            return null;
        }
        $yaml = new Parser();
        $y = $yaml->Parse($yamlString);
        if ($y === false) {
            $this->log->error("Failed to parse passed YAML string: " . $y);
            return null;
        }
        $yaml = new Parser();
        $conf = $yaml->Parse(file_get_contents($this->configFilename));
        if ($conf === false) {
            $this->log->error("Failed to parse YAML configuration file " . $this->configFilename);
            return null;
        }
        // NOTE: overwrite settings e.g. 'idtoken' if any.
        $conf['settings'] = array_replace($conf['settings'], $y);
        $contents = Yaml::dump($conf);
        if (!file_put_contents($this->configFilename, $contents)) {
            $this->log->error("Failed to update YAML configuration file with: " . print_r($conf, true));
            return null;
        }
        return 0;
    }

    private function getBodyParams()
    {
        $bodyParams = [
            'ApiKey' => array_key_exists('apikey', $this->opts) ? $this->opts['apikey'] : '',
            'ChResp' => array_key_exists('challenge', $this->opts) ? $this->opts['challenge'] : '',
            'Company' => array_key_exists('company', $this->opts) ? $this->opts['company'] : '',
            'Encrypted' => array_key_exists('encrypted', $this->opts) ? $this->opts['encrypted'] : '',
            'Name' => array_key_exists('name', $this->opts) ? $this->opts['name'] : '',
            'Phone' => array_key_exists('phone', $this->opts) ? $this->opts['phone'] : '',
            'WsUserId' => array_key_exists('wsuserid', $this->opts) ? $this->opts['wsuserid'] : '',
            'Certificate' => array_key_exists('certificate', $this->opts) ? $this->opts['certificate'] : '',
            'PrivateKey' => array_key_exists('privatekey', $this->opts) ? $this->opts['privatekey'] : '',
            'EncCertificate' => array_key_exists('enccertificate', $this->opts) ? $this->opts['enccertificate'] : '',
            'EncPrivateKey' => array_key_exists('encprivatekey', $this->opts) ? $this->opts['encprivatekey'] : '',
            'FileReference' => array_key_exists('filereference', $this->opts) ? $this->opts['filereference'] : '',
            'FileContents' => array_key_exists('filecontents', $this->opts) ? $this->opts['filecontents'] : '',
            'FileName' => array_key_exists('filename', $this->opts) ? $this->opts['filename'] : '',
            'Status' => array_key_exists('filestatus', $this->opts) ? $this->opts['filestatus'] : '',
            'startDate' => array_key_exists('startdate', $this->opts) ? $this->opts['startdate'] : '',
            'endDate' => array_key_exists('enddate', $this->opts) ? $this->opts['enddate'] : '',
            'PgpKey' => array_key_exists('pgpkeycontents', $this->opts) ? $this->opts['pgpkeycontents'] : '',
            'PgpKeyId' => array_key_exists('pgpkeyid', $this->opts) ? $this->opts['pgpkeyid'] : '',
            'PgpKeyPurpose' => array_key_exists('pgpkeypurpose', $this->opts) ? $this->opts['pgpkeypurpose'] : '',
            'FileContents' => array_key_exists('filecontents', $this->opts) ? $this->opts['filecontents'] : '',
            'Signatures' => array_key_exists('signatures', $this->opts) ? $this->opts['signatures'] : '',
            'FileType' => array_key_exists('filetype', $this->opts) ? $this->opts['filetype'] : ''
        ];
        if (array_key_exists('session', $this->opts) && $this->opts['session']) {
            $bodyParams['Session'] = $this->opts['session'];
            unset($bodyParams['session']);
        }
        if (array_key_exists('code', $this->opts) && $this->opts['code']) {
            $bodyParams['Code'] = $this->opts['code'];
            unset($bodyParams['code']);
        }
        if (array_key_exists('pincode', $this->opts) && $this->opts['pincode']) {
            $bodyParams['Code'] = $this->opts['pincode'];
            unset($bodyParams['code']);
        }
        if (array_key_exists('accesstoken', $this->opts) && $this->opts['accesstoken']) {
            $bodyParams['AccessToken'] = $this->opts['accesstoken'];
            unset($bodyParams['accesstoken']);
        }
        $apiKey = $bodyParams['ApiKey'];
        $bodyParams = array_filter($bodyParams);
        if ($apiKey == "0") {
            // array_filter() filters out keys with value "0"
            $bodyParams['ApiKey'] = "0";
        }
        $bodyParams = json_encode($bodyParams);
        $this->log->debug("body params: " . print_r($bodyParams, true));
        return $bodyParams;
    }

    private function getOptsFromConfig()
    {
        $conf = false;
        if (array_key_exists('config', $this->opts) && strlen($this->opts['config']) > 0) {
            $conf = $this->opts['config'];
        }
        $this->log->debug("config opt: " . $conf);
        $this->configFilename = '';
        if ($conf && file_exists($conf)) {
            if (substr(sprintf('%o', fileperms($conf)), -4) != "0600") {
                $msg = "invalid $conf file permissions, please set to 600";
                $this->log->error($msg);
                echo $msg . PHP_EOL;
                return 1;
            }
            $this->configFilename = $conf;
        }
        $home = getenv("HOME");
        $this->log->debug("HOME: ${home}");
        if ($home && !$conf && file_exists("${home}/.wscli/settings.yaml")) {
            $this->log->debug("fileperms for ${home}/.wscli/: " . substr(sprintf('%o', fileperms("${home}/.wscli")), -4));
            if (substr(sprintf('%o', fileperms("${home}/.wscli/")), -4) != "0700") {
                $msg = "ERROR: invalid ${home}/.wscli dir permissions, please set to 700";
                $this->log->error($msg);
                echo $msg . PHP_EOL;
                return 1;
            }
            $this->log->debug("fileperms for ${home}/.wscli/settings.yaml: " . substr(sprintf('%o', fileperms("${home}/.wscli/settings.yaml")), -4));
            if (substr(sprintf('%o', fileperms("${home}/.wscli/settings.yaml")), -4) != "0600") {
                $msg = "ERROR: invalid ${home}/.wscli/settings.yaml file permissions, pleaset set to 600";
                $this->log->error($msg);
                echo $msg . PHP_EOL;
                return 1;
            }
            $this->configFilename = "${home}/.wscli/settings.yaml";
        }
        if ($this->configFilename === '') {
            $this->log->info("No settings file found");
            return [];
        }
        if ($this->configFilename != '') {
            $this->log->debug("settings from: " . $this->configFilename);
            $yaml = new Parser();
            $configArgs = $yaml->Parse(file_get_contents($this->configFilename));
            if ($configArgs === false) {
                $this->log->error("Config file YAML parse error; " . $this->configFilename);
                return 1;
            }
            if ($configArgs['settings']) {
                $configArgs = $this->checkValidIdToken($configArgs);
                $this->log->debug("settings from file: " . print_r($configArgs['settings'], true));
                return $configArgs['settings'];
            }
        }
    }

    private function checkValidIdToken($configArgs)
    {
        if (array_key_exists('idtoken', $configArgs['settings']) &&
            array_key_exists('idtokenexpiry', $configArgs['settings']) &&
            array_key_exists('idtokenmode', $configArgs['settings']) &&
            strlen($configArgs['settings']['idtoken']) > 1 &&
            strlen($configArgs['settings']['idtokenexpiry']) > 1 &&
            strlen($configArgs['settings']['idtokenmode']) > 1) {
            if (strtotime($configArgs['settings']['idtokenexpiry']) < (time()+10) ||
                (array_key_exists('mode', $this->opts) &&
                 strlen($this->opts['mode']) > 1 &&
                 $configArgs['settings']['idtokenmode'] !== $this->opts['mode'])) {
                $this->log->info("Removing old token from config");
                $this->updateConfig("idtoken: \"\"");
                $this->updateConfig("idtokenexpiry: \"\"");
                $this->updateConfig("idtokenmode: \"\"");
                unset($configArgs['settings']['idtoken']);
                unset($configArgs['settings']['idtokenexpiry']);
                unset($configArgs['settings']['idtokenmode']);
            }
        }
        return $configArgs;
    }

    private function getOpts()
    {
        $this->opts = array_merge(array_filter($this->getOptsFromConfig()), array_filter($this->opts));
        if ((array_key_exists('apikey', $this->opts) && $this->opts['apikey'] === 0) ||
            !array_key_exists('apikey', $this->opts)) {
            $this->opts['apikey'] = "0";
        }
        $this->log->debug("merged opts: " . print_r($this->opts, true));
    }

    private function getChallenge()
    {
        // NOTE: initRegister and initLogin do the same thing
        if (!array_key_exists('email', $this->opts)) {
            $this->log->error("email not specified!");
        }
        if (!array_key_exists('mode', $this->opts)) {
            $this->log->error("mode not specified!");
        }
        if (array_key_exists('email', $this->opts) && array_key_exists('mode', $this->opts)) {
            $this->opts['challenge'] = $this->session->initLogin($this->opts['email'], $this->opts['mode'])['challenge'];
            $this->log->debug("Challenge: " . $this->opts['challenge']);
            return $this->opts['challenge'];
        }
        return null;
    }

    private function getEncryptedPassword()
    {
        if (!array_key_exists('environment', $this->opts)) {
            $this->log->error("Environment not specified!");
            return null;
        }
        if (!array_key_exists('password', $this->opts)) {
            $this->log->error("Password not specified!");
            return null;
        }
        if (!array_key_exists('challenge', $this->opts)) {
            $this->log->error("Challenge not specified!");
            return null;
        }
        $rsa = new \phpseclib\Crypt\RSA();
        if ($this->opts['environment'] == "test" ||
            $this->opts['environment'] == "testing") {
            $rsa->loadKey("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQ".
                          "EAkuSaoSZztGAIGDTY7RffpsBHJJT1k207UodOJbYF".
                          "hHAq0lWJnvMPLl5Q1DUUZdTGtTdL8Dsaj/Bo2+gSyk".
                          "MMR5QiKewvQsLfvqjwOO8JDItnhJl0lUqcPpdQV4M/".
                          "Ai3YNRjNcVy4a+pichqtSAWl9S1HV01MNeouk8PEr/".
                          "zoUasmgfO3mz6N6XTUtF/tIi8K2kBOsLAtqltihFSd".
                          "/zT8ifYZE9cZTJ09lUs7kMz1wxFIsiegaE1jUYV+VS".
                          "Lu3PJ97oKhQpqop8EnkBAoBl6rmdmFryBQIdakPIdd".
                          "4rO5Yg+to10n4u7Wij9ePIwWMfbqY4QoW5nXqMgFJQ".
                          "kIt4TGeQIDAQAB");
        }
        if ($this->opts['environment'] == "production") {
            $rsa->loadkey("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQ".
                          "EA7wx4l7P3eLsaEyK7ZRMEg5urEHwaEoY9LjkYcpMw".
                          "9gmPIi3RoGjQX7HzPad2D7ES2yIGdmyxjN8R2LyFa8".
                          "keEE+VY3ISYzP2cOjd/zDkX01yjDXQLRxntXbtqIyp".
                          "GQAzmZbCyIB226ZKEE+ldh6MYyM41YWYikfocYssFE".
                          "jY7fpPGeUg4FOmHmyWIZeMkXYovskoi1jZ1Ay1qn95".
                          "XlpA/Ptru2efro4T1xksv4WBBrj8bMNwdDpf4oyzH2".
                          "PKYkn3/KlNTBCHlAmzP0jd4pIaN0tAf2m8TcNq7kuB".
                          "zyfs8AcCUj870p8SEiko0PMx6K+zVsTVWsxfUX+/+k".
                          "mapmp/AwIDAQAB");
        }
        //$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_OAEP);
        $rsa->setEncryptionMode(1);
        $this->opts['encrypted'] = base64_encode($rsa->encrypt($this->opts['password']."||".substr($this->opts['challenge'], 45, 13)));
        return $this->opts['encrypted'];
    }
}
