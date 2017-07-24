<?php
/**
 * This file is part of the IsecureFi.WsCliPhpSdk
 */
namespace IsecureFi\WsCliPhpSdk;

use \Monolog\Formatter\LineFormatter;
use \Monolog\Handler\StreamHandler;
use \Monolog\Logger;
#use \Firebase\JWT\JWT;
use \Swagger\Client\ApiClient;
use \Swagger\Client\Api\AccountApi;
use \Swagger\Client\Api\SessionApi;
use \Swagger\Client\Api\PgpApi;
use \Swagger\Client\Api\FilesApi;
use \Swagger\Client\Api\CertsApi;

// Extends swagger generated SDK
class WsCli
{
    private $config_filename = '';

    // APIs
    private $account = '';
    private $session = '';
    private $certs = '';
    private $gpg = '';
    private $files = '';
    
    public function __construct($args)
    {
        $this->log = new Logger('WsCliPhpSdk');
        $stream = new StreamHandler('wscli_sdk.log', Logger::DEBUG);
        $stream->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n", 'Y-m-d H:i:s', true, true));
        $this->log->pushHandler($stream);

        $this->opts = $args;
        $this->log->debug("opts before merge: " . print_r($this->opts, true));
        $this->getOpts();

        $config = new \Swagger\Client\Configuration();
        $this->log->debug("x-api-key: " . $this->opts['apikey']);
        $config->setApiKey("x-api-key", $this->opts['apikey']);
        $apiClient = new \Swagger\Client\ApiClient($config);

        $this->account = new \Swagger\Client\Api\AccountApi($apiClient);
        $this->session = new \Swagger\Client\Api\SessionApi($apiClient);
        $this->files = new \Swagger\Client\Api\FilesApi($apiClient);
        $this->gpg = new \Swagger\Client\Api\PgpApi($apiClient);
        $this->certs = new \Swagger\Client\Api\CertsApi($apiClient);
    }

