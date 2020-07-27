# NAB RadioDNS Setup Tutorial

This repository contains the final code from a coding tutorial included in the
NAB RadioDNS Setup Tutorial session held in July 2020.

It includes a simple PHP script (index.php) that you can use and adapt to build a
compliantRadioDNS SI file and image assets from a source MySQL database,
directory of station logos and an access token for the
[NRSC PI Codes](https://picodes.nrscstandards.org) Call Signs API.

The `radio.sql` file is a dump of the database used in the demo.

There is an alternate version of the script (index-googlesheets.php), which uses
a Google Sheet published in CSV format as it's data source.
Create a Google Sheet with a first row of
``` callsign	name	description	genre	logo_url	stream_url
and then use the File -> Publish to the Web -> Entire Document as CSV and copy
the URL provided (ending output=csv) into the script.

There is an example [Google Sheet](https://docs.google.com/spreadsheets/d/1k14sJX80GhtRLJwOzBQ8KjseLeom3ByGZiA7R3Z1mZA/edit?usp=sharing) and its [CSV output.](https://docs.google.com/spreadsheets/d/e/2PACX-1vT8vZCX3E5jvk519354UTwrmLEgZjuHibjZaPAIyDkRMZuG5ULERyDNd5qOs_dK85sioZT1EnowkAKB/pub?output=csv)
