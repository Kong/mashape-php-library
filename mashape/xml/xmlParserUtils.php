<?php

/*
 * Mashape PHP library.
 *
 * Copyright (C) 2010 Mashape, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * The author of this software is Mashape, Inc.
 * For any question or feedback please contact us at: support@mashape.com
 *
 */

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
