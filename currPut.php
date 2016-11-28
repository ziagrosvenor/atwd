<?php
  error_reporting(E_ALL);
  ini_set("display_errors", "On");
  libxml_use_internal_errors(true);

  require_once("initialize.php");
  require_once("lib/store.php");
  require_once("lib/currency.php");
  require_once("lib/errors.php");
  require_once("lib/formatters.php");
  require_once("lib/third-party-data.php");
  require_once("lib/transformer.php");


  $requiredPutParams = array(
    "code",
    "name",
    "loc",
    "rate",
  );

  function parsePutReq() {
    $put_vars = array();
    parse_str(file_get_contents("php://input"), $put_vars);
    return $put_vars;
  }


  function checkGetParamsExist($requiredParams, $params) {
    foreach ($requiredParams as $requiredParam) {
      if (
        !isset($params[$requiredParam])
      ) {
        throw new Exception("1100");
      }
    }
  }


  function putController($params) {
    // Instantiate currency persistence object
    $currenciesStore = new CurrenciesStore("currencies.xml");
    $currenciesStore->createCurrency($params);
    return $params;
  }

  // If any of these functions cause an error to be throw
  // then the service will send an error response
  try {
    $put_vars = parsePutReq();

    // Parameter validation
    checkGetParamsExist($requiredPutParams, $put_vars);
    $responseData = putController($put_vars);
    // Format the result of the currency conversion request and
    // send it back as a response
    formatResponse("xml", $responseData);
  } catch (Exception $e) {
    // This function is used to format the response.
    // Responses from this service are either sent in a format of XML of JSON
    // The use default option configures this function to use the default
    // format which is XML
    $error = buildError($e->getMessage());
    formatResponse("xml", $error);
  }
