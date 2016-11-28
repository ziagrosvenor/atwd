<?php
  function dateTimeToTimestamp($format, $datetime) {
    echo $format;
    echo $datetime;
	  $dateTime = DateTime::createFromFormat($format, $datetime);
	  return $dateTime->getTimestamp();
  }
