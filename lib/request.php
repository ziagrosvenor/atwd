<?php
  function curlGet($uri, $querystring) {
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here

    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $uri . ($querystring ? "?" . $querystring : "")
    ));

    // Send the request & save response to $resp
    $resp = curl_exec($curl);

    // Close request to clear up some resources
    curl_close($curl);

    return $resp;
  }

