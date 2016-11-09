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
      $this->addDateTime($el);
      $this->addCurrencyNameLocations($el, $countriesCurrencies);
    };

    return $currenciesXml->currencies[0]->asXML();
  }

  private function removeBaseFromCode($el) {
    $el->code = str_replace("GBP/", "", $el->code);
  }

  private function addDateTime($el) {

    $el->date = date("d/m/Y");
    $el->time = date("G:ia");

    $timestamp = dateTimeToTimestamp("!d/m/Y G:ia", $el->date . " " . $el->time);
    $el->addchild("timestamp", $timestamp);
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

