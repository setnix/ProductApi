<?php


namespace AppBundle\DoctrineExtensions\DBAL\Types;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

/**
 * UTC datetime type for proper timezone handling.
 * More info http://doctrine-orm.readthedocs.io/projects/doctrine-orm/en/latest/cookbook/working-with-datetime.html#handling-different-timezones-with-the-datetime-type
 *
 * Class UTCDateTimeType
 */
class UTCDateTimeType extends DateTimeType
{
    static private $utc = null;

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }

        $value->setTimeZone(self::$utc);

        return $value->format($platform->getDateTimeFormatString());
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return \DateTime|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }

        $val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::$utc);

        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }
}
