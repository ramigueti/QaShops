<?php
include "flattening.php";

$flattening = new FlatteningXml("output.csv");

$flattening->flatteningXml("test.xml");
