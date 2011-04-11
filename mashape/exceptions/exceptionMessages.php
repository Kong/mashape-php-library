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

define("EXCEPTION_GENERIC_LIBRARY_ERROR_CODE", 1000);
define("EXCEPTION_EXPECTED_ARRAY_RESULT_SIMPLE", "The result value it's not an array although it's described it would have been an array, please check your XML file");
define("EXCEPTION_EXPECTED_ARRAY_RESULT", "The result value of field \"%s\" in object \"%s\" it's not an array although it's described it would have been an array, please check your XML file");
define("EXCEPTION_UNEXPECTED_ARRAY_RESULT_SIMPLE", "The result value it's an array although it was described it wouldn't have been an array, please check your XML file");
define("EXCEPTION_UNEXPECTED_ARRAY_RESULT", "The result value of field \"%s\" in object \"%s\" it's an array although it was described it wouldn't have been an array, please check your XML file");
define("EXCEPTION_UNKNOWN_OBJECT", "The result can't be serialized because it's of an unknown type \"%s\" not described in the XML file");

define("EXCEPTION_XML_CODE", 1001);
// Error messages for XML configuration
define("EXCEPTION_CONFIGURATION_FILE_NOTFOUND", "Can't find the XML configuration file (path: \"%s\"). Please check that the path is valid and it exists");
define("EXCEPTION_METHOD_EMPTY_NAME", "Methods can't have an empty \"name\" attribute");
define("EXCEPTION_METHOD_DUPLICATE_NAME", "A method with name \"%s\" has already been described");
define("EXCEPTION_METHOD_DUPLICATE_ROUTE", "A method with route \"%s\" has already been described");
define("EXCEPTION_METHOD_EMPTY_HTTP", "Methods can't have an empty \"http\" attribute");
define("EXCEPTION_METHOD_INVALID_HTTP", "Http method \"%s\" not supported");
define("EXCEPTION_METHOD_INVALID_ROUTE", "Route \"%s\" is invalid");
define("EXCEPTION_METHOD_INVALID_ROUTE_PARAM", "Can't find the route param \"%s\" in the method signature");
define("EXCEPTION_METHOD_OPTIONAL_ROUTE_PARAM", "You can't set the optional parameter \"%s\" belonging to the method \"%s\" as a route parameter. Optional parameters should never been included in a route URL");
define("EXCEPTION_RESULT_MULTIPLE", "The method \"%s\" has multiple result nodes. Only one is allowed");
define("EXCEPTION_RESULT_MISSING", "The method \"%s\" requires a result child element");
define("EXCEPTION_RESULT_EMPTY_TYPE", "Please enter a result \"type\" attribute for method \"%s\"");
define("EXCEPTION_RESULT_EMPTY_NAME_SIMPLE", "Please enter a result \"name\" attribute for method \"%s\", that is the name of the field that will contain the result value");
define("EXCEPTION_RESULT_EMPTY_NAME_OBJECT", "Please enter a result \"name\" attribute for method \"%s\", that is the class name belonging to the object that will represent the result value");
define("EXCEPTION_RESULT_INVALID_TYPE", "Invalid result type \"%s\" for method \"%s\"");
define("EXCEPTION_OBJECT_EMPTY_CLASS", "Objects can't have an empty \"class\" attribute");
define("EXCEPTION_OBJECT_DUPLICATE_CLASS", "An object with class \"%s\" has already been described");
define("EXCEPTION_EMPTY_FIELDS", "Please add at least one field to object: %s");
define("EXCEPTION_FIELD_EMPTY_NAME", "Fields can't have an empty \"name\" attribute");
define("EXCEPTION_FIELD_NAME_DUPLICATE", "A field with name \"%s\" has already been described for object \"%s\"");
define("EXCEPTION_EMPTY_SERVERKEY", "The server-key is missing");
define("EXCEPTION_MISSING_OBJECTS", "Missing XML description for objects: %s");

define("EXCEPTION_INVALID_HTTPMETHOD_CODE", 1002);
define("EXCEPTION_INVALID_HTTPMETHOD", "The requested method doesn't support this HTTP Method provided");

define("EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE", 1003);
define("EXCEPTION_NOTSUPPORTED_HTTPMETHOD", "Http Method not supported. Only DELETE, GET, POST, PUT are supported");

define("EXCEPTION_NOTSUPPORTED_OPERATION_CODE", 1004);
define("EXCEPTION_NOTSUPPORTED_OPERATION", "Operation not supported");

define("EXCEPTION_METHOD_NOTFOUND_CODE", 1006);
define("EXCEPTION_METHOD_NOTFOUND", "The method requested was not found: \"%s\"");

define("EXCEPTION_AUTH_INVALID_CODE", 1007);
define("EXCEPTION_AUTH_INVALID", "The request has not been authorized");

define("EXCEPTION_AUTH_INVALID_SERVERKEY_CODE", 1005);
define("EXCEPTION_AUTH_INVALID_SERVERKEY", "The request can't be authenticated because the server key sent for the request, and the one set in your implementation, don't match");

define("EXCEPTION_REQUIRED_PARAMETERS_CODE", 1008);
define("EXCEPTION_REQUIRED_PARAMETERS", "Some parameters required by the method are missing");

define("EXCEPTION_REQUIRED_PARAMETER", "Missing required parameter \"%s\"");

// http://api.mashape.com Error codes

define("EXCEPTION_FIELD_NOTFOUND", "Can't find the property \"%s\"");
define("EXCEPTION_INSTANCE_NULL", "Please verify the class you're initializing with 'MashapeHandler::handleApi(..)' exists");
define("EXCEPTION_EMPTY_REQUEST", "A request attempt was made to Mashape, but the response was empty. The firewall may be blocking outbound HTTP requests");
define("EXCEPTION_JSONDECODE_REQUEST", "Can't deserialize the response JSON from Mashape. The json_decode function is missing on server");
define("EXCEPTION_INVALID_CALLBACK", "Invalid function name set as a callback");
define("EXCEPTION_INVALID_PERMISSION", "File permission denied: can't create and write to the file %s");
define("EXCEPTION_AMBIGUOUS_ROUTE", "Some routes are ambiguous and very similar, please verify the methods: %s");

define("EXCEPTION_INVALID_APIKEY_CODE", 2001);
define("EXCEPTION_EXCEEDED_LIMIT_CODE", 2002);
define("EXCEPTION_SYSTEM_ERROR_CODE", 2000);
