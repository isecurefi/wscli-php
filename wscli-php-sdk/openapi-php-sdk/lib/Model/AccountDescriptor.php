<?php
/**
 * AccountDescriptor
 *
 * PHP version 8.1
 *
 * @category Class
 * @package  IsecureFi\WsApi\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * ISECure WS Channel API
 *
 * The API provides secure file exchange with all common banks in Finland via *SEPA WebServices* channel on the API side towards the banks, including certificate enrollment (PKI) with automatic renewals.  The API specification in OpenAPI v2 format can be found on GitHub [isecurefi/wsapi-v2](https://github.com/isecurefi/wsapi-v2). Command line CLI and beefed-up PHP SDK are also available on GitHub [isecurefi/wscli-php](https://github.com/isecurefi/wscli-php).  ``` % export SESSION=~/.wscli/settings.yaml % wscli session login -c $SESSION % export APIKEY=`yq -r .settings.apikey $SESSION` % export IDTOKEN=`yq -r .settings.idtoken $SESSION` % % curl -H Authorization:$IDTOKEN \\         -H x-api-key:$APIKEY \\         https://ws-api.isecure.fi/v2/files/danskebank ```  API provides simple role based access control (RBAC) and user account management, password recovery, and SMS based Multi Factor Authentication based on AWS Cognito Your User Pool managed service.  *NOTE: The API endpoint for production is the same as for test, but without `test.` in the URL. Production and test APIs are deployd on separate AWS accounts*.  *NOTE: The API is run on AWS API Gateway and with AWS Lambda backend. When Lambda functions are cold, there is a small delay in response time. Additionally, banks have considerable delays in their processings, especially with certificate enrollments.*  ### Service enrollment  Every integrator (partner) has own *API Key* and every user account belongs to one integrator. *API Key* is bound with service subscription. In other words, enrolling fresh *API Key* requires service agreement before file transfers are allowed on production accounts.  If user registers with `0` *API Key* (i.e. no *API Key*) she gets a fresh *API Key* and becomes the *API Key* owner. The *API Key* owner account can list all users under the same *API Key*, see the *Integrator API*. Integrators (partners) registers their own *API Key* owner accounts and use it to register their client accounts.  *NOTE: API call rate limits are set and tracked per API Key by AWS API Gateway*.  ### Account management  A user (email address) can register either *admin* or *data* or both roles. The role in the API is referred to as *mode*. Both modes have separate passwords and provide differing capabilities for the user.  Login always requires account mode parameter in addition to user's email address and password. *Admin* mode login always requires an additional SMS one-time-password (MFA), whilst with *data* mode password is enough (suitable for automation). *Admin* mode is used to configure the account (e.g. adding PGP keys and sharing certs) and *data* mode to exchange files. Listing files is allowed on both modes.  *NOTE: Integrator (partner) registers her customers by using her API Key from the API Key owner account.*  ### Bank certificate enrollment  The *SEPA WebServices* connection to the bank requires enrolling PKI certificate with the bank. The *Admin* mode can enroll certificates for different banks, but only one certificate per bank. The corresponding private key is stored encrypted with AWS KMS service.  ### Bank certificate sharing  It is possible to share the same bank certificate with multiple accounts. Certificate sharing between accounts can be configured when accounts have the same API Key. Account that holds the certificate can share/unshare it with another account (*admin* mode operation). Note that only the account that has the certificate can PGP export the certificate and corresponding private key. This allows creating e.g. one *admin* mode only account and multiple *data* mode only accounts, where the *admin* account shares its certificates with other *data* accounts.  An account can never have multiple certificates per bank, be it shared or account's enrolled certificate. This is because the API requires identification of the bank, but not the certificate and private key pair.  ### Access security  Access is secured with TLS on Amazon Web Services (AWS) API Gateway. Inside TLS, secure sessions are established by using email address as username and by RSA encrypting password along with dynamic username specific parameters fetched from the API with `InitRegister` or `InitLogin` API commands (challenge response).  Successful login provides a session token (AWS Cognito User Pool). Authorization happens with the session token (`Authorization`) and *API Key* (`x-api-key`) headers.  Administrative actions require SMS based MFA authentication (see *admin* mode). User account management is handled with AWS Cognito User Pools and each user (email) has separate *admin* and/or *data* mode (role) accounts sharing the same API account data.  ### CHANGELOG  **2.6.0** :: 2021-06-14 - Added token revocation for logout (AWS SDK based new capability), no changes to the API itself.  **2.5.0** :: 2020-04-19 - Removed OPTIONS - Fixed Login response.
 *
 * The version of the OpenAPI document: v2.6.0
 * Contact: dan.forsberg@isecure.fi
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.22.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace IsecureFi\WsApi\Client\Model;

use \ArrayAccess;
use \IsecureFi\WsApi\Client\ObjectSerializer;

/**
 * AccountDescriptor Class Doc Comment
 *
 * @category Class
 * @package  IsecureFi\WsApi\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class AccountDescriptor implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static $openAPIModelName = 'AccountDescriptor';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    protected static $openAPITypes = [
        'adminMode' => 'string',
        'certs' => '\IsecureFi\WsApi\Client\Model\CertDescriptor[]',
        'dataMode' => 'string',
        'email' => 'string',
        'export' => 'string',
        'name' => 'string',
        'phone' => 'string'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var string[]
     * @phpstan-var array<string, string|null>
     * @psalm-var array<string, string|null>
     */
    protected static $openAPIFormats = [
        'adminMode' => null,
        'certs' => null,
        'dataMode' => null,
        'email' => null,
        'export' => null,
        'name' => null,
        'phone' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var boolean[]
     */
    protected static array $openAPINullables = [
        'adminMode' => false,
        'certs' => false,
        'dataMode' => false,
        'email' => false,
        'export' => false,
        'name' => false,
        'phone' => false
    ];

    /**
     * If a nullable field gets set to null, insert it here
     *
     * @var boolean[]
     */
    protected array $openAPINullablesSetToNull = [];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return boolean[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param boolean[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Checks if a property is nullable
     *
     * @param string $property
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'adminMode' => 'AdminMode',
        'certs' => 'Certs',
        'dataMode' => 'DataMode',
        'email' => 'Email',
        'export' => 'Export',
        'name' => 'Name',
        'phone' => 'Phone'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'adminMode' => 'setAdminMode',
        'certs' => 'setCerts',
        'dataMode' => 'setDataMode',
        'email' => 'setEmail',
        'export' => 'setExport',
        'name' => 'setName',
        'phone' => 'setPhone'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'adminMode' => 'getAdminMode',
        'certs' => 'getCerts',
        'dataMode' => 'getDataMode',
        'email' => 'getEmail',
        'export' => 'getExport',
        'name' => 'getName',
        'phone' => 'getPhone'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }

    public const ADMIN_MODE_REGISTERED = 'registered';
    public const ADMIN_MODE_UNREGISTERED = 'unregistered';
    public const DATA_MODE_REGISTERED = 'registered';
    public const DATA_MODE_UNREGISTERED = 'unregistered';
    public const EXPORT_DISABLED = 'disabled';
    public const EXPORT_ALLOWED = 'allowed';

    /**
     * Gets allowable values of the enum
     *
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
     *
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
     *
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
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[]|null $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(?array $data = null)
    {
        $this->setIfExists('adminMode', $data ?? [], null);
        $this->setIfExists('certs', $data ?? [], null);
        $this->setIfExists('dataMode', $data ?? [], null);
        $this->setIfExists('email', $data ?? [], null);
        $this->setIfExists('export', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('phone', $data ?? [], null);
    }

    /**
     * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
     * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
     * $this->openAPINullablesSetToNull array
     *
     * @param string $variableName
     * @param array  $fields
     * @param mixed  $defaultValue
     */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getAdminModeAllowableValues();
        if (!is_null($this->container['adminMode']) && !in_array($this->container['adminMode'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'adminMode', must be one of '%s'",
                $this->container['adminMode'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['certs'] === null) {
            $invalidProperties[] = "'certs' can't be null";
        }
        $allowedValues = $this->getDataModeAllowableValues();
        if (!is_null($this->container['dataMode']) && !in_array($this->container['dataMode'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'dataMode', must be one of '%s'",
                $this->container['dataMode'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['email'] === null) {
            $invalidProperties[] = "'email' can't be null";
        }
        if ($this->container['export'] === null) {
            $invalidProperties[] = "'export' can't be null";
        }
        $allowedValues = $this->getExportAllowableValues();
        if (!is_null($this->container['export']) && !in_array($this->container['export'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'export', must be one of '%s'",
                $this->container['export'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        if ($this->container['phone'] === null) {
            $invalidProperties[] = "'phone' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets adminMode
     *
     * @return string|null
     */
    public function getAdminMode()
    {
        return $this->container['adminMode'];
    }

    /**
     * Sets adminMode
     *
     * @param string|null $adminMode `admin` mode status
     *
     * @return self
     */
    public function setAdminMode($adminMode)
    {
        if (is_null($adminMode)) {
            throw new \InvalidArgumentException('non-nullable adminMode cannot be null');
        }
        $allowedValues = $this->getAdminModeAllowableValues();
        if (!in_array($adminMode, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'adminMode', must be one of '%s'",
                    $adminMode,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['adminMode'] = $adminMode;

        return $this;
    }

    /**
     * Gets certs
     *
     * @return \IsecureFi\WsApi\Client\Model\CertDescriptor[]
     */
    public function getCerts()
    {
        return $this->container['certs'];
    }

    /**
     * Sets certs
     *
     * @param \IsecureFi\WsApi\Client\Model\CertDescriptor[] $certs certs
     *
     * @return self
     */
    public function setCerts($certs)
    {
        if (is_null($certs)) {
            throw new \InvalidArgumentException('non-nullable certs cannot be null');
        }
        $this->container['certs'] = $certs;

        return $this;
    }

    /**
     * Gets dataMode
     *
     * @return string|null
     */
    public function getDataMode()
    {
        return $this->container['dataMode'];
    }

    /**
     * Sets dataMode
     *
     * @param string|null $dataMode `data` mode status
     *
     * @return self
     */
    public function setDataMode($dataMode)
    {
        if (is_null($dataMode)) {
            throw new \InvalidArgumentException('non-nullable dataMode cannot be null');
        }
        $allowedValues = $this->getDataModeAllowableValues();
        if (!in_array($dataMode, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'dataMode', must be one of '%s'",
                    $dataMode,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['dataMode'] = $dataMode;

        return $this;
    }

    /**
     * Gets email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     *
     * @param string $email Email address as the account username
     *
     * @return self
     */
    public function setEmail($email)
    {
        if (is_null($email)) {
            throw new \InvalidArgumentException('non-nullable email cannot be null');
        }
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets export
     *
     * @return string
     */
    public function getExport()
    {
        return $this->container['export'];
    }

    /**
     * Sets export
     *
     * @param string $export Status for certificate and private key export allowance. See ConfigCerts.
     *
     * @return self
     */
    public function setExport($export)
    {
        if (is_null($export)) {
            throw new \InvalidArgumentException('non-nullable export cannot be null');
        }
        $allowedValues = $this->getExportAllowableValues();
        if (!in_array($export, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'export', must be one of '%s'",
                    $export,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['export'] = $export;

        return $this;
    }

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     *
     * @param string $name Full name of registrant
     *
     * @return self
     */
    public function setName($name)
    {
        if (is_null($name)) {
            throw new \InvalidArgumentException('non-nullable name cannot be null');
        }
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->container['phone'];
    }

    /**
     * Sets phone
     *
     * @param string $phone Phone number with country code and `+` in front
     *
     * @return self
     */
    public function setPhone($phone)
    {
        if (is_null($phone)) {
            throw new \InvalidArgumentException('non-nullable phone cannot be null');
        }
        $this->container['phone'] = $phone;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer|string $offset Offset
     *
     * @return boolean
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer|string $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer|string $offset Offset
     *
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


