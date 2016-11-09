<?php
  function applyXSLT($xslLocation, $xmlDoc) {
    // http://php.net/manual/en/xsl.examples.php
    $xslDoc = new DOMDocument();
    $xslDoc->load($xslLocation);
    $proc = new XSLTProcessor();
    $proc->importStylesheet($xslDoc);
    return simplexml_load_string($proc->transformToXML($xmlDoc));
  }

  function array_to_xml($array, $level=1) {
    // https://vantulder.net/old-articles/array-to-xml
    $xml = '';
    foreach ($array as $key=>$value) {
      $key = strtolower($key);
      if (is_object($value)) {$value=get_object_vars($value);}// convert object to array

      if (is_array($value)) {
        $multi_tags = false;
        foreach($value as $key2=>$value2) {
          if (is_object($value2)) {$value2=get_object_vars($value2);} // convert object to array
          if (is_array($value2)) {
            $xml .= str_repeat("\t",$level)."<$key>\n";
            $xml .= array_to_xml($value2, $level+1);
            $xml .= str_repeat("\t",$level)."</$key>\n";
            $multi_tags = true;
          } else {
            if (trim($value2)!='') {
              if (htmlspecialchars($value2)!=$value2) {
                $xml .= str_repeat("\t",$level).
                  "<$key2><![CDATA[$value2]]>". // changed $key to $key2... didn't work otherwise.
                  "</$key2>\n";
              } else {
                $xml .= str_repeat("\t",$level).
                  "<$key2>$value2</$key2>\n"; // changed $key to $key2
              }
            }
            $multi_tags = true;
          }
        }
        if (!$multi_tags and count($value)>0) {
          $xml .= str_repeat("\t",$level)."<$key>\n";
          $xml .= array_to_xml($value, $level+1);
          $xml .= str_repeat("\t",$level)."</$key>\n";
        }

      } else {
        if (trim($value)!='') {
          echo "value=$value<br>";
          if (htmlspecialchars($value)!=$value) {
            $xml .= str_repeat("\t",$level)."<$key>".
              "<![CDATA[$value]]></$key>\n";
          } else {
            $xml .= str_repeat("\t",$level).
              "<$key>$value</$key>\n";
          }
        }
      }
    }
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
