<?php

include 'requestMethod.php';

$postdata = file_get_contents("php://input");
$post = json_decode($postdata);
$reactProps = $post->reactionProps->properties;

$reactionCount = json_decode(request(getCount('gdbo:SolventReaction')));
//$reactionCount = $reactionCount->results;
$reactionCount = $reactionCount->results->bindings[0]->count->value;


$solvent = $reactProps[0]->value;
$ratio = $reactProps[1]->value;
$cgc = $reactProps[3]->value;
$unit = $reactProps[3]->unit;
//$solubility = $reactProps[2]->value; note: will need this parameter to hold the value, call lookup funciton here.
//$rheology = $reactProps[2]->value;

$solventResult = json_decode(request(getChem($solvent)));
$solventResult = $solventResult->results->bindings[0]->solvent->value;


//var_dump($solventResult);
//RegEx to extract the chemNumber from the URI returned.
if($solventResult != NULL){
  preg_match("/(solv\d+)/", $solventResult, $matches );
  //echo $matches[0];
  $solventResult = 'gdb:'.$matches[0];

  //No need to insert new chemical
}else{
  $numSolvents = json_decode(request(getCount('gdbo:Solvent')));
  $numSolvents = $numSolvents->results->bindings[0]->count->value;

  $solventResult = insertSolvent($numSolvents, $solvent);
  //Need to insert new chemical
}
//Now insert the entire reaction

  echo insertReaction($reactionCount, $solventResult, $ratio, $cgc, $solubility, $rheology);

function insertReaction($num, $solventResult, $ratio, $cgc, $solubility, $rheology){
  $num = $num + 1;
  $reactionID = 'gdb:reaction'.$num;//This is what needs to be returned to the entry page


  //Ratio not structred in yet, nor is notes here. cgc needs sorted when I know how to handle it
  $query = "PREFIX gdbo: <http://example.com/GelsOntology/>
            PREFIX gdb: <http://example.com/gdb/>
            INSERT DATA{ $reactionID a gdbo:SolventReaction ;
                                      gdbo:hasSolvent $solventResult;
                                      gdbo:hasCGC [ gdbo:hasValue '$cgc';
                                    			gdbo:hasUnit '$unit'
                                    			];
                                      gdbo:solubility '$solubility';}";

  $query = urlencode($query);
  $url = 'http://localhost:3030/gdb/?update='.$query;//change to gdb when ready
  requestPost($url);
  return $reactionID;
}

function insertSolvent($num, $name){
  $num = $num + 1;
  $newSolvID = 'gdb:solv'.$num;



  $query = "PREFIX gdbo: <http://example.com/GelsOntology/>
            PREFIX gdb: <http://example.com/gdb/>
            INSERT DATA{ $newSolvID a gdbo:Solvent ;
            				gdbo:name '$name'}";

  $query = urlencode($query);
  $url = 'http://localhost:3030/gdb/?update='.$query;//change to gdb when ready

  requestPost($url);

  return $newSolvID;
}


 ?>
