<?php
  error_reporting(E_ALL);
  ini_set("display_errors", "On");
  libxml_use_internal_errors(true);

  require_once("lib/third-party-data.php");
  require_once("lib/transformer.php");
  require_once("lib/store.php");
  require_once("lib/file-system.php");
  require_once("lib/currency.php");
  require_once("lib/formatters.php");
  require_once("constants.php");

  $thirdPartyData = new ThirdPartyData();

  $allRates = simplexml_load_string($thirdPartyData->getAllRates("JPY"));
  $currencyIso = simplexml_load_string($thirdPartyData->getCurrencyIso());

  $transformer = new Transformer();
  $currenciesData = $transformer->mergeRatesCurrencies($allRates, $currencyIso);

  createFile("currencies.xml", $currenciesData);

  $currenciesStore = new CurrenciesStore("currencies.xml");

  $amnt = floatval($_GET["amnt"]);
  $from = $_GET["from"];
  $to = $_GET["to"];

  $fromValues = $currenciesStore->findByCode($from);
  $toValues = $currenciesStore->findByCode($to);

  $fromValues["amnt"] = $amnt;
  $fromValues["rate"] = covertFromRateToBase($fromValues["rate"]);
  $toValues["amnt"] = convertCurrency($fromValues["rate"], $toValues["rate"], $amnt);

  $res = formatResponseData($fromValues, $toValues);

  var_dump($res);

  /**function handleError($value, $message) {
    if ($value === null) {
      die(echo $message);
    }
  }

  require_once("lib/xml.php");
  require_once("lib/request.php");
  require_once("lib/date-time.php");
  require_once("lib/file-system.php");

  if ($countriesCurrencies === "") {
    initialize()
  } else {
    updateExchangeRates()
  }

  $exchangeRates = domImportFromFile("exchange-rates.xml");


  $toValues = xml_to_array(findCurrencyFromCode($to, $currencies));
  $fromValues = xml_to_array(findCurrencyFromCode($from, $currencies));

  $toValues = array_merge($toValues, pickFromCountriesXmlAsArray($to, $countriesCurrencies));
  $fromValues = array_merge($fromValues, pickFromCountriesXmlAsArray($from, $countriesCurrencies));

  **/
 ?>
