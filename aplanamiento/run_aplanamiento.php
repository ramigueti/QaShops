<?php
include "aplanamiento.php";

$aplanamiento = new AplanamientoXml("output_test.csv");

$aplanamiento->aplanarXml("test.xml");
