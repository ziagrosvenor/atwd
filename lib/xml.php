<?php
  /* This function is used to convert the response
   * from the Yahoo Finance API into the shape of that
   * of the model of this application.
   *
   */
  function applyXSLT($xslLocation, $xmlDoc) {
    // http://php.net/manual/en/xsl.examples.php
    $xslDoc = new DOMDocument();
    $xslDoc->load($xslLocation);
    $proc = new XSLTProcessor();
    $proc->importStylesheet($xslDoc);
    return simplexml_load_string($proc->transformToXML($xmlDoc));
  }

  /* Covert some an array into XML data.
   * This function is used to format the data that is sent
   * in responses from this service.
   */
  function array_to_xml($array, $root) {
    $xml = "<{$root}>\n";

    foreach ($array as $key => $value) {
        if (is_array($value)) {
          $xml .= array_to_xml($value, $key);
        } else {
            if (is_numeric($key)) {
                $xml .= "<{$root}>{$value}</{$root}>\n";
            } else {
                $xml .= "<{$key}>{$value}</{$key}>\n";
            }
        }
    }

    $xml .= "</{$root}>\n";

    return $xml;
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

  function sxml_append($to, $from) {
    // http://stackoverflow.com/questions/4778865/php-simplexml-addchild-with-another-simplexmlelement
    $toDom = dom_import_simplexml($to);
    $fromDom = dom_import_simplexml($from);
    $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
  }
