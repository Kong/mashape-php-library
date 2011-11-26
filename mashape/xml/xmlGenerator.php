<?php
 /**
 * Mashape JSON to XML Generator
 *
 * Copyright Richard Tibbett, 2011
 */
class XMLGenerator {
  
  var $json;
  
  var $rootName;
  
  function XMLGenerator($json, $rootName = 'result') {
    $this->json = $json;
    $this->rootName = $rootName;
    
    $this->dom = new DOMDocument('1.0', 'utf-8');
  }
  
  public function toXML() {
    $body = $this->Parse($this->json);
    return $body;
  }
  
  private function Parse($json, $node = false) {
    $root = false;
    if (empty($node)) {
     $node = $this->dom->createElement($this->rootName);
     $root = true;
    }

    foreach ($json as $key => $val) {   
       if (is_array($val)) { // handle arrays

        foreach ($val as $skey => $sval) {
          if($currentKey !== $key) $nNode = $this->dom->createElement($key);
          $node->appendChild($this->Parse(array($skey => $sval), $nNode));
          $currentKey = $key;
        }

       } else {
        $node->appendChild($this->dom->createElement($key, $val));
       }
    }

    if ($root == true) {
     $this->dom->appendChild($node);
     return $this->dom->saveXML(); 
    } else {
     return $node;
    }
  }
  
}

/*
// TEST

$wteJson = array(
  "version" => "1.2",
  "url" => "http:\/\/worldtimeengine.com\/current\/-0.1_51",
  "location" => array(
    "region" => "United Kingdom",
    "latitude" => "51",
    "longitude" => "-0.1"
  ),
  "summary" => array(
    "utc" => "2011-11-24 22:45:53",
    "local" => "2011-11-24 22:45:53"
  )
);

$xmlgen = new XMLGenerator($wteJson, 'timezone');
echo $xmlgen->toXML();
*/