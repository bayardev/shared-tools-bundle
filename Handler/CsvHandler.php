<?php

namespace Bayard\Bundle\SharedToolsBundle\Handler;

use Bayard\Bundle\SharedToolsBundle\Exception\BayardSharedException as SharedException;

class CsvHandler
{
    //const CSV_MAX_LINE_LENGHT = 16384;
    const CSV_MAX_LINE_LENGHT = 0;
    const CSV_DELIMITER = ';';
    const INDEX_MORE_EXCEL = 2;
    const PARSE_HEADER = true;

    // protected $inCharset = 'WINDOWS-1252//TRANSLIT//IGNORE';
    // protected $outCharset = 'UTF-8';

    protected $fp;
    protected $delimiter;
    protected $length;
    protected $parse_header;
    protected $csvHeaders;
    protected $csvContent;
    protected $csvColumns = array();


    public function __construct($file_path, $parse_header = true, $delimiter = ';', $length = null)
    {
        ini_set("auto_detect_line_endings", true);

        $this->filePath = $file_path;
        $this->fp = fopen($this->filePath, "r+");
        $this->parse_header = (isset($parse_header))? $parse_header : self::PARSE_HEADER;
        $this->delimiter = (isset($delimiter))? $delimiter : self::CSV_DELIMITER;
        $this->length = (isset($length))? $length : self::CSV_MAX_LINE_LENGHT;

        if ($this->parse_header)
        {
           $this->csvHeaders = fgetcsv($this->fp, $this->length, $this->delimiter);
        }

        return $this;
    }

    public function __destruct()
    {
        if ($this->fp)
        {
            fclose($this->fp);
        }
    }

    public function getCsvHeaders()
    {
        return $this->csvHeaders;
    }

    public function getCsvContent()
    {
        if (!isset($this->csvContent)) {
            $this->csvContent = $this->get();
        }

        return $this->csvContent;
    }

    public function getRow($index)
    {
        rewind($this->fp);
        return $this->get(1, ($index+1));
    }

    public function listColumn($key)
    {
        $this->getCsvContent();
        /**
         * @todo  throw new Exception if !array_key_exists
         */
        if (!in_array($key, $this->csvHeaders)) {
            return array();
        }

        $this->csvColumns[$key] = array_column($this->csvContent, $key);

        return $this->csvColumns[$key];
    }

    public function search($key, $value)
    {
        $column_values = $this->listColumn($key);

        $search = array_search($value, $column_values);

        if (!is_null($search) && $search !== false) {
            return $search;
        }

        return false;
    }

    public function get($max_lines = 0, $start = 0, $excel_index = false)
    {
        $data = array();
        $start = ($excel_index === true && $start >= self::INDEX_MORE_EXCEL)
            ? $start - self::INDEX_MORE_EXCEL
            : $start;

        //if $max_lines is set to 0, then get all the data
        if ($max_lines > 0)
            $line_count = 0;
        else
            $line_count = -1; // so loop limit is ignored

        $count_for_start = 0;
        while ($line_count < $max_lines && ($row = fgetcsv($this->fp, $this->length, $this->delimiter)) !== FALSE)
        {
            if ($start > 0 && $count_for_start < $start) {
                $count_for_start++;
                continue;
            }

            if ($this->parse_header)
            {
                foreach ($this->csvHeaders as $i => $heading_i)
                {
                    //$row_new[$heading_i] = $this->stringEncode($row[$i]);
                    $row_new[$heading_i] = $row[$i];
                }
                $data[] = $row_new;

            }
            else
            {
                //$data[] = $this->arrayEncode($row);
                $data[] = $row;
            }

            if ($max_lines > 0)
                $line_count++;
        }

        return $data;
    }

    public function replace($index, $oldvalue, $newvalue)
    {

    }


    // protected function stringEncode($str, $in_charset = null, $out_charset = null)
    // {
    //     $in_charset = (isset($in_charset)) ? $in_charset : $this->inCharset;
    //     $out_charset = (isset($out_charset)) ? $out_charset : $this->outCharset;

    //     return iconv($in_charset, $out_charset, $str);
    // }

    // protected function arrayEncode($collection = array(), $in_charset = null, $out_charset = null)
    // {
    //     if (!is_array($collection)) {
    //         if (is_string($collection)) {
    //             return $this->stringEncode($str, $in_charset, $out_charset);
    //         } else {
    //             /**
    //              * @todo : create an exception or alert if
    //              *      $collection is not array && is not string
    //              */
    //             return $collection;
    //         }

    //     }

    //     foreach ($collection as $key => $value) {
    //         if (is_string($value)) {
    //             $collection[$key] = $this->stringEncode($value, $in_charset, $out_charset);
    //         }
    //     }

    //     return $collection;
    // }
}