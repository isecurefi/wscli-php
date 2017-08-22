<?php

namespace

{
    $n = PHP_EOL;

    set_error_handler(
        function ($code, $message, $file, $line) use ($n) {
            if ($code & error_reporting()) {
                echo "$n{$n}Error: $message$n$n";
                exit(1);
            }
        }
    );

    echo "wscli installer$n";
    echo "===============$n$n";

    echo "Environment Check$n";
    echo "-----------------$n$n";

    echo "\"-\" indicates success.$n";
    echo "\"*\" indicates error.$n$n";

    // check version
    check(
        'You have a supported version of PHP (>= 5.6.0).',
        'You need PHP 5.6.0 or greater.',
        function () {
            return version_compare(PHP_VERSION, '5.6.0', '>=');
        }
    );

    // check phar extension
    check(
        'You have the "phar" extension installed.',
        'You need to have the "phar" extension installed.',
        function () {
            return extension_loaded('phar');
        }
    );

    // check phar extension version
    check(
        'You have a supported version of the "phar" extension.',
        'You need a newer version of the "phar" extension (>=2.0).',
        function () {
            $phar = new ReflectionExtension('phar');

            return version_compare($phar->getVersion(), '2.0', '>=');
        }
    );

    // check allow url open setting
    check(
        'The "allow_url_fopen" setting is on.',
        'The "allow_url_fopen" setting needs to be on.',
        function () {
            return (true == ini_get('allow_url_fopen'));
        }
    );

    // check apc cli caching
    if (!defined('HHVM_VERSION') && !extension_loaded('apcu') && extension_loaded('apc')) {
        check(
            'The "apc.enable_cli" setting is off.',
            'Notice: The "apc.enable_cli" is on and may cause problems with Phars.',
            function () {
                return (false == ini_get('apc.enable_cli'));
            },
            false
        );
    }

    echo "{$n}Everything seems good!$n$n";

    echo "Download$n";
    echo "--------$n$n";
    $base_url = 'https://isecurefi.github.io/wscli-php/';

    echo " - Downloading wscli.phar...$n";
    $res = file_put_contents("wscli.phar", file_get_contents($base_url . "wscli.phar"));
    if ($res === FALSE || $res <= 0) {
        echo " x Downloading and writing wscli.phar failed.$n";
        exit(1);
    }
    
    echo " - Downloading wscli.phar.pubkey..$n";
    $res = file_put_contents("wscli.phar.pubkey", file_get_contents($base_url . "wscli.phar.pubkey"));
    if ($res === FALSE || $res <= 0) {
        echo " x Downloading and writing wscli.phar.pubkey failed.$n";
        exit(1);
    }

    echo " - Downloading wscli.phar.version..$n";
    $res = file_put_contents("wscli.phar.version", file_get_contents($base_url . "wscli.phar.version"));
    if ($res === FALSE || $res <= 0) {
        echo " x Downloading and writing wscli.phar.pubkey failed.$n";
        exit(1);
    }

    echo " - Checking file checksum...$n";
    $sha_calculated = trim(hash("sha1", file_get_contents("wscli.phar")));
    $sha_version = trim(file_get_contents("wscli.phar.version"));
    if ($sha_calculated !== $sha_version) {
        unlink("wscli.phar");
        unlink("wscli.phar.version");
        unlink("wscli.phar.pubkey");
        echo " x The download was corrupted ($sha_calculated != $sha_version).$n";
        exit(1);
    }

    echo " - Checking if valid Phar...$n";

    try {
        new Phar("wscli.phar");
    } catch (Exception $e) {
        echo " x The Phar is not valid.$n$n";

        throw $e;
    }

    echo " - Making wscli executable...$n";

    @chmod("wscli.phar", 0755);

    echo "{$n}";
    echo "NOTE:{$n}";
    echo "    wscli.phar installed on local directory. You may want to copy {$n}"
        ."    wscli.phar and wscli.phar.pubkey into e.g. /usr/local/bin/ as {$n}"
        ."    wscli and wscli.pubkey. Sudo may be needed.{$n}";

    /**
     * Checks a condition, outputs a message, and exits if failed.
     *
     * @param string   $success   The success message.
     * @param string   $failure   The failure message.
     * @param callable $condition The condition to check.
     * @param boolean  $exit      Exit on failure?
     */
    function check($success, $failure, $condition, $exit = true)
    {
        global $n;

        if ($condition()) {
           echo ' - ', $success, $n;
        } else {
            echo ' * ', $failure, $n;

            if ($exit) {
                exit(1);
            }
        }
    }
}
