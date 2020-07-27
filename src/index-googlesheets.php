<?php

// google sheet configuration
define('SHEET_URL', 'URL TO THE CSV OF YOUR GOOGLE SHEET GOES HERE');


// NRSC PI Codes Call Sign API token
define('CALLSIGN_API_TOKEN', 'YOUR KEY GOES HERE');

// RadioDNS-specified logo sizes
define('LOGO_SIZES', [[32, 32], [112, 32], [128, 128], [320, 240], [600, 600]]);
define('LOGO_SQUARE_SIZE', [32, 32]);
define('LOGO_RECTANGLE_SIZE', [112, 32]);

// output path for the files
define('OUTPUT_PATH', 'output/');

// HTTP host that the files will be deployed on
define('HOST', 'http://epg.example.com/');

// create the output paths
@mkdir(OUTPUT_PATH . 'logos', 0777, true);
@mkdir(OUTPUT_PATH . 'radiodns/spi/3.1', 0777, true);

// connect to Google Sheet and extract rows of data into an associative array
$rows   = array_map('str_getcsv', file(SHEET_URL));
$header = array_shift($rows);
$csv    = array();
foreach($rows as $row) {
    $csv[] = array_combine($header, $row);
}


// iterate results, build data and create logo files
$stations = [];
$apiUrl = 'https://picodes.nrscstandards.org/callsigns?';
foreach($csv as $row) {
    $callSign = $row['callsign'];
    $logoUrl = $row['logo_url'];

    $source = imagecreatefrompng($logoUrl);
    list($width, $height) = getimagesize($logoUrl);

    foreach (LOGO_SIZES as $size) {
        list($targetWidth, $targetHeight) = $size;
        $destination = imagecreatetruecolor($targetWidth, $targetHeight);
        imagefill($destination, 0, 0, imagecolorallocate($destination, 255, 255, 255));
        $xOffset = (($targetWidth - $targetHeight) / 2);
        imagecopyresampled($destination, $source, $xOffset, 0, 0, 0, $targetHeight, $targetHeight, $width, $height);
        imagepng($destination, OUTPUT_PATH . 'logos/' . $callSign . '_' . $targetWidth . 'x' . $targetHeight . '.png');
    }

    $stations[$callSign] = $row;

    // builds the query string for the API call because PHP's query builder can't do multi-value
    $apiUrl = $apiUrl . (($apiUrl[-1] == '?') ? '' : '&') . 'call_sign=' . $callSign;
}

// request API to convert call sign to broadcast parameters
$options = [
    'http' => [
        'method' => 'GET',
        'header' => "Host: picodes.nrscstandards.org\r\nAuthorization: Bearer " . CALLSIGN_API_TOKEN . "\r\n",
    ]
];
$context = stream_context_create($options);
$data = json_decode(file_get_contents($apiUrl, false, $context), true);
foreach ($data as $station) {
    $callSign = $station['call_sign'];
    $stations[$callSign]['bearer'] = $station['fm_bearer_id'];
}

// generate XML output
$xml = new SimpleXMLElement('<serviceInformation/>');
$xml['xmlns'] = 'http://www.worlddab.org/schemas/spi/31';
$xml['xmlns:xsi'] = 'http://www.w3.org/2001/XMLSchema-instance';
$xml['xsi:schemaLocation'] = 'http://www.worlddab.org/schemas/spi/31 spi_31.xsd';

$services = $xml->addChild('services');
foreach ($stations as $station) {
    // create the service element
    $service = $services->addChild('service');

    // required name elements
    $service->addChild('shortName', $station['callsign']);
    $service->addChild('mediumName', $station['name']);

    // required short description
    $service->addChild('mediaDescription')->addChild('shortDescription', $station['description']);

    // required logo elements
    foreach (LOGO_SIZES as $size) {
        list($width, $height) = $size;
        $logo = $service->addChild('mediaDescription')->addChild('multimedia');
        $logo['url'] = HOST . 'logos/' . $callSign . '_' . $targetWidth . 'x' . $targetHeight . '.png';
        $logo['type'] = (($size == LOGO_SQUARE_SIZE)
            ? 'logo_colour_square' : (($size == LOGO_RECTANGLE_SIZE)
                ? 'logo_colour_rectangle' : 'logo_unrestricted'));
        if ($logo['type'] == 'logo_unrestricted') {
            $logo['width'] = $width;
            $logo['height'] = $height;
        }
    }

    // required genre
    $genre = $service->addChild('genre');
    $genre['href'] = $station['genre'];

    // broadcast bearer
    $bearer = $service->addChild('bearer');
    $bearer['id'] = $station['bearer'];
    $bearer['cost'] = 10;
    
    // ip bearer
    $bearer = $service->addChild('bearer');
    $bearer['id'] = $station['stream_url'];
    $bearer['cost'] = 20;
}

// output XML
$xml->asXML(OUTPUT_PATH . 'radiodns/spi/3.1/SI.xml');
