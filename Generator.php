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

    /** @var Config */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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
        $limit = $this->config->getLimit($year, $month);
        $nums = $this->config->getNumbers($year, $month);
        $avg = array_sum($nums) / (float) sizeof($nums);

        // get generated values
        $timeId = "$year.$month";
        $values = $this->getValues($timeId);

        for ($day = 0; $day < 31; $day++) {
            $time = mktime(0, 0, 0, $month, $day + 1, $year);
            if (!checkdate($month, $day + 1, $year)
                || $time > time()) { // filter invalid date (30.2) and future
                break;
            }

            // check for limit change
            $dayLimit = $this->config->getLimit($year, $month, $day + 1);
            if (isset($dayLimit)) {
                $relative = in_array($dayLimit[0], array('+', '-'));
                if ($relative) {
                    $limit += (float) $dayLimit;
                } else {
                    $limit = (float) $dayLimit;
                }
            }

            if (!isset($values[$day])) { // generate
                $value = 0.0;
                $count = $limit / $avg / (31 - $day) * (mt_rand(8, 13) / 10.0);
                for ($i = 0.0; $i < $count; $i++) {
                    $next = $value + $nums[mt_rand(0, sizeof($nums) - 1)];
                    if ($next > $limit) { // over limit
                        break;
                    }
                    $value = $next;
                }

                $values[$day] = $value;
            }

            $limit -= $values[$day];
        }

        $this->setValues($timeId, $values);
        return array($values, $limit);
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
    private function setValues($key, array $values)
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
