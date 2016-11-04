TODO
----

* Add currency data for GBP, this is the base currency defaulted to a rate of 1

* Format date string sent in the response to match example in spec

* Calculate conversion rate when the converting from non base currency i.e EUR/JPY. The rate will need to be how many JPY for 1 EUR.

* Separate out the code into separate files.

* Add timestamp check

* Add locations and currency name information from the static file retrieved from the ISO website. (countries.xml)

* Add references

* Generate the API call dynamically.

* When updating check all timestamps to get list of currency codes. Use list of currency codes to generate URL.
This will ensure that only the currency which have not been updated in the last 12hours will be updated.

This architechure may also help when building the PUT/POST/DELETE code as it will be dealing with the currencies data on a more granular basis.


Jonathan Yoga 

James Ross Health care bristol
