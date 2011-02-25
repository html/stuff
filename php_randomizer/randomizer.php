<?php 

function load_symbols($file_name)
{
    $symbols = array();

    foreach(explode("\n", file_get_contents($file_name)) as $line){
        if($line){
            $symbols[] = explode(' ', $line);
        }
    }

    return $symbols;
}

function display_symbols($symbols)
{
    foreach ($symbols as $val) {
        foreach ($val as $v) {
            echo "<div style='width:100px;float:left'>$v (" . ord($v) . ")</div>";
        }
        echo "<div style='clear:both'></div>";
    }
}

class Replacer {
    public static $all_symbols = array();
    public static $symbols_hash = array();
    public static function replace_symbol($symbol)
    {
        $symbol = pos($symbol);
        return (self::$symbols_hash[$symbol][array_rand(self::$symbols_hash[$symbol])]);
    }
}

function randomize_symbols($text, $file_name)
{
    $symbols = load_symbols($file_name);
    $all_symbols = array();
    $symbols_hash = array();

    foreach ($symbols as $val) {
        foreach ($val as $v) {
            $symbols_hash[$v] = $val;
            if($res = array_search($v, $all_symbols)){
                throw new Exception("Symbol \"$v\"(char code " . ord($v) . ") already exist on position $res");
            }
        }
        $all_symbols = array_merge($all_symbols, $val);
    }

    Replacer::$all_symbols = $all_symbols;
    Replacer::$symbols_hash = $symbols_hash;

    return preg_replace_callback('/[' . join($all_symbols) . ']/', array('Replacer', 'replace_symbol'), $text);
}

/*
display_symbols(load_symbols(
    $file_name = '/var/www/test/sendmail_3_0_1/admin/cron_job/symbols.txt'
));
 */
function pad($text)
{
    return str_pad($text, 5, ' ', STR_PAD_LEFT);
}

function char_codes($random)
{
    return str_replace(' ', '&nbsp;', (join('|', array_map('pad', array_map('ord', str_split($random))))));
}

$random = randomize_symbols('Алексей',  '/var/www/test/sendmail_3_0_1/admin/cron_job/symbols.txt');
echo $random;
echo "(" . char_codes($random) . ")";
?>
