<?php
/**
 * CertDescriptor
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swaagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * ISECure WS Channel API
 *
 * The API provides secure file exchange with all common banks in Finland via *SEPA WebServices* channel on the API side towards the banks, including certificate enrollment (PKI) with automatic renewals.  The API specification in OpenAPI v2 format can be found on GitHub [isecurefi/wsapi-v2](https://github.com/isecurefi/wsapi-v2). Command line CLI and beefed-up PHP SDK are also available on GitHub [isecurefi/wscli-php](https://github.com/isecurefi/wscli-php).  API provides simple role based access control (RBAC) and user account management, password recovery, and SMS based Multi Factor Authentication based on AWS Cognito Your User Pool managed service.  *NOTE: The API endpoint for production is the same as for test, but without `test.` in the URL. Production and test APIs are deployd on separate AWS accounts*.  *NOTE: The API is run on AWS API Gateway and with AWS Lambda backend. When Lambda functions are cold, there is a small delay in response time. Additionally, banks have considerable delays in their processings, especially with certificate enrollments.*  ### Service enrollment  Every integrator (partner) has own *API Key* and every user account belongs to one integrator. *API Key* is bound with service subscription. In other words, enrolling fresh *API Key* requires service agreement before file transfers are allowed on production accounts.  If user registers with `0` *API Key* (i.e. no *API Key*) she gets a fresh *API Key* and becomes the *API Key* owner. The *API Key* owner account can list all users under the same *API Key*, see the *Integrator API*. Integrators (partners) registers their own *API Key* owner accounts and use it to register their client accounts.  *NOTE: API call rate limits are set and tracked per API Key by AWS API Gateway*.  ### Account management  A user (email address) can register either *admin* or *data* or both roles. The role in the API is referred to as *mode*. Both modes have separate passwords and provide differing capabilities for the user.  Login always requires account mode parameter in addition to user's email address and password. *Admin* mode login always requires an additional SMS one-time-password (MFA), whilst with *data* mode password is enough (suitable for automation). *Admin* mode is used to configure the account (e.g. adding PGP keys and sharing certs) and *data* mode to exchange files. Listing files is allowed on both modes.  *NOTE: Integrator (partner) registers her customers by using her API Key from the API Key owner account.*  ### Bank certificate enrollment  The *SEPA WebServices* connection to the bank requires enrolling PKI certificate with the bank. The *Admin* mode can enroll certificates for different banks, but only one certificate per bank. The corresponding private key is stored encrypted with AWS KMS service.  ### Bank certificate sharing  It is possible to share the same bank certificate with multiple accounts. Certificate sharing between accounts can be configured when accounts have the same API Key. Account that holds the certificate can share/unshare it with another account (*admin* mode operation). Note that only the account that has the certificate can PGP export the certificate and corresponding private key. This allows creating e.g. one *admin* mode only account and multiple *data* mode only accounts, where the *admin* account shares its certificates with other *data* accounts.  An account can never have multiple certificates per bank, be it shared or account's enrolled certificate. This is because the API requires identification of the bank, but not the certificate and private key pair.  ### Access security  Access is secured with TLS on Amazon Web Services (AWS) API Gateway. Inside TLS, secure sessions are established by using email address as username and by RSA encrypting password along with dynamic username specific parameters fetched from the API with `InitRegister` or `InitLogin` API commands (challenge response).  Successful login provides a session token (AWS Cognito User Pool). Authorization happens with the session token (`Authorization`) and *API Key* (`x-api-key`) headers.  Administrative actions require SMS based MFA authentication (see *admin* mode). User account management is handled with AWS Cognito User Pools and each user (email) has separate *admin* and/or *data* mode (role) accounts sharing the same API account data.
 *
 * OpenAPI spec version: v2.4.0
 * Contact: dan.forsberg@isecure.fi
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Model;

use \ArrayAccess;

/**
 * CertDescriptor Class Doc Comment
 *
 * @category    Class
 * @package     Swagger\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class CertDescriptor implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'CertDescriptor';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'cert_name' => 'string',
        'expires' => 'string',
        'issuer' => 'string',
        'pem' => 'string',
        'serial' => 'string',
        'subject' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerFormats = [
        'cert_name' => null,
        'expires' => null,
        'issuer' => null,
        'pem' => null,
        'serial' => null,
        'subject' => null
    ];

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = [
        'cert_name' => 'CertName',
        'expires' => 'Expires',
        'issuer' => 'Issuer',
        'pem' => 'PEM',
        'serial' => 'Serial',
        'subject' => 'Subject'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'cert_name' => 'setCertName',
        'expires' => 'setExpires',
        'issuer' => 'setIssuer',
        'pem' => 'setPem',
        'serial' => 'setSerial',
        'subject' => 'setSubject'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'cert_name' => 'getCertName',
        'expires' => 'getExpires',
        'issuer' => 'getIssuer',
        'pem' => 'getPem',
        'serial' => 'getSerial',
        'subject' => 'getSubject'
    ];

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    public static function setters()
    {
        return self::$setters;
    }

    public static function getters()
    {
        return self::$getters;
    }

    

    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     * @param mixed[] $data Associated array of property values initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['cert_name'] = isset($data['cert_name']) ? $data['cert_name'] : null;
        $this->container['expires'] = isset($data['expires']) ? $data['expires'] : null;
        $this->container['issuer'] = isset($data['issuer']) ? $data['issuer'] : null;
        $this->container['pem'] = isset($data['pem']) ? $data['pem'] : null;
        $this->container['serial'] = isset($data['serial']) ? $data['serial'] : null;
        $this->container['subject'] = isset($data['subject']) ? $data['subject'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];

        if ($this->container['cert_name'] === null) {
            $invalid_properties[] = "'cert_name' can't be null";
        }
        if ($this->container['expires'] === null) {
            $invalid_properties[] = "'expires' can't be null";
        }
        if ($this->container['issuer'] === null) {
            $invalid_properties[] = "'issuer' can't be null";
        }
        if ($this->container['pem'] === null) {
            $invalid_properties[] = "'pem' can't be null";
        }
        if ($this->container['serial'] === null) {
            $invalid_properties[] = "'serial' can't be null";
        }
        if ($this->container['subject'] === null) {
            $invalid_properties[] = "'subject' can't be null";
        }
        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {

        if ($this->container['cert_name'] === null) {
            return false;
        }
        if ($this->container['expires'] === null) {
            return false;
        }
        if ($this->container['issuer'] === null) {
            return false;
        }
        if ($this->container['pem'] === null) {
            return false;
        }
        if ($this->container['serial'] === null) {
            return false;
        }
        if ($this->container['subject'] === null) {
            return false;
        }
        return true;
    }


    /**
     * Gets cert_name
     * @return string
     */
    public function getCertName()
    {
        return $this->container['cert_name'];
    }

    /**
     * Sets cert_name
     * @param string $cert_name Certificate common name
     * @return $this
     */
    public function setCertName($cert_name)
    {
        $this->container['cert_name'] = $cert_name;

        return $this;
    }

    /**
     * Gets expires
     * @return string
     */
    public function getExpires()
    {
        return $this->container['expires'];
    }

    /**
     * Sets expires
     * @param string $expires Date of expiry
     * @return $this
     */
    public function setExpires($expires)
    {
        $this->container['expires'] = $expires;

        return $this;
    }

    /**
     * Gets issuer
     * @return string
     */
    public function getIssuer()
    {
        return $this->container['issuer'];
    }

    /**
     * Sets issuer
     * @param string $issuer Certificate issuer
     * @return $this
     */
    public function setIssuer($issuer)
    {
        $this->container['issuer'] = $issuer;

        return $this;
    }

    /**
     * Gets pem
     * @return string
     */
    public function getPem()
    {
        return $this->container['pem'];
    }

    /**
     * Sets pem
     * @param string $pem Certificate in PEM format
     * @return $this
     */
    public function setPem($pem)
    {
        $this->container['pem'] = $pem;

        return $this;
    }

    /**
     * Gets serial
     * @return string
     */
    public function getSerial()
    {
        return $this->container['serial'];
    }

    /**
     * Sets serial
     * @param string $serial Certificate serial number
     * @return $this
     */
    public function setSerial($serial)
    {
        $this->container['serial'] = $serial;

        return $this;
    }

    /**
     * Gets subject
     * @return string
     */
    public function getSubject()
    {
        return $this->container['subject'];
    }

    /**
     * Sets subject
     * @param string $subject Certificate subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->container['subject'] = $subject;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     * @param  integer $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     * @param  integer $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($this));
    }
}


