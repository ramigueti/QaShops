<?php
class WriteCsv
{
    protected $header;
    protected $values;
    protected $csv_file;
    protected $delimeter;
    protected $csv_output;
    
    protected function writeCsv()
    {
        $fp = fopen($this->csv_output, 'w');
        fputcsv($fp,$this->header,$this->delimeter);
        
        foreach ($this->values as $line)
        {
            $value_key = array();
            foreach ($this->header as $key) {
                $value_key[] = (isset($line[$key])) ? $line[$key] :"";
            }
            fputcsv($fp,$value_key,$this->delimeter);
        }
        fclose($fp);
    }
}