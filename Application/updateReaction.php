<?php

include 'requestMethod.php';

$postdata = file_get_contents("php://input");
$post = json_decode($postdata);

$reaction = $post->reaction;

preg_match("/(reaction\d+)/", $reaction, $matches );
$reaction = 'gdb:'.$matches[0];

$original = $post->original;
$name = $post->hasSolventName;
$SMILES = $post->hasSolventSMILES;
$cgc = $post->hasCGC;
$unit = $post->unit;
//$rheology = $post->hasRheology;
$notes = $post->notes;
//$solubility = lookupFunction(); remember to code in lookup function, and include this parameter in the query below.

//PREFIX qudte: <http://www.example.com/units/> Incase I need it
$currentSolvent = json_decode(request(getChem($original)));

$currentSolvent = $currentSolvent->results->bindings[0]->solvent->value;
preg_match("/(solv\d+)/", $currentSolvent, $matches2 );
$currentSolvent = 'gdb:'.$matches2[0];

//Ready to delete and now insert the updated parameters.

$queryDelReaction = "PREFIX gdbo: <http://example.com/GelsOntology/>
                     PREFIX gdb: <http://example.com/gdb/>

          DELETE {?s ?p ?o} WHERE{
                    ?s ?p ?o .
                    FILTER(?s = $reaction)
          }";

      $queryDelReaction = urlencode($queryDelReaction);
      $url1 = 'http://localhost:3030/gdb/?update='.$queryDelReaction;//change to gdb when ready
      requestPost($url1);

$queryDelSolvent = " PREFIX gdbo: <http://example.com/GelsOntology/>
                      PREFIX gdb: <http://example.com/gdb/>

                      DELETE {?s ?p ?o} WHERE {
                        ?s a gdbo:Solvent .
                        ?s gdbo:name '$original' .
                        ?s ?p ?o
}";

      $queryDelSolvent = urlencode($queryDelSolvent);
      $url2 = 'http://localhost:3030/gdb/?update='.$queryDelSolvent;
      requestPost($url2);



$queryInsert = "PREFIX gdbo: <http://example.com/GelsOntology/>
                PREFIX gdb: <http://example.com/gdb/>
                PREFIX UO: <http://purl.obolibrary.org/obo/uo/>
                PREFIX EFO: <http://www.ebi.ac.uk/efo/>

                INSERT DATA{
                    $reaction a gdbo:SolventReaction;
                    gdbo:hasSolvent $currentSolvent;
                    gdbo:hasCGC [ gdbo:hasValue '$cgc';
                  			gdbo:hasUnit '$unit'
                  			];
                  	gdbo:notes '$notes' .

                    $currentSolvent a gdbo:Solvent;
                      gdbo:name '$name';
                      gdbo:SMILES '$SMILES'
                }";



                $queryInsert = urlencode($queryInsert);
                $url3 = 'http://localhost:3030/gdb/?update='.$queryInsert;
                echo $url3;
                requestPost($url3);

//FUNCTION WRITTEN, WILL PROBABLY NEED DEBUGGED THOUGH

?>
