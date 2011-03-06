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

require_once(dirname(__FILE__) . "/../init/init.php");

class HttpUtils {

	public static function parseQueryString($str) {
		$op = array();
		$pairs = explode("&", $str);
		foreach ($pairs as $pair) {
			list($k, $v) = array_map("urldecode", explode("=", $pair));
			$op[$k] = $v;
		}
		return $op;
	}

	public static function isLocal() {
		$ip = self::getRealIpAddr();
		return $ip == "127.0.0.1" || $ip == "::1";
	}

	public static function getHeader($name) {
		$headers = apache_request_headers();
		foreach (array_keys($headers) as $header) {
			if ($header == $name) {
				return $headers[$header];
			}
		}
	}

	public static function makeHttpRequest($url) {
		$response = @file_get_contents($url);
		return $response;
	}

	private static function getRealIpAddr()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}
