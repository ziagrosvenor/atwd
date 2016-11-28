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

  // Check that the required parameters are defined as HTTP query parameters
  // If the required parameters are not defined then an error will be throw.
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


  // Take the values from $_GET. Convert thm to an
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
     *  When USE_DEFAULT is false this function will check
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

  function refreshCurrency($currency) {
    // This object is used to retrieve the data from the Yahoo finance API
    $thirdPartyData = new ThirdPartyData();
    // This object is used to convert the data into the shape of this
    // services model
    $transformer = new Transformer();
    // This object is used to manage the currencies data that is persisted
    // in the currencies.xml file.
    $currenciesStore = new CurrenciesStore("currencies.xml");

    $freshData = simplexml_load_string($thirdPartyData->getByCode($currency["code"]));

    $updatedCurrency = $transformer->updateCurrency($freshData, $currency);
    $currenciesStore->updateCurrency($updatedCurrency);

    return $updatedCurrency;
  }

  function ensureFreshExchangeRate($currency) {
    $millisecondsSinceLastRefresh = time(); //- (12 * (60 * 60));

    if ($millisecondsSinceLastRefresh > intval($currency["timestamp"])) {
      $currency = refreshCurrency($currency);
    }

    return $currency;
  }

  // This function is used to validate that the value passed in HTTP query params
  // as the amnt is a valid number
  function covertAndCheckAmntParam($amnt) {
    // When passed a string floatval returns the int value 0
    $convertedAmnt = floatval($amnt);

    if ($convertedAmnt === floatval(0) && $amnt !== "0") {
      throw new Exception("1300");
    }

    return $convertedAmnt;
  }

  /* The necessary values to perform a conversion are passed into this function
   * This function will query the XML file that contains this services version of the
   * exchange rates.
   *
   * Steps required
   *
   * Find the necessary data that is required to perform a conversion.
   * This data will be taken from the services internal persistence layer.
   * Check that the data is no more than 12 hours old.
   * If the data is 12 hours old then fetch new data from the Yahoo Finance API.
   * Calculate the amount of the currency that the service is converting to.
   *
   */
  function getController($params) {
    // Instantiate currency persistence object
    $currenciesStore = new CurrenciesStore("currencies.xml");

    // Find the requested currencies
    $fromValues = $currenciesStore->findByCode($params["from"]);
    $toValues = $currenciesStore->findByCode($params["to"]);

    $fromValues = ensureFreshExchangeRate($fromValues);
    $toValues = ensureFreshExchangeRate($toValues);

    // Calculate the conversion values
    $fromValues["amnt"] = covertAndCheckAmntParam($params["amnt"]);

    $toValues["amnt"] = convertCurrency(
      floatval($fromValues["rate"]),
      floatval($toValues["rate"]),
      $fromValues["amnt"]
    );

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
  // If any of these functions cause an error to be throw
  // then the service will send an error response
  try {
    // Parameter validation
    checkGetParamsExist();
    // Get the params as an array
    $params = extractParams();
    // Get the format as a string
    $format = extractFormat(array("USE_DEFAULT" => false));
    // Calculate the conversion and return it as an array.
    $responseData = getController($params);
    // Format the result of the currency conversion request and
    // send it back as a response
    formatResponse($format, $responseData);
  } catch (Exception $e) {
    // This function is used to format the response.
    // Responses from this service are either sent in a format of XML of JSON
    // The use default option configures this function to use the default
    // format which is XML
    $format = extractFormat(array("USE_DEFAULT" => true));
    $error = buildError($e->getMessage());
    formatResponse($format, $error);
  }

