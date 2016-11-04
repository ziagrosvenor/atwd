<?php
  function createFile($fileName, $fileContents) {
	  // www.w3schools.com/php/php_file_create.asp
	  $file = fopen($fileName, "w");
	  fwrite($file, $fileContents);
	  fclose($file);
  }
