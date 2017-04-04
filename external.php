<?php

include 'requestMethod.php';

$extCallsUrl = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/inchikey/RZJQGNCSTQAWON-UHFFFAOYSA-N/PNG';
$res = request($extCallsUrl);

$result['structure'] = 'data:image/png;base64,'.base64_encode($res);

print_r($result);
//print_r($result);

 ?>
