<?php

namespace Helpers;

class stdObject {
	public function __construct(array $arguments = array()) {
		if (!empty($arguments)) {
			foreach ($arguments as $property => $argument) {
				$this->{$property} = $argument;
			}
		}
	}

	public function __call($method, $arguments) {
		$arguments = array_merge(array("stdObject" => $this), $arguments); // Note: method argument 0 will always referred to the main class ($this).
		if (isset($this->{$method}) && is_callable($this->{$method})) {
			return call_user_func_array($this->{$method}, $arguments);
		} else {
			throw new Exception("Fatal error: Call to undefined method stdObject::{$method}()");
		}
	}
}

/*
 * GeoName Helper - collection of methods for working with GeoName
 *
 * @author Enner Pérez - ennerperez@gmail.com
 * @version 1.0
 * @date 2015-07-26
 */
class GeoName {
	
	// Function to convert CSV into associative array
	protected static function csvToArray($data) {
		
		$array = array();
		
		$lineArray = preg_split ( "/[\r\n]+/", $data );
		for($j = 0; $j < count ( $lineArray ); $j ++) {
			$subarray = preg_split ( "/[\t]/", $lineArray [$j] );
			
			$array [$j] = new stdObject();
			
			$array [$j]->geonameid = $subarray [0];
			$array [$j]->name = $subarray [1];
			$array [$j]->asciiname = $subarray [2];
			$array [$j]->alternatenames = $subarray [3];
			$array [$j]->latitude = $subarray [4];
			$array [$j]->longitude = $subarray [5];
			$array [$j]->feature_class = $subarray [6];
			$array [$j]->feature_code = $subarray [7];
			$array [$j]->country_code = $subarray [8];
			$array [$j]->cc2 = $subarray [9];
			$array [$j]->admin1 = $subarray [10];
			$array [$j]->admin2 = $subarray [11];
			$array [$j]->admin3 = $subarray [12];
			$array [$j]->admin4 = $subarray [13];
			$array [$j]->population = $subarray [14];
			$array [$j]->elevation = $subarray [15];
			$array [$j]->dem = $subarray [16];
			$array [$j]->timezone = $subarray [17];
			$array [$j]->modification = $subarray [18];
		}
		
		return $array;
	}
	public static function getGeoName($file = COUNTRY_CODE.'.txt') {
		$searchfor = $_GET ['s'];
		
		// the following line prevents the browser from parsing this as HTML.
		header ( 'Content-Type: text/plain' );
		
		if (! is_null ( $searchfor )) {
			
			// get the file contents, assuming the file to be readable (and exist)
			$contents = file_get_contents ( __DIR__ . '/GeoNames/' . $file );
			
			// escape special characters in the query
			$pattern = preg_quote ( $searchfor, '/' );
			// finalise the regular expression, matching the whole line
			$pattern = "/^.*$pattern.*\$/m";
			// search, and store all matching occurences in $matches
			
			if (preg_match_all ( $pattern, $contents, $matches )) {
				$data = implode ( "\n", $matches [0] );
				$array = GeoName::csvToArray ( $data );
			}
		}
		
		if (isset ( $_GET ["fc"] )) {
			$result = array_filter ( $array, function ($k) {
				return ($k->feature_code == $_GET ["fc"]);
			} );
		} elseif (isset ( $_GET ["a1"] )) {
			$result = array_filter ( $array, function ($k) {
				return ($k->admin1 == $_GET ["a1"]);
			} );
		} elseif (isset ( $_GET ["a2"] )) {
			$result = array_filter ( $array, function ($k) {
				return ($k->admin2 == $_GET ["a2"]);
			} );
		} else {
			$result = $array;
		}
		
		if (sizeof ( $result ) > 0) {
			header ( 'Content-Type: application/json' );
			echo json_encode ( $result );
		}
	}
}
