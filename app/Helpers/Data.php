<?php
namespace Helpers;

/*
 * data Helper - common data lookup methods
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 1.0
 * @date March 28, 2015
 * @date May 18 2015
 */
class Data
{

  public static function unserialize( $data ) {
		if ( self::isSerialized( $data ) ) // don't attempt to unserialize data that wasn't serialized going in
			return unserialize( $data );
		return $data;
	}

	/**
	 * Check value to find if it was serialized.
	 *
	 * If $data is not an string, then returned value will always be false.
	 * Serialized data is always a string.
	 *
	 *
	 *
	 * @param string $data   Value to check to see if was serialized.
	 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
	 * @return bool False if not serialized and true if it was.
	 */
	public static function isSerialized( $data, $strict = true ) {
		// if it isn't a string, it isn't serialized.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
	 	if ( 'N;' == $data ) {
			return true;
		}
		if ( strlen( $data ) < 4 ) {
			return false;
		}
		if ( ':' !== $data[1] ) {
			return false;
		}
		if ( $strict ) {
			$lastc = substr( $data, -1 );
			if ( ';' !== $lastc && '}' !== $lastc ) {
				return false;
			}
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace )
				return false;
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 )
				return false;
			if ( false !== $brace && $brace < 4 )
				return false;
		}
		$token = $data[0];
		switch ( $token ) {
			case 's' :
				if ( $strict ) {
					if ( '"' !== substr( $data, -2, 1 ) ) {
						return false;
					}
				} elseif ( false === strpos( $data, '"' ) ) {
					return false;
				}
				// or else fall through
			case 'a' :
			case 'O' :
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b' :
			case 'i' :
			case 'd' :
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
		}
		return false;
	}

	/**
	 * Check whether serialized data is of string type.
	 *
	*
	 *
	 * @param string $data Serialized data.
	 * @return bool False if not a serialized string, true if it is.
	 */
	public static function isSerializedString( $data ) {
		// if it isn't a string, it isn't a serialized string.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( strlen( $data ) < 4 ) {
			return false;
		} elseif ( ':' !== $data[1] ) {
			return false;
		} elseif ( ';' !== substr( $data, -1 ) ) {
			return false;
		} elseif ( $data[0] !== 's' ) {
			return false;
		} elseif ( '"' !== substr( $data, -2, 1 ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Serialize data, if needed.
	 *
	 *
	 *
	 * @param string|array|object $data Data that might be serialized.
	 * @return mixed A scalar data
	 */
	public static function serialize( $data ) {
		if ( is_array( $data ) || is_object( $data ) )
			return serialize( $data );

		// Double serialization is required for backward compatibility.
		// See https://core.trac.wordpress.org/ticket/12930
		if ( self::isSerialized( $data, false ) )
			return serialize( $data );

		return $data;
	}

    /**
     * print_r call wrapped in pre tags
     * @param  string or array $data
     */
    public static function pr($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    /**
     * var_dump call
     * @param  string or array $data
     */
    public static function vd($data)
    {
        var_dump($data);
    }

    /**
     * strlen call - count the lengh of the string
     * @param  string $data
     * @return string return the count
     */
    public static function sl($data)
    {
        return strlen($data);
    }

    /**
     * strtoupper - convert string to uppercase
     * @param  string $data
     * @return string
     */
    public static function sup($data)
    {
        return strtoupper($data);
    }

    /**
     * strtolower - convert string to lowercase
     * @param  string $data
     * @return string
     */
    public static function slw($data)
    {
        return strtolower($data);
    }

    /**
     * ucwords - the first letter of each word to be a capital
     * @param  string $data
     * @return string
     */
    public static function ucw($data)
    {
        return ucwords($data);
    }
}
