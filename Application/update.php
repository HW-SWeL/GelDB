<?php

    include 'requestMethod.php';

    $postdata = file_get_contents("php://input");
    $post = json_decode($postdata);

    $chem = $post->chem;

    preg_match("/(chem\d+)/", $chem, $matches );
    $chem = 'gdb:'.$matches[0];

    $name = $post->name;
    $InChI = $post->InChI;
    $formula = $post->formula;
    $SMILES = $post->SMILES;
    $morphology = $post->morph;
    $trigger = $post->triggerMech;

    $reactions = $post->reactions;

    function formTriples(&$value,$key){
      $value = "gdbo:solventReaction <$value>";
    }

//    print_r($reactions);

    for($item = 0; $item < sizeof($reactions); $item++){
      $reactions[$item] = $reactions[$item]->_about;
      print_r( $reactions[$item] );
    }

    array_walk($reactions,"formTriples");
    $reactions = implode(" ; ", $reactions);

    $queryDelete = "PREFIX gdbo: <http://example.com/GelsOntology/>
                         PREFIX gdb: <http://example.com/gdb/>

              DELETE {?s ?p ?o} WHERE{
                        ?s ?p ?o .
                        FILTER(?s = $chem)
              }";

    $queryDelete = urlencode($queryDelete);
    $url1 = 'http://localhost:3030/gdb/?update='.$queryDelete;
    requestPost($url1);

    $queryInsert = "PREFIX gdbo: <http://example.com/GelsOntology/>
                    PREFIX gdb: <http://example.com/gdb/>
                    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

                    INSERT DATA{
                            $chem a gdbo:Gel;
                                gdbo:InChI '$InChI';
                                gdbo:name '$name';
                                rdfs:label '$name';
                                gdbo:formula '$formula' ;
                                gdbo:SMILES '$SMILES';
                                gdbo:morph '$morphology';
                                gdbo:triggerMech '$trigger';
                                $reactions}";

    echo $queryInsert;
    $queryInsert = urlencode($queryInsert);
    $url2 = 'http://localhost:3030/gdb/?update='.$queryInsert;

    //echo $url2;

    requestPost($url2);

    //Probable debugging required, but script is complete

 ?>
