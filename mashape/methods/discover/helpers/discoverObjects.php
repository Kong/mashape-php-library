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

require_once(dirname(__FILE__) . "/../../../json/jsonUtils.php");
require_once(dirname(__FILE__) . "/../../../exceptions/mashapeException.php");

function discoverObjects($configuration, $objectsFound) {
	$result = '"objects":[';
	$objects = $configuration->getObjects();
	
	$objectsDone = array();
	
	foreach ($objects as $object) {
		$result .= "{";

		$className = $object->getClassName();
		$result .= '"class":"' . $className . '",';
		$result .= '"fields":[';
		foreach($object->getFields() as $field) {
			$result .= "{";

			$fieldName = $field->getName();
			$result .= '"name":"' . $fieldName . '",';
			$objectName = $field->getObject();
			if ($objectName != null) {
				if (!in_array($objectName, $objectsFound) && !in_array($objectName, $objectsDone)) {
					array_push($objectsFound, $objectName);
				}
			}
			$result .= '"object":' . (empty($objectName) ? "null" : '"' . $objectName . '"') . ',';
			$methodName = $field->getMethod();
			$result .= '"method":' . (empty($methodName) ? "null" : '"' . $methodName . '"') . ',';
			$result .= '"array":' . ($field->isArray() ? "true" : "false") . ",";
			$result .= '"optional":' . ($field->isOptional() ? "true" : "false");

			$result .= "},";
		}
		// Remove the last comma
		$result = JsonUtils::removeLastChar($object->getFields(), $result);

		$result .= ']';
		$result .= "},";
		
		array_push($objectsDone, $className);
		
		$objectsFound = array_diff($objectsFound, array($className));
	}
	// Remove the last comma
	$result = JsonUtils::removeLastChar($objects, $result);
	$result .= ']';
	
	// Check that all objects exist
	if (!empty($objectsFound)) {
		$missingObjects = "";
		foreach($objectsFound as $requiredObject) {
			$missingObjects .= $requiredObject . ",";
		}
		$missingObjects = JsonUtils::removeLastChar($objectsFound, $missingObjects);
		throw new MashapeException(sprintf(EXCEPTION_MISSING_OBJECTS, $missingObjects), EXCEPTION_XML_CODE);
	}
	
	return $result;
}

?>