<?php

// import pareser lib
Yii::import('libphonenumber.*');

use libphonenumber;

/**
 * Lib Phone Number validator for Yii framework
 * 
 * @see https://github.com/davideme/libphonenumber-for-PHP
 */
class LPNValidator extends CValidator
{
    /**
     * @var bool $allowEmpty - Whether the attribute is allowed to be empty.
     */
    public $allowEmpty = false;
    /**
     * @var string $message default error message.
     * Note that if you wish it to be translated please pass 
     * translated value to this validator class in rules() method
     * for the relevant AR class.
     */
    public $message = "Invalid phone number";
    /** 
     * @var string $emptyMessage - the message to be displayed if an empty value 
     *                             is validated while 'allowEmpty' is false
     */
    public $emptyMessage = "{attribute} cannot be blank";
    /**
     * @var string - default country code ('GB', 'US' or 'RU')
     */
    public $defaultCountry;
    /**
     * @var bool
     */
    public $enableClientValidation = false;
    /**
     * @var string
     */
    public $outputFormat = \libphonenumber\PhoneNumberFormat::NATIONAL;
    
    /**
     * validates $attribute in $object.
     *
     * @param CModel $object the object to check
     * @param string $attribute the attribute name to validate in the given $object.
     *
     * @throws CException
     */
    protected function validateAttribute($object, $attribute)
    {
        if ( $value = trim($object->$attribute) )
        {// attribute contains value
            try
            {// parser throws an exception for any error
                /* @var $parser \libphonenumber\PhoneNumberUtil */
                $parser     = \libphonenumber\PhoneNumberUtil::getInstance();
                /* @var $numberData PhoneNumber */
                $numberData = $parser->parse($value, $this->defaultCountry);
                // format and transform to string
                $object->$attribute = $phoneUtil->format($numberData, $this->outputFormat);
            }catch ( NumberParseException $e )
            {// incorrect number
                $this->addError($object, $attribute, $e->message);
            }
        }elseif ( ! $this->allowEmpty )
        {// attribute cannot be empty
            $emptyMessage = Yii::t("LPNValidator.general", $this->emptyMessage, array('{attribute}' => $attribute));
            $this->addError($object, $attribute, $emptyMessage);
        }
    }
}