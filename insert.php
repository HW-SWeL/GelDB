<?php

include 'requestMethod.php';

$postdata = file_get_contents("php://input");
$post = json_decode($postdata);
$gelProps = $post->gelProps->properties;//Make a function to determine variables and return the values?
$reactions = $post->reactions;


//print_r($reactions);

$name = $gelProps[0]->value;
$InChI = $gelProps[1]->value;
$formula = $gelProps[2]->value;
$morphology = $gelProps[3]->value;
$trigger = $gelProps[4]->value;

$numGels = json_decode(request(getCount('gdbo:Gel')));
$numGels = $numGels->results->bindings[0]->count->value;

$doesGelExist = (checkGel($numGels, $name, $formula, $morphology, $trigger, $reactions)->results->bindings[0]);

//Check if gel name exists
function checkGel($numGels, $name, $formula, $morphology, $trigger, $reactions){

  $query = "PREFIX gdbo: <http://example.com/GelsOntology/>
            SELECT ?gel
            WHERE {
                ?gel a gdbo:Gel ;
                  gdbo:name '$name'
            }";

  $searchUrl = 'localhost:3030/gdb/?query='.urlencode($query);
  $doesGelExist = json_decode(request($searchUrl));
  //print_r( $doesGelExist->results->bindings[0]);
  $doesGelExist = $doesGelExist->results->bindings[0];
  if ($doesGelExist == NULL){
    //It's a new gel, update the database.
    insertGel($numGels, $name, $formula, $morphology, $trigger, $reactions);
  }else{
  //No need to insert, delete all entered reactions?
    deleteReactions($reactions);
      echo "This chemical is already in the database";
  }

}

function formTriples(&$value,$key){
  $value = "gdbo:solventReaction $value";
}

function insertGel($count, $name, $formula, $morphology, $trigger, $reactions){
  $count = $count + 1;
  $gelID = 'gdb:chem'.$count;

  array_walk($reactions,"formTriples");

  $reactions = implode(" ; ", $reactions);

  $query = "PREFIX gdbo: <http://example.com/GelsOntology/>
            PREFIX gdb: <http://example.com/gdb/>
            PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
            INSERT DATA{ $gelID a gdbo:Gel ;
                                      gdbo:name '$name';
                                      gdbo:InChI '$InChI';
                                      rdfs:label '$name';
                                      gdbo:formula '$formula';
                                      gdbo:morph '$morphology';
                                      gdbo:triggerMech '$trigger';
                                      $reactions}";

  $query = urlencode($query);
  $url = 'http://localhost:3030/gdb/?update='.$query;//change to gdb when ready
  requestPost($url);
}

function deleteReactions($reactions){

  for ($x = 0; $x < sizeof($reactions); $x++) {
      $query = "PREFIX gdb: <http://example.com/gdb/>
                DELETE {?s ?p ?o} WHERE {
                  ?s ?p ?o .
                  FILTER(?s = $reactions[$x])
                }";
      $query = urlencode($query);
      $url = 'http://localhost:3030/gdb/?update='.$query;//change to gdb when ready
      requestPost($url);
  }
}
 ?>
