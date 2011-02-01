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

require_once(dirname(__FILE__) . "/../../../exceptions/mashapeException.php");
require_once(dirname(__FILE__) . "/../../../configuration/restConfigurationLoader.php");
require_once(dirname(__FILE__) . "/../../../utils/arrayUtils.php");

function validateCallParameters($method, $parameters, $instance) {
	$reflectedClass = new ReflectionClass(get_class($instance));
	$reflectedMethod = $reflectedClass->getMethod($method->getName());
	$reflectedParameters = $reflectedMethod->getParameters();

	$hasRequiredParams = false;
	foreach ($reflectedParameters as $reflectedParameter) {
		if (!$reflectedParameter->isOptional()) {
			$hasRequiredParams = true;
			break;
		}
	}

	if ($hasRequiredParams && (empty($parameters) || !is_array($parameters) || count($parameters) == 0)) {
		throw new MashapeException(EXCEPTION_REQUIRED_PARAMETERS, EXCEPTION_REQUIRED_PARAMETERS_CODE);
	}

	if (!empty($parameters)) {
		$keys = array_keys($parameters);
		for ($i = 0;$i<count($reflectedParameters);$i++) {
			$parameterName = $reflectedParameters[$i]->name;
			if (($i + 1) > count($parameters)) {
				if (!$reflectedParameters[$i]->isOptional()) {
					throw new MashapeException(sprintf(EXCEPTION_REQUIRED_PARAMETER, $parameterName), EXCEPTION_REQUIRED_PARAMETERS_CODE);
				}
			} else {
				if (in_array($parameterName, $keys) == false && $reflectedParameters[$i]->isOptional() == false) {
					throw new MashapeException(sprintf(EXCEPTION_REQUIRED_PARAMETER, $parameterName), EXCEPTION_REQUIRED_PARAMETERS_CODE);
				}
			}
		}
	}
	
	$callParameters = array();

	// Sort parameters
	if (!empty($reflectedParameters)) {
		$sorted = array();
		
		foreach ($reflectedParameters as $reflectedParameter) {
			$reflectedParameterName = $reflectedParameter->name;
			
			if (isset($parameters[$reflectedParameterName])) {
				$sorted[$reflectedParameterName] = $parameters[$reflectedParameterName];
			} else {
				$sorted[$reflectedParameterName] = $reflectedParameter->getDefaultValue();
			}
		}

		$callParameters = $sorted;
	}

	return $callParameters;
}

?>