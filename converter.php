<?php
// USE SIMPLE XML



// Create all codes array file

$CURRENCY_CODES = array(
  "CAD",
  "HF",
  "CNY",
  "DKK",
  "EUR",
  "GBP",
  "HKD",
  "HUF",
  "INR",
  "JPY",
  "MXN",
  "MYR",
  "NOK",
  "NZD",
  "PHP",
  "RUB",
  "SEK",
  "SGD",
  "THB",
  "TRY",
  "USD",
  "ZAR",
);

// GET THE DATA 
class ThirdPartyData {

  private $YQLStatement = 'select * from csv where url="http://finance.yahoo.com/d/quotes.csv?e=.csv&f=nl1d1t1&s=';

  public $format = "xml";


  public function getRate($code) {

  }

  public function getAllRates() {

  }

  public function getAllCountriesCurrencies() {

  }

  private templateListOfCodesToQuery($codes) {
      $listResult = array();

      foreach ($codes as $code) {
        // build list of exchange rates to query the YAHOO finance API
        $statementFragement = "gbp" . strtolower($code) . "=X";
        array_push($listResult, $statementFragement);
      }

      return join(",", $listResult);
  }
}

class Transformer {
  // USE XSLT TO TRANSFORM TO LOTS OF XML AND APPEND
  function ratesCountries($rates, $countriesCurrencies) {

  }

  function rate($rate) {

  }
}

// OPERATE ON XML PERSISTENCE
class Currencies {
  function findCurrency($code) {

  }

  function updateRate($code, $rate) {

  }

  function deleteCurrency($code) {

  }
}

// USE OBJECTS TO GET DATA TO RETURN
function getController($routeParams) {

}

