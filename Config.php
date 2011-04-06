<?php
/**
 * @author Petr Jasek <jasekpetr@gmail.com>
 * @copyright 2011 Petr Jasek
 * @license http://www.gnu.org/licenses/gpl.txt
 */

/**
 * Config
 */
class Config
{
    const NUMS = 1000;

    /** @var SimpleXMLElement */
    private $xml;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->xml = simplexml_load_file($file);
    }

    /**
     * Get limit
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return float|string|NULL
     */
    public function getLimit($year, $month, $day = NULL)
    {
        $preset = $this->getPreset($year, $month);

        if (empty($day)) {
            return (float) $preset['limit'];
        }

        $limit = $preset->xpath("//limit[@day=$day]");
        return empty($limit) ?
            NULL : (string) $limit[0];
    }

    /**
     * Get numbers
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getNumbers($year, $month)
    {
        $preset = $this->getPreset($year, $month);

        $nums = array();
        foreach ($preset->num as $num) {
            $count = self::NUMS * (float) $num['p'];
            for ($i = 0; $i < $count; $i++) {
                $nums[] = (float) $num;
            }
        }

        return $nums;
    }

    /**
     * Get presert
     *
     * @param int $year
     * @param int $month
     * @return SimpleXMLElement
     */
    private function getPreset($year, $month)
    {
        $search = $this->xml->xpath("//preset[@year=$year][@month=$month]");
        return $search[0];
    }
}
