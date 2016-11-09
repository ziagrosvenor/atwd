<?php
  function buildError($code) {
    $errors = array(
      "1000" =>	"Currency type not recognized",
      "1100" =>	"Required parameter is missing",
      "1200" =>	"Parameter not recognized",
      "1300" =>	"Currency amount must be a decimal number",
      "1400" =>	"Error in service",
    );

    return array(
      "error" => array(
        "code" => $code,
        "msg" => $errors[$code] ? $errors[$code] : $errors["1400"],
      )
    );

  }
