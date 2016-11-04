<?php

  function transformXML($xslLocation, $xmlDoc) {
	  // http://php.net/manual/en/xsl.examples.php
	  $xslDoc = new DOMDocument();
	  $xslDoc->load($xslLocation);
	  $proc = new XSLTProcessor();
	  $proc->importStylesheet($xslDoc);
	  return simplexml_load_string($proc->transformToXML($xmlDoc));
  }


  function domImportFromFile($fileName) {
	return dom_import_simplexml(simplexml_load_file($fileName));
  }

  function xml_to_array($root) {
	  $result = array();

	  if ($root->hasAttributes()) {
		  $attrs = $root->attributes;
		  foreach ($attrs as $attr) {
			  $result['@attributes'][$attr->name] = $attr->value;
		  }
	  }

	  if ($root->hasChildNodes()) {
		  $children = $root->childNodes;
		  if ($children->length == 1) {
			  $child = $children->item(0);
			  if ($child->nodeType == XML_TEXT_NODE) {
				  $result['_value'] = $child->nodeValue;
				  return count($result) == 1
					  ? $result['_value']
					  : $result;
			  }
		  }
		  $groups = array();
		  foreach ($children as $child) {
			  if (!isset($result[$child->nodeName])) {
				  $result[$child->nodeName] = xml_to_array($child);
			  } else {
				  if (!isset($groups[$child->nodeName])) {
					  $result[$child->nodeName] = array($result[$child->nodeName]);
					  $groups[$child->nodeName] = 1;
				  }
				  $result[$child->nodeName][] = xml_to_array($child);
			  }
		  }
	  }

	  return $result;
  }

  function findCurrencyFromCode($code, $currencies) {
	  $currencyEls = $currencies->getElementsByTagName('currency');
	  $result = false;

	  foreach ($currencyEls as $el) {
		  echo $el->childNodes[0]->nodeValue;
		  if ($el->childNodes[0]->nodeValue === strtoupper($code)) {
			  $result = $el;
		  }
	  }

	  return $result;
  }

  function sxml_append($to, $from) {
    // http://stackoverflow.com/questions/4778865/php-simplexml-addchild-with-another-simplexmlelement
    $toDom = dom_import_simplexml($to);
    $fromDom = dom_import_simplexml($from);
    $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
  }

  // This function filters the countries XML and adds the
  // countries which match the code to an array.
  function pickFromCountriesXmlAsArray($code, $countries) {
	  $countryEls = $countries->CcyTbl;

	  $result = array();
    $result["loc"] = array();
    $result["curr"] = "";

	  foreach ($countryEls->CcyNtry as $el) {
      $elCode = (string) $el->Ccy;
      $elLoc = (string) $el->CtryNm;
      $elCurr = (string) $el->CcyNm;

		  if ($elCode === strtoupper($code)) {
        array_push($result["loc"], $elLoc);
        $result["curr"] = $elCurr;
		  }
	  }

	  return $result;
  }
