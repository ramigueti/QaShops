<?php

class AplanamientoXml
{

    function __construct($output_csv="output.csv") {
        $this->csv_file=$output_csv; 
    }
    private $header;
    private $leafs_for_nodes;
    private $csv_file;

    private function loadXml($path_xml)
    {
        if (file_exists($path_xml)) {
            $xml = simplexml_load_file($path_xml);
            return $xml;
        } else {
            exit("Error file not exist");
        }
    }

    private function processNode($xml_node)
    {
        $leafs = array();
        foreach ($xml_node->children() as $child) {
            if (0 != $child->count()) {
                $this->processNode($child);
            } else {
                $this->header[] = $child->getName();
                $leafs[$child->getName()] = $child->__toString();
            }
        }
     if(!empty($leafs))
     {
        $this->leafs_for_nodes[] = $leafs;
     }
    }
    private function writeCsv()
    {
        $header_unique=array_unique($this->header);
        $fp = fopen($this->csv_file, 'w');
        fputcsv($fp,$header_unique,";");
        
        foreach ($this->leafs_for_nodes as $leafs_node)
        {
            $value_key = array();
            foreach ($header_unique as $key) {
                $value_key[] = (isset($leafs_node[$key])) ? $leafs_node[$key] :"";
            }
            fputcsv($fp,$value_key,";");
        }
        fclose($fp);
    }

    /**
     *
     * @param unknown $xml
     */
    function aplanarXml($path_xml)
    {
        $xml = $this->loadXml($path_xml);

        $this->processNode($xml);
        $this->writeCsv();
    }
}