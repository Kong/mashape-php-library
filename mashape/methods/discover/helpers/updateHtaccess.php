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

function updateHtaccess($instance, $methods) {
	$reflectedClass = new ReflectionClass(get_class($instance));
	$implPath = $reflectedClass->getParentClass()->getProperty("dirPath")->getValue($instance);

	$fileParts = Explode('/', $_SERVER["PHP_SELF"]);
	$scriptName = $fileParts[count($fileParts) - 1];
	
	$fhandle = fopen($implPath . "/.htaccess", "w");
	fwrite($fhandle, "RewriteEngine On\n");
	unset($fileParts[count($fileParts) - 1]);
	$basePath = (count($fileParts) == 1 && empty($fileParts[0])) ? "/" : implode("/", $fileParts);
	fwrite($fhandle, "RewriteBase " . $basePath . "\n");
	fwrite($fhandle, "RewriteRule ^(.*)$ " . $scriptName . " [QSA,L]");
	fclose($fhandle);
}

function emptyMatches($matches) {
	if (empty($matches)) {
		return true;
	} else {
		foreach ($matches as $match) {
			if (!empty($match)) {
				return false;
			}
		}
	}
	return true;
}

function parseRoute($route) {
	$pattern = '/\{(\w+)\}/';
	preg_match_all($pattern, $route, $matches, PREG_OFFSET_CAPTURE);
	return $matches;
}

?>