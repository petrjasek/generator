<?php
/**
 * @author Petr Jasek <jasekpetr@gmail.com>
 * @copyright 2011 Petr Jasek
 * @license http://www.gnu.org/licenses/gpl.txt
 */

require_once dirname(__FILE__) . '/Generator.php';

$config = parse_ini_file(dirname(__FILE__) . '/config.ini');
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
    <ol id="results">
        <?php foreach ($values as $value) { ?>
        <li><?php echo $value; ?></li>
        <?php } ?>
    </ol>
</body>
</html>
