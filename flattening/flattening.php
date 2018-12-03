<?php
include "../common/write_csv.php";

class FlatteningXml extends WriteCsv
{
    // Contructor de la clase MergeCsv
    function __construct($output_csv = "output.csv", $delimeter = ";")
    {
        $this->csv_output = $output_csv;
        $this->delimeter = $delimeter;
    }

    // Método privado que permite cargar un fichero Xml (devuelve un simpleXml)
    private function loadXml($path_xml)
    {
        if (file_exists($path_xml)) {
            $xml = simplexml_load_file($path_xml);
            return $xml;
        } else {
            exit("Error file not exist");
        }
    }

    // Método recursivo que dado un SimpleXml lo procesa llegando a las hojas
    // Guardar por un lado la cabecera del futuro csv y por otro almacena en values clave y valor
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

    // Método público que dado un path con un fichero xml lo aplana y lo escribe en un fichero csv.
    function flatteningXml($path_xml)
    {
        $xml = $this->loadXml($path_xml);

        $this->processNodes($xml);
        $this->header = array_unique($this->header);
        $this->writeCsv();
    }
}