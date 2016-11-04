<?php
  function dateTimeToTimestamp($format, $datetime) {
	  $dateTime = DateTime::createFromFormat($format, $datetime); 
	  return $dateTime->getTimestamp();
  }
