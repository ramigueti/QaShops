<?php
class WriteCsv
{
    protected $header;
    protected $values;
    protected $csv_file;
    protected $delimter;
    protected $csv_output;
    
    protected function writeCsv()
    {
        $fp = fopen($this->csv_output, 'w');
        fputcsv($fp,$this->header,$this->delimter);
        
        foreach ($this->values as $line)
        {
            $value_key = array();
            foreach ($this->header as $key) {
                $value_key[] = (isset($line[$key])) ? $line[$key] :"";
            }
            fputcsv($fp,$value_key,$this->delimter);
        }
        fclose($fp);
    }
}