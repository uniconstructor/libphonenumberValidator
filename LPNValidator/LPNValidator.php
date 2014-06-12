<?php

/**
 * Lib Phone Number validator for Yii framework
 * @todo add custom return format
 */
class LPNValidator extends CValidator
{
    /**
     * @var bool $allowEmpty Whether the attribute is allowed to be empty.
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
     * @var string $emptyMessage the message to be displayed if an empty value 
     * is validated while 'allowEmpty' is false
     */
    public $emptyMessage = "{attribute} cannot be blank";
    
    /**
     * @var string - default country code ('GB', 'US' or 'RU')
     */
    public $defaultCountry = '';
    /**
     * @var bool
     */
    public $enableClientValidation=false;
    
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
        if ( empty($object->$attribute) )
        {// first, if 'allowEmpty' is true and the attribute is indeed empty - finish execution - all good!
            if ( $this->allowEmpty )
            {
                return;
            }
            $translated_msg = Yii::t("LPNValidator.general", $this->emptyMessage, array('{attribute}' => $attribute));
            $this->addError($object, $attribute, $translated_msg);
            return $object->$attribute;
        }
        
        Yii::import('libphonenumber.*');
        
        $phoneUtil   = \libphonenumber\PhoneNumberUtil::getInstance();
        $numberProto = $phoneUtil->parse($object->$attribute, $this->defaultCountry);
        
        if ( $phoneUtil->isValidNumber($numberProto) )
        {// number is valid
            return $phoneUtil->format($numberProto, \libphonenumber\PhoneNumberFormat::NATIONAL);  //$numberProto->countryCode . $numberProto->nationalNumber;
        }
        
        $translated_msg = Yii::t("LPNValidator.general", $this->message);
        $this->addError($object, $attribute, $translated_msg);
    }
}