    public function __call($name, $args)
    {
        $apiName = strtolower($this->opts['<api>']);
        $cmd = $this->opts['<cmd>'];
        $this->log->debug("callname: " . implode(',',$args) . "->" . $name);
        $this->log->debug("from api: " . $apiName . "->" . $cmd);

        switch ($apiName) {
        case "account":
            if (!array_key_exists('email', $this->opts)) {
                $this->log->error("email not specified!");
                $error = 1;
            }
            if (!array_key_exists('mode', $this->opts)) {
                $this->log->error("mode not specified!");
                $error = 1;
            }
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
                $resp = $this->${"apiName"}->${"cmd"}($this->getBodyParams(),
                                                      $this->opts['email'],
                                                      $this->opts['mode']);
                $this->log->debug(print_r($resp, true));
                return $resp;
            case "verifyemail":
                while (strlen($this->opts['code']) != 6) {
                    $this->opts['code'] = readline("Give EMail verification code: ");
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"apiName"}->${"cmd"}($this->getBodyParams(),
                                                      $this->opts['email'],
                                                      $this->opts['mode']);
                $this->log->debug(print_r($resp, true));
                if ($name != $this->opts['<cmd>']) {
                    $this->log->debug("Callbacking to " . implode(',',$args) . "->" . $name);
                    $this->opts['<cmd>'] = $name;
                    $this->opts['<api>'] = $args[0] ? $args[0] : "n/a";;
                    return $this->__call($cmd, [$apiName]);
                }
            case "verifyEmail":
                while (strlen((string)$this->opts['code']) != 6) {
                    $this->opts['code'] = readline("Give email verification code: ");
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"apiName"}->${"cmd"}($this->getBodyParams(),
                                                      $this->opts['email'],
                                                      $this->opts['mode']);
                $this->log->debug(print_r($resp, true));
                if ($name != $this->opts['<cmd>']) {
                    $this->log->debug("Callbacking to " . implode(',',$args) . "->" . $name);
                    $this->opts['<cmd>'] = $name;
                    $this->opts['<api>'] = $args[0] ? $args[0] : "n/a";;
                    return $this->__call($cmd, [$apiName]);
                }
                return $resp;
                break;
            case "verifyPhone":
                while (strlen((string)$this->opts['code']) != 6) {
                    $this->opts['code'] = readline("Give phone registration code: ");
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"apiName"}->${"cmd"}($this->getBodyParams(),
                                                      $this->opts['email'],
                                                      $this->opts['mode'],
                                                      $this->opts['phone']);
                $this->log->debug(print_r($resp, true));
                if ($name != $this->opts['<cmd>']) {
                    $this->log->debug("Callbacking to " . implode(',',$args) . "->" . $name);
                    $this->opts['<cmd>'] = $name;
                    $this->opts['<api>'] = $args[0] ? $args[0] : "n/a";;
                    return $this->__call($cmd, [$apiName]);
                }
                return $resp;
            case "initpasswordreset":
                if ($error) {
                    return $error;
                }
                break;
            case "passwordreset":
                if ($error) {
                    return $error;
                }
                break;
            default:
                $this->log->error("Unknown api command");
                break;
            }
            break;
        case "session":
            switch ($cmd) {
            case "loginmfa":
            case "login":
                $error = false;
                if (!array_key_exists('email', $this->opts)) {
                    $this->log->error("email not specified!");
                    $error = 1;
                }
                if (!array_key_exists('mode', $this->opts)) {
                    $this->log->error("mode not specified!");
                    $error = 1;
                }
                if ($error) {
                    return $error;
                }
                if (!$this->getChallenge()) {
                    $this->log->error("Could not get challenge.");
                    $error = 1;
                }
                if (!array_key_exists('password',$this->opts)) {
                    $this->opts['password'] = '';
                    while (strlen((string)$this->opts['password']) < 20) {
                        $this->opts['password'] = readline("Give password (at least 20 chars, upper/lower letters and special chars): ");
                    }
                }
                if (!$this->getEncryptedPassword()) {
                    $this->log->error("Could not get encrypted password.");
                    $error = 1;
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"apiName"}->${"cmd"}($this->getBodyParams(),
                                                      $this->opts['email'],
                                                      $this->opts['mode']);
                $this->opts['code'] = ''; // Reset code if any
                $this->log->debug(print_r($resp, true));
                if ($resp['response_code'] == "00") {
                    if (strstr($resp['response_text'], "Verify phone number")) {
                        $this->opts['<api>'] = "account";
                        $this->opts['<cmd>'] = "verifyphone";
                        return $this->__call("login", [$apiName]);
                    }
                    if (strstr($resp['response_text'], "Give SMS code")) {
                        while (strlen((string)$this->opts['code']) != 6) {
                            $this->opts['code'] = readline("Give SMS code: ");
                        } 
                        $this->opts['session'] = $resp['session'];
                        $this->opts['<cmd>'] = "loginmfa";
                        return $this->__call("login", [$apiName]);
                    }
                    if (strstr($resp['response_text'], "Verify email address")) {
                        $this->opts['accesstoken'] = $resp['access_token'];
                        $this->opts['<api>'] = "account";
                        $this->opts['<cmd>'] = "verifyemail";
                        return $this->__call("login", [$apiName]);
                    }
                    if ($resp['response_text'] == "Login OK") {
                        $this->updateConfig("idtoken: " . $resp['id_token']);
                        # NOTE: See https://github.com/firebase/php-jwt
                        #$this->updateConfig("idtokenexpiry: " . JWT::decode($resp['id_token], "", "")['']
                        $exp = date("Y-m-d H:i:s", time()+$resp['expires_in']-10);
                        $this->log->debug("idtokenexpiry: " . $exp);
                        if ($exp === FALSE) {
                            $this->log->error("Could not form idtoken expiry date. Setting to +3300s");
                            $exp = time() + 3300;
                        }
                        $this->updateConfig("idtokenexpiry: \"" . $exp . "\"");
                    } 
                }
                return $resp;
            default:
                $this->log->error("Unknown api command");
                break;
            }
            break;
        case "files":
            switch ($cmd) {
            case "listFiles":
                $error = 0;
                if (!array_key_exists('idtoken', $this->opts)) {
                    $this->log->error("no valid idtoken specified! please try re-login.");
                    $error = 2;
                }
                if (!array_key_exists('bank', $this->opts)) {
                    $this->log->error("bank not specified!");
                    $error = 1;
                }
                if (!array_key_exists('status', $this->opts)) {
                    $this->log->error("status not specified!");
                    $error = 1;
                }
                if (!array_key_exists('filetype', $this->opts)) {
                    $this->log->error("filetype not specified!");
                    $error = 1;
                }
                if ($error) {
                    return $error;
                }
                $resp = $this->${"apiName"}->listFiles($this->opts['idtoken'],
                                                       $this->opts['bank'],
                                                       $this->opts['status'],
                                                       $this->opts['filetype']);
                $this->log->debug(print_r($resp, true));
                return $resp;
            case "downloadFile":
                $this->log->error("Unimplemented API command");
                return 3;
            case "downloadFiles":
                // TODO: Use downloadFile to download all files one-by-one
                $this->log->error("Unimplemented API command");
                return 3;
            default:
                $this->log->error("Unknown api command");
                return 3;
            }
            break;
        case "pgp":
            break;
        case "certs":
            break;
        default:
            break;
        }
    }

    private function updateConfig($yaml_string)
    {
        if (!file_exists($this->config_filename)) {
            return null;
        }
        $y = yaml_parse($yaml_string);
        if ($y === FALSE) {
            $this->log->error("Failed to parse passed YAML string: " . $y);
            return null;
        }
        $conf = yaml_parse(file_get_contents($this->config_filename));
        if ($conf === FALSE) {
            $this->log->error("Failed to parse YAML configuration file " . $this->config_filename);
            return null;
        }
        // NOTE: overwrite settings e.g. 'idtoken' if any.
        $conf['settings'] = array_replace($conf['settings'], $y);
        if (!yaml_emit_file($this->config_filename, $conf, YAML_UTF8_ENCODING)) {
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
            'Phone' => array_key_exists('phone', $this->opts) ? $this->opts['phone'] : ''
        ];
        if (array_key_exists('session', $this->opts) && $this->opts['session']) {
            $bodyParams['Session'] = $this->opts['session'];
            unset($bodyParams['session']);
        }
        if (array_key_exists('code', $this->opts) && $this->opts['code']) {
            $bodyParams['Code'] = $this->opts['code'];
            unset($bodyParams['code']);
        }
        if (array_key_exists('accesstoken', $this->opts) && $this->opts['accesstoken']) {
            $bodyParams['AccessToken'] = $this->opts['accesstoken'];
            unset($bodyParams['accesstoken']);
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
        $this->log->debug("config opt: " . $this->opts['config']);
        $this->config_filename = '';
        if ($conf && file_exists($conf)) {
            if (substr(sprintf('%o', fileperms($conf)), -4) != "0600") {
                $msg = "invalid $conf file permissions, please set to 600".
                $this->log->error($msg);
                echo $msg . PHP_EOL;
                exit(1);
            }
            $this->config_filename = $conf;
        }
        $HOME = getenv("HOME");
        $this->log->debug("HOME: ${HOME}");
        if ($HOME && !$conf && file_exists("${HOME}/.wscli/settings.yaml")) {
            $this->log->debug("fileperms for ${HOME}/.wscli/: " . substr(sprintf('%o', fileperms("${HOME}/.wscli")), -4));
            if (substr(sprintf('%o', fileperms("${HOME}/.wscli/")), -4) != "0700") {
                $msg = "ERROR: invalid ${HOME}/.wscli dir permissions, please set to 700";
                $this->log->error($msg);
                echo $msg . PHP_EOL;
                exit(1);
            }
            $this->log->debug("fileperms for ${HOME}/.wscli/settings.yaml: " . substr(sprintf('%o', fileperms("${HOME}/.wscli/settings.yaml")), -4));
            if (substr(sprintf('%o', fileperms("${HOME}/.wscli/settings.yaml")), -4) != "0600") {
                $msg = "ERROR: invalid ${HOME}/.wscli/settings.yaml file permissions, pleaset set to 600";
                $this->log->error($msg);
                echo $msg . PHP_EOL;
                exit(1);
            }
            $this->config_filename = "${HOME}/.wscli/settings.yaml";
        }
        if ($this->config_filename === '') {
            $this->log->info("No settings file found");
            return [];
        }
        if ($this->config_filename != '') {
            $this->log->debug("settings from: " . $this->config_filename);
            $config_args = yaml_parse(file_get_contents($this->config_filename));
            if ($config_args === FALSE) {
                $this->log->error("Config file YAML parse error; " . $this->config_filename);

                return -1;
            }
            if ($config_args['settings']) {
                // Check idtoken expiry
                if (array_key_exists('idtoken', $config_args['settings']) &&
                    array_key_exists('idtokenexpiry', $config_args['settings']) &&
                    strlen($config_args['settings']['idtoken']) > 1 &&
                    strlen($config_args['settings']['idtokenexpiry'] > 1)) {
                    if (strtotime($config_args['settings']['idtokenexpiry']) < time()) {
                        $this->log->info("Removing old token from config");
                        $this->updateConfig("idtoken: \"\"");
                        $this->updateConfig("idtokenexpiry: \"\"");
                    }
                    if (strlen($config_args['settings']['idtoken']) <= 0) {
                        $this->log->debug("Removing idtoken from settings; " . $config_args['settings']['idtoken']);
                        unset($config_args['settings']['idtoken']);
                        $this->log->debug("Removing idtokenexpiry from settings; " . $config_args['settings']['idtokenexpiry']);
                        unset($config_args['settings']['idtokenexpiry']);
                    }
                }
                $this->log->debug("settings from file: " . print_r($config_args['settings'], true));
                return $config_args['settings'];
            }
        }
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
