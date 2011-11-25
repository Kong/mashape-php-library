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
       if (is_array($val) || is_object($val)) { // handle arrays/objects

        foreach ($val as $skey => $sval) {
          if($currentKey !== $key) $nNode = $this->dom->createElement($key);
          $node->appendChild($this->Parse(array($skey => $sval), $nNode));
          $currentKey = $key;
        }

       } else {
        // fix boolean output
        if($val === true) {
          $val = "true";
        } elseif($val === false) {
          $val = "false";
        }
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

$jsonStr = '{"version":"1.2","url":"http:\/\/worldtimeengine.com\/current\/10_10","location":{"region":"Nigeria","latitude":10,"longitude":10},"summary":{"utc":"2011-11-25 09:52:10","local":"2011-11-25 10:52:10","hasDst":true},"current":{"abbreviation":"WAT","description":"West Africa Time","utcOffset":"+1:00"}}';
$json = json_decode($jsonStr);

$xmlgen = new XMLGenerator($json, 'timezone');
echo $xmlgen->toXML();
*/