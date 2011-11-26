<?php
 /**
 * JSON to XML Generator / Convertor
 *
 * Author: Richard Tibbett, 2011
 */
class XMLGenerator {
  
  var $json;
  var $rootName;
  
  function XMLGenerator($json, $rootName = 'result') {
    $this->json = $json;
    $this->rootName = $rootName;
  }
  
  public function toXML() {
    try {
      $this->dom = new DOMDocument('1.0', 'utf-8');
      return $this->Parse($this->json);
    } catch(Exception $e) {}
    $this->dom = new DOMDocument('1.0', 'utf-8');
    $this->dom->appendChild( $this->dom->createElement($this->rootName) );
    return $this->dom->saveXML();
  }
  
  private function Parse($json, $node = false) {
    $root = false;
    if (empty($node)) {
     $node = $this->dom->createElement($this->rootName);
     $root = true;
    }
    foreach ($json as $key => $val) {
       if(is_array($val)) {
        foreach ($val as $skey => $sval) {
          if(is_object($sval)) {
            $node->appendChild($this->Parse($sval, $this->dom->createElement($key)));
          } else {
            $node->appendChild($this->dom->createElement($key, $sval));
          }
        } 
       } elseif (is_object($val)) {
        foreach ($val as $skey => $sval) {
          if(!isset($currentKey) || $key !== $currentKey) $nNode = $this->dom->createElement($key);
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
// - Enable this block and execute this file.

$jsonTestStr = '{"simpleval0":1.2,"simpleval1":"http:\/\/foo.com\/bar\/","objval":{"foo":"bar","baz":10},"arrayval":["50", "60", "70"],"arrayobjval":[{"foo":"bar","baz":30}, {"foo":"bar","baz":40}],"recursivetest":{"simpleval0":1.4,"simpleval1":"http:\/\/foo.com\/bar\/","objval":{"foo":"bar","baz":100},"arrayval":["500", "600", "700"],"arrayobjval":[{"foo":"bar","baz":300}, {"foo":"bar","baz":400}]}}';
$json = json_decode($jsonTestStr);

$xmlgen = new XMLGenerator($json, 'jsontoxml');
echo $xmlgen->toXML();
*/