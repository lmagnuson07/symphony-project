<?php

namespace App\Helper;

use Exception;

class ParseCsv
{
    private static string $fileName;
    private static mixed $header;
    private static array $data = array();
    private static int $row_count = 0;

    /**
     * @throws Exception
     */
    public static function file($fileName): bool {
        if(!file_exists($fileName)) {
            throw new Exception(message: "File does not exist.");
        } elseif(!is_readable($fileName)) {
            throw new Exception();
        }
        static::$fileName = $fileName;
        return true;
    }

    /**
     * @throws Exception
     */
    private static function setFile($fileName): void {
        if($fileName != '') {
            if (static::file($fileName)) {
//                Session::setSession('fileFeedbackMsg', "[$fileName] loaded");
            }
        }
    }

    /**
     * @throws Exception
     */
    public static function parse(string $fileName, string $delimiter = ',', array $headers = null): array {
        static::setFile($fileName);

        if(!isset(static::$fileName)) {
            throw new Exception(message: "CSV file not set.");
        }

        // clear any previous results
        static::reset();

        if (isset($headers)) {
            static::$header = $headers;
        }

        $file = fopen(static::$fileName, 'r');
        while(!feof($file)) {
            $row = fgetcsv($file, 0, $delimiter);
            if($row == null || $row === false || $row == '') { continue; }
            if(!static::$header) {
                static::$header = $row;
            } else {
                static::$data[] = array_combine(static::$header, $row);
                static::$row_count++;
            }
        }
        fclose($file);
        return static::$data;
    }

    private static function reset(): void {
        static::$header = null;
        static::$data = [];
        static::$row_count = 0;
    }

    public static function removeZeroWidthSpaceCharacters(array $arr): array {
        $trimmedRegionArray = [];
        foreach($arr as $key => $value) {
            $newKey = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $key);
            $newValue = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $value);
            $trimmedRegionArray[$newKey] = $newValue;
        }
        return $trimmedRegionArray;
    }
}