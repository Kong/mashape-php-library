<?php

class XmlParserUtils {

	public static function existAttribute($obj, $attribute) {
		return array_key_exists($attribute, $obj->tagAttrs);
	}

	public static function getChildren($document, $tagName) {
		$result = array();
		foreach ($document->tagChildren as $child) {
			if ($child->tagName == $tagName) {
				array_push($result, $child);
			}
		}
		return $result;
	}

}

?>