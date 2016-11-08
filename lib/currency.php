<?php
  function convertCurrency($fromRate, $toRate, $amnt) {
  	return (floatval($amnt) * floatval($fromRate)) * floatval($toRate);
  }

  function covertFromRateToBase($fromRate) {
    $rate;

    if ($fromRate !== 1) {
      $rate = 1 / $fromRate;
    }

    return $rate;
  }

