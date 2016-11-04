<?php
  error_reporting(E_ALL);
  ini_set("display_errors", "On");
  libxml_use_internal_errors(true);

  require_once("lib/xml.php");
  require_once("lib/request.php");
  require_once("lib/date-time.php");
  require_once("lib/file-system.php");
  require_once("constants.php");

  /**
  function initialize() {
    echo "initialize";
  }

  if (file_exists("countries.xml") === 0) {
    initialize()
  }

  **/

  $currenciesXml = transformXML(
	  "currency.xsl",
	  simplexml_load_string(
		  curlGet($EXCHANGE_API_URI, null)
	  )
  );

  $gbpEl = simplexml_load_string("<currency><code>GBP</code><rate>1</rate><date>10/26/2016</date><time>8:35pm</time><timestamp>1518294900</timestamp></currency>");

  sxml_append($currenciesXml->currencies[0], $gbpEl);

  foreach ($currenciesXml->currencies[0] as $el) {
    $timestamp = dateTimeToTimestamp("!d/m/Y G:ia", $el->date . " " . $el->time);
    $el->code = str_replace("GBP/", "", $el->code);
    $el->addChild("timestamp", $timestamp);
  };

  createFile("countries.xml", file_get_contents("http://www.currency-iso.org/dam/downloads/lists/list_one.xml"));
  createFile("currencies.xml", $currenciesXml->currencies[0]->asXML());

  function formatResponseData($fromValues, $toValues) {
	  return array(
		  "at" => $toValues["date"] . " " .	$toValues["time"],
		  "rate" => $toValues["rate"] * $fromValues["rate"],
		  "from" => array(
			  "code" => $fromValues["code"],
			  "curr" => $fromValues["curr"],
        "loc" => implode(", ", $fromValues["loc"]),
			  "amnt" => $fromValues["amnt"],
		  ),
		  "to" => array(
			  "code" => $toValues["code"],
			  "curr" => $toValues["curr"],
        "loc" => implode(", ", $toValues["loc"]),
			  "amnt" => $toValues["amnt"],
		  )
	  );
  }

  $countries = simplexml_import_dom(domImportFromFile("countries.xml"));
  $currencies = domImportFromFile("currencies.xml");

  $amnt = floatval($_GET["amnt"]);
  $from = $_GET["from"];
  $to = $_GET["to"];


  function convertCurrency($fromRate, $toRate, $amnt) {
  	return (floatval($amnt) * floatval($fromRate)) * floatval($toRate);
  }

  function covertFromRateToBase($fromRate) {
    $rate;

    if ($fromRate !== 1) {
      $rate = 1 / $fromRate;
    }

    return $rate;
  }


  $toValues = xml_to_array(findCurrencyFromCode($to, $currencies));
  $fromValues = xml_to_array(findCurrencyFromCode($from, $currencies));

  $toValues = array_merge($toValues, pickFromCountriesXmlAsArray($to, $countries));
  $fromValues = array_merge($fromValues, pickFromCountriesXmlAsArray($from, $countries));

  $fromValues["amnt"] = $amnt;
  $fromValues["rate"] = covertFromRateToBase($fromValues["rate"]);
  $toValues["amnt"] = convertCurrency($fromValues["rate"], $toValues["rate"], $amnt);

  $res = formatResponseData($fromValues, $toValues);

  var_dump($res);
 ?>
