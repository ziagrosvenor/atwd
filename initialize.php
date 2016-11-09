<?php
  require_once("lib/third-party-data.php");
  require_once("lib/transformer.php");
  require_once("lib/file-system.php");

  function init() {
    $thirdPartyData = new ThirdPartyData();

    $allRates = simplexml_load_string($thirdPartyData->getAllRates());
    $currencyIso = simplexml_load_string($thirdPartyData->getCurrencyIso());

    $transformer = new Transformer();
    $currenciesData = $transformer->mergeRatesCurrencies($allRates, $currencyIso);

    createFile("currencies.xml", $currenciesData);
  }
