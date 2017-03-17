<?php
error_reporting(E_ALL);
ini_set('display_errors', 'stdout');

/**
 * Battle rap
 *
 * Write an algorithm to detect the type of pattern in a series of lyrics based on the following: AAAA, ABAB, ABBA, ABAB.
 * The number P of the last characters to check is given as an input on the first line of the file.
 *
 * The input is represented by a file containing:
 * On the first line: the number P of the last characters to check;
 * On the following lines: the lyrics with one or more strophes of four lines (maximum 16 lines for the entire text).
 *
 * The output will be one of the following 5 integer values representing:
 * 0 – If the P condition is not met OR the lyrics has no pattern OR the lyrics has more than one type of patterns;
 * 1 – If the AABB pattern is met for the entire text;
 * 2 – If the ABBA pattern is met for the entire text;
 * 3 – If the ABAB pattern is met for the entire text;
 * 4 – If the AAAA pattern is met for the entire text.
 *
 * Example:
 * For the following input:
 * 3
 * Singin' old Too Short's on the microphone
 * If you're battlin' Short that's the chance you take
 * The beat's so fresh can't leave me alone
 * So you better come fresh and don't be fake
 *
 * The output is: 3
 *
 * The pattern of these lyrics is of the ABAB type.
 *
 * Class RhymesGame
 */
class RhymesGame
{
    public function __construct($file)
    {
        $this->input_filename = $file;
    }

    public function Main()
    {
        $rhymes = $this->readString();
        $check_len = array_shift($rhymes);

        $return_value = $this->checkPattern($rhymes, $check_len);

        return $return_value;
    }

    /**
     * @param array $allLines
     * @param int $check_len
     * @return float|int
     */
    protected function checkPattern($allLines, $check_len)
    {
        $strophes = $this->splitToStrophes($allLines, $check_len);

        $patternsFound = $this->findPatterns($strophes);

        if (count($patternsFound) != 1 || end($patternsFound) % count($strophes) != 0) {
            $result = 0;
        } else {
            $result = end($patternsFound) / count($strophes);
        }

        return $result;
    }

    /**
     * @param array $finalStrings - the line endings of all verses in a strophe
     * @return string
     */
    private function getStrophePattern($finalStrings)
    {
        $a = $finalStrings[0];
        $pattern = '';
        foreach ($finalStrings as $string) {
            $pattern .= ($string == $a ? 'A' : 'B');
        }

        return $pattern;
    }

    public function readString()
    {
        if (!is_readable($this->input_filename)) {
            throw new Exception("File not found (404)", 1);
        }

        return file($this->input_filename, FILE_IGNORE_NEW_LINES);
    }

    /**
     * @param array $strophes - groups of 4 verses
     * @return array
     */
    protected function findPatterns($strophes)
    {
        $patternsMap = array(
            'AABB' => 1,
            'ABBA' => 2,
            'ABAB' => 3,
            'AAAA' => 4,
        );

        $patternsFound = array();
        foreach ($strophes as $stropheId => $finalStrings) {
            $pattern = $this->getStrophePattern($finalStrings);
            if (!isset($patternsFound[$pattern])) {
                $patternsFound[$pattern] = 0;
            }
            $patternsFound[$pattern] += array_search($pattern, array_flip($patternsMap));
        }

        return $patternsFound;
    }

    /**
     * @param array $allLines
     * @param int $check_len
     * @return array
     */
    protected function splitToStrophes($allLines, $check_len)
    {
        $strophes = array();
        $stropheId = 0;
        foreach ($allLines as $key => $line) {
            if ($key % 4 == 0 and !empty($strophes)) {
                $stropheId++;
            }
            $strophes[$stropheId][$key % 4] = substr($line, 0 - $check_len);
        }

        return $strophes;
    }
}

$o = new RhymesGame($argv[1]);
echo $o->Main() . PHP_EOL;
