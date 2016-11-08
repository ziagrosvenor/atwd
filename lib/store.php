<?php

// OPERATE ON XML PERSISTENCE
class CurrenciesStore {

  public $currencies = null;

  public function __construct($currenciesXMLPath) {
    $this->currencies = simplexml_load_file($currenciesXMLPath);
  }

  public function findByCode($code) {

	  $result = false;

	  foreach ($this->currencies as $el) {
		  if ((string) $el->code === strtoupper($code)) {
			  $result = $el;
		  }
	  }

	  return (array) $result;
  }

  function updateRate($code, $rate) {

  }

  function deleteCurrency($code) {

  }
}

