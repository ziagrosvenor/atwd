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

  // Initial fetching of all currencies.
  if (file_get_contents("currencies.xml") === "") {
    init();
  }

  function checkGetParamsExist() {
    if (
      !isset($_GET["amnt"]) ||
      !isset($_GET["from"]) ||
      !isset($_GET["to"]) ||
      !isset($_GET["format"])
    ) {
      throw new Exception("1100");
    }
  }

  function extractParams() {
    return array(
      "amnt" => floatval($_GET["amnt"]),
      "from" => $_GET["from"],
      "to" => $_GET["to"],
    );
  }

  function extractFormat($options) {
    $format = $_GET["format"];
    $DEFAULT_FORMAT = "xml";
    $isValidFormat = ($format === "xml" || $format === "json");

    /** When USE_DEFAULT is true this function returns 'xml'.
    https://soundcloud.com/mefjus/phace-feat-mefjus-the-mothership-neosignal *  When USE_DEFAULT is false this function will check
     *  for a $_GET["format"] with a value of 'xml' or 'json'.
     *  If it cannot find one will throw an error
     */
    if ($options["USE_DEFAULT"] && !$isValidFormat) {
      $format = $DEFAULT_FORMAT;
    } else if (!$isValidFormat) {
      throw new Exception("1200");
    }

    return $format;
  }

  function refreshCurrencyByCode($code) {
    $thirdPartyData = new ThirdPartyData();
    $transformer = new Transformer();
    $currenciesStore = new CurrenciesStore("currencies.xml");

    $freshData = $thirdPartyData->getRate($code);

  }

  function ensureFreshExchangeRate($currency) {
    $result;

    if ($currency["timestamp"] > time() - (12 * (60 * 60))) {
      $result = $currency;
    } else {


    }


  }

  function getController($params) {
    // Instantiate currency persistence object
    $currenciesStore = new CurrenciesStore("currencies.xml");

    // Find the requested currencies
    $fromValues = $currenciesStore->findByCode($params["from"]);



    $toValues = $currenciesStore->findByCode($params["to"]);

    // Calculate the conversion values
    $fromValues["amnt"] = $params["amnt"];
    $fromValues["rate"] = covertFromRateToBase($fromValues["rate"]);
    $toValues["amnt"] = convertCurrency($fromValues["rate"], $toValues["rate"], $params["amnt"]);

	  return array(
		  "at" => $toValues["date"] . " " .	$toValues["time"],
		  "rate" => $fromValues["rate"],
		  "from" => array(
			  "code" => $fromValues["code"],
			  "curr" => $fromValues["currency-name"],
        "loc" => $fromValues["locations"],
			  "amnt" => $fromValues["amnt"],
		  ),
		  "to" => array(
			  "code" => $toValues["code"],
			  "curr" => $toValues["currency-name"],
        "loc" => $toValues["locations"],
			  "amnt" => $toValues["amnt"],
		  )
	  );
  }

  try {
    checkGetParamsExist();
    $params = extractParams();
    $format = extractFormat(array("USE_DEFAULT" => false));
    $responseData = getController($params);
    formatResponse($format, $responseData);
  } catch (Exception $e) {
    $format = extractFormat(array("USE_DEFAULT" => true));
    $error = buildError($e->getMessage());
    formatResponse($format, $error);
  }

