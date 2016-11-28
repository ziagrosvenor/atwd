<?php
require_once("lib/formatters.php");

function formatResponse($format, $data) {
  if ($format === "xml") {
  //  header('Content-Type: application/xml');
    echo array_to_xml($data, "conv");
  } else {
  //  header('Content-Type: application/json');
    echo json_encode(array("conv" => $data));
  }
}

