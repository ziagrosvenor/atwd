<?php


function getCurrenciesXML($EXCHANGE_API_URI) {
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

  return $currenciesXml->currencies[0]->asXML();
}


function initialize() {
  createFile("exchange-rates.xml", file_get_contents("http://www.currency-iso.org/dam/downloads/lists/list_one.xml"));
  createFile("countries-currencies.xml", getCurrenciesXML());
}

