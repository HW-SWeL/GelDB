<?php

  include 'requestMethod.php';
  include 'hansenValues.php';

  $postdata = file_get_contents("php://input");
  $post = json_decode($postdata);
  $dat = $post->ss;
  //$dat is the search string



  echo search($dat);

  /*

      If $str is an integer, call a function to search hansen values
      If it's a string, call functions for Name/other names/solvents used etc.
      If no result is found for Name, call other names query, etc.
      Always need to ensure result isn't blank (check items isn't empty array)
      Union search for name/other names. Independant search for solvents.

  */



  function check($dat, $eldaURL, $flag){
    //$eldaURL = 'http://localhost:8080/elda-common/try.json?resource=';
    $searchURL = $eldaURL.urlencode($dat);
    $tempResult = request($searchURL);
    $tempResult = json_decode($tempResult);
    if(isNull($tempResult)){
      return false;
    }else{
      $tempResult = json_encode($tempResult);
      $tempResult = json_decode($tempResult, true);//Array is now in a usable form, call function to Elda for pubChem?

      if($flag == 'name'){
        $tempResult = externalCalls($tempResult);
      }

      $tempResult['flag']=$flag;//This is how a new item is added, will need to do this for the results from pubChem calls
      return json_encode($tempResult);
    }
  }


  function isNull($dat){
    if( empty($dat->result->items)){
      return true;
    }else{
      return false;
    }
  }

  function externalCalls($currentData){
    include 'hansenValues.php';
    //This funciton will make an elda request, which will retrieve data from pubChem.
    //Is elda request necessary? just send request to the endpoint directly?
    $key = $currentData["result"]["items"][0]["InChI"];

    //$key = 'RZJQGNCSTQAWON-UHFFFAOYSA-N';

    $extCallsUrl = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/inchikey/'.$key.'/PNG';

    $res = request($extCallsUrl);

    $currentData['structure'] = 'data:image/png;base64,'.base64_encode($res);

    $react = $currentData["result"]["items"][0]["solventReaction"];

    //$currentData['structure'] = $k;
    //$currentData['structure'] = $react;
    for ($x = 0; $x < sizeof($react); $x++) {
      $k = $react[$x]["hasSolvent"]["name"];
      $currentData["result"]["items"][0]["solventReaction"][$x]["solubility"] = $hansen[$k];
    }

    return $currentData;
  }

  function search($dat){
    $nameURL = 'http://localhost:8080/elda-common/try.json?resource=';
    $nameFlag = 'name';
    $solventURL = 'http://localhost:8080/elda-common/solvent.json?resource=';
    $solventFlag = 'solvent';
    $hansenURL = 'http://localhost:8080/elda-common/hansen.json?resource=';
    $hansenFlag = 'hansen value';

    switch(true){
      case is_string($dat):
        if(check($dat, $nameURL, $nameFlag) != false){
          //Find way to return flag
          return check($dat, $nameURL, $nameFlag);
          break;
        }elseif(check($dat, $solventURL, $solventFlag) != false){
          //Find way to return flag
          return check($dat, $solventURL, $solventFlag);
          break;
        }else{
           //return an error
           return "test";
           break;
        }
        case is_int($dat):
          //Do int stuff
          return "entered an int";
          break;

        default:
          return "Not a valid search query";
        }

      }


 ?>
