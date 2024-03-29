<?php


namespace TimSoft\DroitAccesBundle\Extensions;


use Symfony\Component\Translation\TranslatorInterface;

class Twig_Extensions_Extension_Date extends \Twig_Extension
{
    public static $units = array(
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('time_diff', array($this, 'diff'), array('needs_environment' => true)),
        );
    }

    /**
     * Filter for converting dates to a time ago string like Facebook and Twitter has.
     *
     * @param \Twig_Environment $env a Twig_Environment instance
     * @param string|\DateTime $date a string or DateTime object to convert
     * @param string|\DateTime $now A string or DateTime object to compare with. If none given, the current time will be used.
     *
     * @return string the converted time
     */
    public function diff(\Twig_Environment $env, $date, $now = null)
    {
        // Convert both dates to DateTime instances.
        $date = twig_date_converter($env, $date);
        $now = twig_date_converter($env, $now);

        // Get the difference between the two DateTime objects.
        $diff = $date->diff($now);

        // Check for each interval if it appears in the $diff object.
        foreach (self::$units as $attribute => $unit) {
            $count = $diff->$attribute;

            if (0 !== $count) {
                return $this->getPluralizedInterval($count, $diff->invert, $unit);
            }
        }

        return '';
    }

    protected function getPluralizedInterval($count, $invert, $unit)
    {
        if ($this->translator) {
            $id = sprintf('diff.%s.%s', $invert ? 'in' : 'ago', $unit);

            return $this->translator->transChoice($id, $count, array('%count%' => $count), 'date');
        }

        if (1 !== $count) {
            $unit .= 's';
        }

        return $invert ? "in $count $unit" : "$count $unit ago";
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date';
    }
}