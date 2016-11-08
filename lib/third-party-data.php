<?php

// GET THE DATA 
class ThirdPartyData {
  private $CURRENCY_CODES = array(
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

  private $YQL_STATEMENT = 'select * from csv where url="http://finance.yahoo.com/d/quotes.csv?e=.csv&f=nl1d1t1&s=';

  private $API_URL = "https://query.yahooapis.com/v1/public/yql";
  private $CURRENCY_ISO_URL = "http://www.currency-iso.org/dam/downloads/lists/list_one.xml";

  public $format = "xml";

  // Queries a single exchange rate from the Yahoo finance API
  public function getRate($code) {
    return file_get_contents($this->templateExchangeAPIQuery(array($code)));
  }

  public function getAllRates() {
    return file_get_contents($this->templateExchangeAPIQuery($this->CURRENCY_CODES));
  }

  public function getCurrencyIso() {
    return file_get_contents($this->CURRENCY_ISO_URL);
  }

  private function templateExchangeAPIQuery($codes) {
      $listResult = array();

      foreach ($codes as $code) {
        // build list of exchange rates to query the YAHOO finance API
        $statementFragement = "gbp" . strtolower($code) . "=X";
        array_push($listResult, $statementFragement);
      }

      $query = $this->YQL_STATEMENT . join(",", $listResult) . '";' ;

      return $this->API_URL . "?q=" . urlencode($query) . '&format=' . $this->format;
  }
}

