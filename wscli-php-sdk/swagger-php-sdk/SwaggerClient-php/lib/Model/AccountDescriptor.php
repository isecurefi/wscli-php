<?php
/**
 * AccountDescriptor
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
 * The API provides normal file based access to all common banks in Finland via WebServices channel on the service side, including certificate enrollment (PKI) with automatic renewals. Additionally, user account management, password recovery, and SMS based 2nd or Multi Factor Authentication (MFA) are provided (AWS Cognito Your User Pool). Access is secured with HTTPS/TLS (AWS API Gateway), using email address as username and password. On login, password is RSA encrypted along with dynamic parameters fetched from the service (username specific challenge response). Every integrator has own API Key and every user account belongs to one integrator. Certificate sharing between accounts is possible under the same integrator API Key, meaning that the enrolled bank connection certificates are shared. Under the same email address / username, both *admin* and *data* accounts must be used as they have separate passwords and differing functions.
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
 * AccountDescriptor Class Doc Comment
 *
 * @category    Class
 * @package     Swagger\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class AccountDescriptor implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'AccountDescriptor';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'admin_mode' => 'string',
        'certs' => '\Swagger\Client\Model\CertDescriptor[]',
        'data_mode' => 'string',
        'email' => 'string',
        'export' => 'string',
        'name' => 'string',
        'phone' => 'string'
    ];

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = [
        'admin_mode' => 'AdminMode',
        'certs' => 'Certs',
        'data_mode' => 'DataMode',
        'email' => 'Email',
        'export' => 'Export',
        'name' => 'Name',
        'phone' => 'Phone'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'admin_mode' => 'setAdminMode',
        'certs' => 'setCerts',
        'data_mode' => 'setDataMode',
        'email' => 'setEmail',
        'export' => 'setExport',
        'name' => 'setName',
        'phone' => 'setPhone'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'admin_mode' => 'getAdminMode',
        'certs' => 'getCerts',
        'data_mode' => 'getDataMode',
        'email' => 'getEmail',
        'export' => 'getExport',
        'name' => 'getName',
        'phone' => 'getPhone'
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

    const ADMIN_MODE_REGISTERED = 'registered';
    const ADMIN_MODE_UNREGISTERED = 'unregistered';
    const DATA_MODE_REGISTERED = 'registered';
    const DATA_MODE_UNREGISTERED = 'unregistered';
    const EXPORT_DISABLED = 'disabled';
    const EXPORT_ALLOWED = 'allowed';
    

    
    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public function getAdminModeAllowableValues()
    {
        return [
            self::ADMIN_MODE_REGISTERED,
            self::ADMIN_MODE_UNREGISTERED,
        ];
    }
    
    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public function getDataModeAllowableValues()
    {
        return [
            self::DATA_MODE_REGISTERED,
            self::DATA_MODE_UNREGISTERED,
        ];
    }
    
    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public function getExportAllowableValues()
    {
        return [
            self::EXPORT_DISABLED,
            self::EXPORT_ALLOWED,
        ];
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
        $this->container['admin_mode'] = isset($data['admin_mode']) ? $data['admin_mode'] : null;
        $this->container['certs'] = isset($data['certs']) ? $data['certs'] : null;
        $this->container['data_mode'] = isset($data['data_mode']) ? $data['data_mode'] : null;
        $this->container['email'] = isset($data['email']) ? $data['email'] : null;
        $this->container['export'] = isset($data['export']) ? $data['export'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['phone'] = isset($data['phone']) ? $data['phone'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];

        $allowed_values = ["registered", "unregistered"];
        if (!in_array($this->container['admin_mode'], $allowed_values)) {
            $invalid_properties[] = "invalid value for 'admin_mode', must be one of 'registered', 'unregistered'.";
        }

        if ($this->container['certs'] === null) {
            $invalid_properties[] = "'certs' can't be null";
        }
        $allowed_values = ["registered", "unregistered"];
        if (!in_array($this->container['data_mode'], $allowed_values)) {
            $invalid_properties[] = "invalid value for 'data_mode', must be one of 'registered', 'unregistered'.";
        }

        if ($this->container['email'] === null) {
            $invalid_properties[] = "'email' can't be null";
        }
        if ($this->container['export'] === null) {
            $invalid_properties[] = "'export' can't be null";
        }
        $allowed_values = ["disabled", "allowed"];
        if (!in_array($this->container['export'], $allowed_values)) {
            $invalid_properties[] = "invalid value for 'export', must be one of 'disabled', 'allowed'.";
        }

        if ($this->container['name'] === null) {
            $invalid_properties[] = "'name' can't be null";
        }
        if ($this->container['phone'] === null) {
            $invalid_properties[] = "'phone' can't be null";
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

        $allowed_values = ["registered", "unregistered"];
        if (!in_array($this->container['admin_mode'], $allowed_values)) {
            return false;
        }
        if ($this->container['certs'] === null) {
            return false;
        }
        $allowed_values = ["registered", "unregistered"];
        if (!in_array($this->container['data_mode'], $allowed_values)) {
            return false;
        }
        if ($this->container['email'] === null) {
            return false;
        }
        if ($this->container['export'] === null) {
            return false;
        }
        $allowed_values = ["disabled", "allowed"];
        if (!in_array($this->container['export'], $allowed_values)) {
            return false;
        }
        if ($this->container['name'] === null) {
            return false;
        }
        if ($this->container['phone'] === null) {
            return false;
        }
        return true;
    }


    /**
     * Gets admin_mode
     * @return string
     */
    public function getAdminMode()
    {
        return $this->container['admin_mode'];
    }

    /**
     * Sets admin_mode
     * @param string $admin_mode `admin` mode status
     * @return $this
     */
    public function setAdminMode($admin_mode)
    {
        $allowed_values = array('registered', 'unregistered');
        if (!is_null($admin_mode) && (!in_array($admin_mode, $allowed_values))) {
            throw new \InvalidArgumentException("Invalid value for 'admin_mode', must be one of 'registered', 'unregistered'");
        }
        $this->container['admin_mode'] = $admin_mode;

        return $this;
    }

    /**
     * Gets certs
     * @return \Swagger\Client\Model\CertDescriptor[]
     */
    public function getCerts()
    {
        return $this->container['certs'];
    }

    /**
     * Sets certs
     * @param \Swagger\Client\Model\CertDescriptor[] $certs
     * @return $this
     */
    public function setCerts($certs)
    {
        $this->container['certs'] = $certs;

        return $this;
    }

    /**
     * Gets data_mode
     * @return string
     */
    public function getDataMode()
    {
        return $this->container['data_mode'];
    }

    /**
     * Sets data_mode
     * @param string $data_mode `data` mode status
     * @return $this
     */
    public function setDataMode($data_mode)
    {
        $allowed_values = array('registered', 'unregistered');
        if (!is_null($data_mode) && (!in_array($data_mode, $allowed_values))) {
            throw new \InvalidArgumentException("Invalid value for 'data_mode', must be one of 'registered', 'unregistered'");
        }
        $this->container['data_mode'] = $data_mode;

        return $this;
    }

    /**
     * Gets email
     * @return string
     */
    public function getEmail()
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     * @param string $email Email address as the account username
     * @return $this
     */
    public function setEmail($email)
    {
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets export
     * @return string
     */
    public function getExport()
    {
        return $this->container['export'];
    }

    /**
     * Sets export
     * @param string $export Status for certificate and private key export allowance. See ConfigCerts.
     * @return $this
     */
    public function setExport($export)
    {
        $allowed_values = array('disabled', 'allowed');
        if ((!in_array($export, $allowed_values))) {
            throw new \InvalidArgumentException("Invalid value for 'export', must be one of 'disabled', 'allowed'");
        }
        $this->container['export'] = $export;

        return $this;
    }

    /**
     * Gets name
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     * @param string $name Full name of registrant
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets phone
     * @return string
     */
    public function getPhone()
    {
        return $this->container['phone'];
    }

    /**
     * Sets phone
     * @param string $phone Phone number with country code and `+` in front
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->container['phone'] = $phone;

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


