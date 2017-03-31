<?php

/*These methods are reused in various places in the GelDB application
  Includes getCount, retrieving the number of gels/reactions/solvents,
  CURL request methods, for sending requests to the DB/Linked Data Api
*/

function getCount($type){
  $query = "PREFIX gdbo: <http://example.com/GelsOntology/>
            SELECT (COUNT(DISTINCT ?reacts) as ?count)
            WHERE {
                ?reacts a $type
            }";


  $searchUrl = 'localhost:3030/gdb/?query='.urlencode($query);
  return $searchUrl;
}

function requestPost($url){

   // is curl installed?
   if (!function_exists('curl_init')){
      die('CURL is not installed!');
   }
   // get curl handle
   $ch= curl_init();
   // set request url
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch,CURLOPT_POST, 1); //0 for get
   //curl_setopt($ch, CURLOPT_POSTFIELDS, $dat);

   $response = curl_exec($ch);
   curl_close($ch);
   return $response;

}

function request($url){

   // is curl installed?
   if (!function_exists('curl_init')){
      die('CURL is not installed!');
   }
   // get curl handle
   $ch= curl_init();
   // set request url
   curl_setopt($ch,
      CURLOPT_URL,
      $url);

   // return response, don't print/echo
   curl_setopt($ch,
      CURLOPT_RETURNTRANSFER,
      true);

   /*
   Here you find more options for curl:
   http://www.php.net/curl_setopt
   */
   $response = curl_exec($ch);
   curl_close($ch);
   return $response;
}


function getChem($solvent){
  $query = "PREFIX gdbo: <http://example.com/GelsOntology/>
            SELECT ?solvent
            WHERE {
                ?solvent gdbo:name '$solvent'
            }";
  $searchUrl = 'localhost:3030/gdb/?query='.urlencode($query);
  return $searchUrl;
}

 ?>
