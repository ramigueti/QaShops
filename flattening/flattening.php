<?php
include "../common/write_csv.php";

/**
 *
 * @author ramigueti
 *        
 */
class FlatteningXml extends WriteCsv
{
    function __construct($output_csv = "output.csv", $delimeter = ";")
    {
        $this->csv_output = $output_csv;
        $this->delimeter = $delimeter;
    }

    private function loadXml($path_xml)
    {
        if (file_exists($path_xml)) {
            $xml = simplexml_load_file($path_xml);
            return $xml;
        } else {
            exit("Error file not exist");
        }
    }

    private function processNodes($xml)
    {
        $leafs = array();
        foreach ($xml->children() as $child) {
            if (0 != $child->count()) {
                $this->processNodes($child);
            } else {
                $this->header[] = $child->getName();
                $leafs[$child->getName()] = $child->__toString();
            }
        }
        if (! empty($leafs)) {
            $this->values[] = $leafs;
        }
    }

    function flatteningXml($path_xml)
    {
        $xml = $this->loadXml($path_xml);

        $this->processNodes($xml);
        $this->header = array_unique($this->header);
        $this->writeCsv();
    }
}