<?php
/**
 * @author Petr Jasek <jasekpetr@gmail.com>
 * @copyright 2011 Petr Jasek
 * @license http://www.gnu.org/licenses/gpl.txt
 */

/**
 * Generator
 */
class Generator
{
    const MIN = 2010;

    const MAX = 2020;

    /** @var array */
    private $options = array();

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Generate numbers
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function generate($year, $month)
    {
        if ($month > 12 || $month < 1
            || $year > self::MAX || $year < self::MIN) {
            throw new InvalidArgumentException();
        }

        $year = (int) $year;
        $month = (int) $month;

        // get config
        $timeId = "$year.$month";
        list($limit, $numbers) = $this->parseOptions($timeId);

        $values = $this->getValues($timeId);
        for ($day = 0; $day < 31; $day++) {
            $time = mktime(0, 0, 0, $month, $day + 1, $year);
            if (!checkdate($month, $day + 1, $year)
                || $time > time()) { // filter invalid date (30.2) and future
                break;
            }

            // check for limit change
            $dayLimitId = implode('.', array($timeId, $day + 1, 'limit'));
            if (isset($this->options[$dayLimitId])) {
                $dayLimit = $this->options[$dayLimitId];
                $relative = in_array($dayLimit[0], array('+', '-'));
                if ($relative) {
                    $limit += (float) $dayLimit;
                } else {
                    $limit = (float) $dayLimit;
                }
            }

            // get value from file or generate
            $value = isset($values[$day]) ?
                $values[$day] : $numbers[mt_rand(0, sizeof($numbers) - 1)];

            // check limit
            if ($limit - $value < 0.0) {
                $value = 0.0;
            } else {
                $limit -= $value;
            }

            $values[$day] = $value;
        }

        $this->setValues($timeId, $values);
        return $values;
    }

    /**
     * Parse options
     *
     * @param string $key
     * @param string $default
     * @return array
     */
    private function parseOptions($time, $default = 'default')
    {
        $return = array();
        foreach (array('limit', 'numbers') as $key) {
            $return[$key] = isset($this->options["$time.$key"]) ?
                $this->options["$time.$key"] : $this->options["$default.$key"];

        }

        return array((int) $return['limit'], array_map('floatval', $return['numbers']));
    }

    /**
     * Get values
     *
     * @param string $key
     * @return array
     */
    private function getValues($key)
    {
        $values = array();

        $file = $this->getFilename($key);
        if (file_exists($file)) {
            $values = array_map('floatval', file($file));
        }

        return $values;
    }

    /**
     * Set values
     *
     * @param string $key
     * @param array $values
     * @return int|false
     */
    public function setValues($key, array $values)
    {
        return file_put_contents($this->getFilename($key), implode("\n", $values));
    }

    /**
     * Get filename
     *
     * @param string $key
     * @return string
     */
    private function getFilename($key)
    {
        return dirname(__FILE__) . "/data/$key.txt";
    }
}
