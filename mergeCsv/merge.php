<?php
include "../common/write_csv.php";

class MergeCsv extends WriteCsv
{
    // Contructor de la clase MergeCsv
    function __construct($output_csv = "output.csv", $max_length = 1000, $char_delimeter = ",")
    {
        $this->csv_output = $output_csv;
        $this->max_length_of_lines = $max_length;
        $this->delimeter = $char_delimeter;
    }

    // Atributo privado que almacena la cantidad de caracteres de una línea para optimizar (por defecto 1000)
    //Si no se quiere limitar establecer a 0 en la llamada al constructor pero será menos eficiente
    private $max_length_of_lines;

    // Método privado que lee un fichero csv y lo procesa 
    // En la variable header almacena la cabecera del fichero csv y en $values_file almacena clave y valor 
    private function loadCsv($path_csv, &$header)
    {
        $values_file = array();
        if (($file = fopen($path_csv, "r")) !== FALSE) {
            $is_header = true;
            while (($datas = fgetcsv($file, $this->max_length_of_lines, $this->delimeter)) !== FALSE) {
                if ($is_header) {
                    $header = $datas;
                    $is_header = false;
                } else {
                    $key_of_value = 0;
                    $values_lines = array();
                    foreach ($datas as $data) {
                        $key = $header[$key_of_value ++];
                        $values_lines[$key] = $data;
                    }
                    $values_file[] = $values_lines;
                }
            }
            fclose($file);
        } else {
            exit("Error: file not open");
        }

        return $values_file;
    }

    // Método público que mergea dos csv
    function mergeCsv($path_cvs_1, $path_csv_2)
    {
        $header_1 = array();
        $header_2 = array();
        $values_1 = $this->loadCsv($path_cvs_1, $header_1);
        $values_2 = $this->loadCsv($path_csv_2, $header_2);

        $this->header = array_unique(array_merge($header_1, $header_2), SORT_REGULAR);
        $this->values = array_merge($values_1, $values_2);

        $this->writeCsv();
    }
}