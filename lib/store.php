<?php
require_once("lib/file-system.php");

// OPERATE ON XML PERSISTENCE
class CurrenciesStore {

  public $currencies = null;

  /*  The __construct magic method is used to load the
   *  currencies data into state from the file system when an instance of this
   *  class is created.
   */
  public function __construct($currenciesXMLPath) {
    $this->currencies = simplexml_load_file($currenciesXMLPath);
  }

  public function findByCode($code) {
    $el = $this->findElByCode($code);

    // When unable to find a currency type then throw
    if (!$el) {
      throw new Exception('1000');
    }

    return (array) $el;
  }

  /* This method loops through all the currency elements to
   * find a currency element with a name matching the $code that
   * is supplied as an argument to this method.
   */
  private function findElByCode($code) {
	  $result = false;

	  foreach ($this->currencies as $el) {
		  if ((string) $el->code === strtoupper($code)) {
			  $result = $el;
		  }
	  }

    return $result;
  }

  /*  This will save the currencies data that is stored
   *  in an instance of this object to a file called
   *  currencies.xml
   *
   * The currencies data is stored in a property called $this->currencies
   *
   * createFile is library function which I created to save a string into a file.
   */
  private function writeCurrenciesToFile() {
    createFile(
      "currencies.xml",
      $this->currencies[0]->asXML()
    );
  }

  /*  This method is used to update a single currency entity.
   *  It is used to keep the currencies up to date.
   *  It is also used by the POST / Update feature that is part
   *  of component B
   *
   */
  function updateCurrency($currency) {
    $el = $this->findElByCode($currency["code"]);
    $el->rate = $currency["rate"];
    $el->timestamp = $currency["timestamp"];
    $this->writeCurrenciesToFile();
  }

  function createCurrency($currencyValues) {
    $curr = $this->currencies->addChild("currency");

    foreach($currencyValues as $currKey => $currVal) {
      $curr->addChild($currKey, $currVal);
    }

    $this->writeCurrenciesToFile();
  }

  function deleteCurrency($code) {


  }
}

