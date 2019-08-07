<?php

namespace Webmasters\Doctrine\DBAL\Types;

/**
 * Modify Doctrine DateTime type to allow our custom class as value
 *
 * @link https://github.com/doctrine/dbal/issues/2794
 * @link https://github.com/doctrine/dbal/commit/912b2b8bb71e560bd7269bdeae00b4c8a7470f25
 * @link https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/custom-mapping-types.html
 */
use \Doctrine\DBAL\Types\DateTimeTzType as OldDateTimeTzType;
use \Doctrine\DBAL\Platforms\AbstractPlatform;
use \Webmasters\Doctrine\ORM\Util\DateTime;

/**
 * Class DateTimeTzType
 * @package Webmasters\Doctrine\DBAL\Types
 */
class DateTimeTzType extends OldDateTimeTzType {
    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return string
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        if ($value instanceof DateTime) {
            $value = $value->getDateTime();
        }
        
        return parent::convertToDatabaseValue($value, $platform);
    }
}
