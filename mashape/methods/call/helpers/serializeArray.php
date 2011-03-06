<?php

/*
 * Mashape PHP library.
 *
 * Copyright (C) 2011 Mashape, Inc.
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

require_once(dirname(__FILE__) . "/../../../configuration/restConfigurationLoader.php");
require_once(dirname(__FILE__) . "/../../../json/jsonUtils.php");
require_once(dirname(__FILE__) . "/../../../utils/arrayUtils.php");
require_once(dirname(__FILE__) . "/serializeObject.php");

function serializeArray($result, $instance, $isSimpleResult, $serverKey) {
	$json = "";
	if (is_array($result)) {
		if (ArrayUtils::isAssociative($result)) {
			$json .= "{";
			foreach ($result as $key => $value) {
				$json .= '"' . $key . '":';
				if (is_object($value)) {
					$json .= serializeObject($value, $instance, false, $serverKey);
				} else {
					if (is_array($value)) {
						$json .= serializeArray($value, $instance, $isSimpleResult, $serverKey);
					} else {
						$json .= serializeObject($value, $instance, !is_object($value), $serverKey);
					}
				}
				$json .= ",";
			}
		} else {
			$json .= "[";
			for ($i=0;$i<count($result);$i++) {
				$json .= serializeObject($result[$i], $instance, $isSimpleResult, $serverKey) . ",";
			}
		}
		$json = JsonUtils::removeLastChar($result, $json);
		if (ArrayUtils::isAssociative($result)) {
			$json .= "}";
		} else {
			$json .= "]";
		}
		
	}
	return $json;
}


function checkIfSimpleResult($value) {
	//if (is_string($value) || is_bool($value) || is_numeric($value) ||  )
}
