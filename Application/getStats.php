<?php

  include 'requestMethod.php';

  function formURL(){
    $format = 'json';

    $query = 'PREFIX gdbo: <http://example.com/GelsOntology/>
              SELECT (COUNT(DISTINCT ?gels) as ?count)
	            WHERE {
  		            ?gels a gdbo:Gel;
	            }';


    $searchUrl = 'localhost:3030/gdb/?query='.urlencode($query);
    return $searchUrl;
  }


  //  $responseArray = json_decode(request(formURL())
    $t = request(formURL());
    echo $t;//->results->bindings->value;
 ?>
