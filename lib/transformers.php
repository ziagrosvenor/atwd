<?php

function sxml_append($to, $from) {
  // http://stackoverflow.com/questions/4778865/php-simplexml-addchild-with-another-simplexmlelement
  $toDom = dom_import_simplexml($to);
  $fromDom = dom_import_simplexml($from);
  $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
}

function applyXSLT($xslLocation, $xmlDoc) {
  // http://php.net/manual/en/xsl.examples.php
  $xslDoc = new DOMDocument();
  $xslDoc->load($xslLocation);
  $proc = new XSLTProcessor();
  $proc->importStylesheet($xslDoc);
  return simplexml_load_string($proc->transformToXML($xmlDoc));
}

class Transformer {
  // USE XSLT TO TRANSFORM TO LOTS OF XML AND APPEND
  public function ratesCountries($rates, $countriesCurrencies) {

    $currenciesXml = applyXSLT(
      "currency.xsl",
      simplexml_load_string($rates)
    );

    $this->appendBaseCurrency($currenciesXml);

    foreach ($currenciesXml->currencies[0] as $el) {
      removeBaseFromCode($el);
      addTimestamp($el);
      addCurrencyNameLocations($el, $countriesCurrencies);
    };

    return $currenciesXml->currencies[0]->asXML();
  }

  private function removeBaseFromCode($el) {
    $el->code = str_replace("GBP/", "", $el->code);
  }

  private function addTimestamp($el) {
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
    $gbpEl = simplexml_load_string("<currency><code>GBP</code><rate>1</rate><date>10/26/2016</date><time>8:35pm</time><timestamp>1518294900</timestamp></currency>");

    sxml_append($currenciesXml->currencies[0], $gbpEl);
  }

  public function rate($rate) {

  }
}

