<?php
  function convertCurrency($fromRate, $toRate, $amnt) {
  	return ($amnt * (1 / $fromRate)) * $toRate;
  }

  function covertFromRateToBase($fromRate) {
    $rate;

    if ($fromRate !== 1) {
      $rate = 1 / $fromRate;
    }

    return $rate;
  }

