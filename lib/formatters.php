<?php
  require_once("lib/xml.php");

  function formatResponse($format, $data) {
    $conv = array("conv" => $data);

    if ($format === "xml") {
      header('Content-Type: application/xml');
      echo array_to_xml($conv);
    } else {
      header('Content-Type: application/json');
      echo json_encode($conv);
    }
  }
