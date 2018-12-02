<?php
include "merge.php";

$merge = new MergeCsv("output.csv");

$merge->mergeCsv("input_csv1.csv", "input_csv2.csv");
