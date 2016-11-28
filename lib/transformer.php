<?php
require_once("lib/date-time.php");
require_once("lib/xml.php");

class Transformer {
  // USE XSLT TO TRANSFORM TO LOTS OF XML AND APPEND
  public function mergeRatesCurrencies($rates, $countriesCurrencies) {

    $currenciesXml = applyXSLT(
      "currency.xsl",
      $rates
    );

    $this->appendBaseCurrency($currenciesXml);

    foreach ($currenciesXml->currencies[0] as $el) {
      $this->removeBaseFromCode($el);
      $this->addTimestamp($el);
      $this->addCurrencyNameLocations($el, $countriesCurrencies);
    };

    return $currenciesXml->currencies[0]->asXML();
  }

  public function updateCurrency($rate, $currency) {
    $currenciesXml = applyXSLT(
      "currency.xsl",
      $rate
    );

    foreach ($currenciesXml->currencies[0] as $el) {
      $this->addTimestamp($el);
    };

    $currency["rate"] = (string) $currenciesXml->currencies[0][0]->currency->rate;
    $currency["timestamp"] = (string) $currenciesXml->currencies[0][0]->currency->timestamp;

    return $currency;

  }

  private function removeBaseFromCode($el) {
    $el->code = str_replace("GBP/", "", $el->code);
  }

  private function addTimestamp($el) {
    $el->addchild("timestamp", strtotime($el->date . " " . $el->time));
  }

  private function addCurrencyNameLocations($el, $countries) {
    $result = array();
    $result["locations"] = array();
    $result["currencyName"] = "";

	  $countryEls = $countries->CcyTbl;

	  foreach ($countryEls->CcyNtry as $countriesEl) {
      $code = (string) $countriesEl->Ccy;
      $location = (string) $countriesEl->CtryNm;
      $currencyName = (string) $countriesEl->CcyNm;

		  if ($code === strtoupper($el->code)) {
        array_push($result["locations"], $location);
        $result["currencyName"] = $currencyName;
		  }
	  }

    $el->addchild("currency-name", $result["currencyName"]);
    $el->addchild(
      "locations",
      join(", ", $result["locations"])
    );
  }

  private function appendBaseCurrency($currenciesXml) {
    $gbpEl = simplexml_load_string("<currency><code>GBP</code><rate>1</rate></currency>");
    sxml_append($currenciesXml->currencies[0], $gbpEl);
  }

  public function pickRateFrom($rate) {

  }
}

