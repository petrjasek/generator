<?php
/**
 * @author Petr Jasek <jasekpetr@gmail.com>
 * @copyright 2011 Petr Jasek
 * @license http://www.gnu.org/licenses/gpl.txt
 */

require_once dirname(__FILE__) . '/Generator.php';
require_once dirname(__FILE__) . '/Config.php';

$config = new Config(dirname(__FILE__) . '/config.xml');
$generator = new Generator($config);

$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? $_GET['month'] : date('n');

try {
    $values = $generator->generate($year, $month);
} catch (InvalidArgumentException $e) {
    echo "Year '$year' or month '$month' not valid.\n";
    exit(1);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charser="utf-8" />
    <title>Number generator</title>
    <style>
        body { padding: 1em; }
        fieldset { border: 1px solid #ccc; }
        li { list-style-type: square; margin-bottom: 8px; }
        li.sum { margin-top: 5px; padding-top: 5px; list-style-type: none; border-top: 3px dashed #ccc; }
        li:hover { background-color: #efefef; }
    </style>
</head>
<body>
    <h1>Number generator</h1>

    <form action="" method="get">
    <fieldset>
        <legend>Select date</legend>

        <label for="year">Year:</label>
        <select id="year" name="year">
            <?php foreach (range(Generator::MIN, Generator::MAX) as $value) { ?>
            <option value="<?php echo $value; ?>" <?php echo $value == $year ? 'selected' : ''; ?>><?php echo $value; ?></option>
            <?php } ?>
        </select>
            
        <label for="month">Month:</label>
        <select id="month" name="month">
            <?php foreach (range(1, 12) as $value) { ?>
            <option value="<?php echo $value; ?>" <?php echo $value == $month ? 'selected' : ''; ?>><?php echo $value; ?></option>
            <?php } ?>
        </select>

        <input type="submit" value="Select" />
    </fieldset>
    </form>

    <h2><?php echo $year; ?>/<?php echo $month; ?></h2>
    <ul id="results">
        <?php foreach ($values as $i => $value) { ?>
        <li><?php printf('%02d.%d.%d', $i + 1, $month, $year); ?>: <strong><?php printf('%.2f', $value); ?></strong></li>
        <?php } ?>
        <li class="sum">Sum: <strong><?php echo array_sum($values); ?></strong></li>
    </ul>
</body>
</html>
