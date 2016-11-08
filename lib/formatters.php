<?php
  function formatResponseData($fromValues, $toValues) {
	  return array(
		  "at" => $toValues["date"] . " " .	$toValues["time"],
		  "rate" => $fromValues["rate"],
		  "from" => array(
			  "code" => $fromValues["code"],
			  "curr" => $fromValues["currency-name"],
        "loc" => $fromValues["locations"],
			  "amnt" => $fromValues["amnt"],
		  ),
		  "to" => array(
			  "code" => $toValues["code"],
			  "curr" => $toValues["currency-name"],
        "loc" => $toValues["locations"],
			  "amnt" => $toValues["amnt"],
		  )
	  );
  }
