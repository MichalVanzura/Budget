<?php

function isDevelopement() {
    return ENVIRONMENT == "development";
}

/**
 * Returns url prefix for current language
 * @return string
 */
function base_language_url() {
    $lang = LanguageLoader::currentLanguage(true);

    if ($lang == "cz") {
        return "";
    }

    return $lang . "/";
}

function thirdPartyFolder($name) {
    return base_url() . "third_party/$name/";
}

/**
 * Functions returns javascript file name with js prefix in folder js.
 * If ENVIRONMENT is production, it will add .min before .js
 * @param string $file fileName without js
 * @param string $path path to the javascript file in folder (def. js/)
 * @return string
 * @example js/text.js development ENVIRONMENT
 * @example js/text.min.js production ENVIRONMENT
 */
function javascriptFileName($file, $path = "") {
    return fileNameForExtension($file, $path, "js");
}

/**
 * Functions returns CSS file name with js prefix in folder css.
 * If ENVIRONMENT is production, it will add .min before .css
 * @param string $file fileName without css
 * @param string $path path to the javascript file in folder (def. css/)
 * @return string
 * @example css/text.css development ENVIRONMENT
 * @example css/text.min.css production ENVIRONMENT
 */
function cssFileName($file, $path = "") {
    return fileNameForExtension($file, $path, "css");
}

/**
 * Functions returns file name with extension prefix in given folder.
 * If ENVIRONMENT is production, it will add .min before .extension
 * @param string $file fileName without extension
 * @param string $path path to the file in folder
 * @param string $extension without .
 * @return string
 * @example js/text.js development ENVIRONMENT
 * @example js/text.min.js production ENVIRONMENT
 */
function fileNameForExtension($file, $path, $extension) {
    if (!isDevelopement()) {
        $file .= ".min";
    }
    return $path . $file . "." . $extension;
}

function truncate($text, $chars = 25) {
    $text = $text . " ";
    $text = substr($text, 0, $chars);
    $text = substr($text, 0, strrpos($text, ' '));
    $text = $text . "...";
    return $text;
}

/**
 * Detects if request method is post.
 * @return BOOL
 */
function isPostRequest() {
    return (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST");
}

function postOrNull($post, $name) {
    if (isset($post[$name])) {
        return $post[$name];
    }
    return false;
}

/**
 * Throws an exception with message and code if value is null
 * @param type $value
 * @param type $message missing_parameter
 * @param type $code
 * @throws Exception
 * @return true if value is ok
 */
function throwIfNull($value, $message = "missing_parameter", $code = 400) {
    if (is_null($value)) {
        throw new IMException($message, $code);
    }
    return true;
}

/**
 * Throws an exception with message and code if string has no length
 * @param type $value is trimed
 * @param type $message empty_string
 * @param type $code 
 * @throws Exception
 * @return true if value is ok
 */
function throwIfEmptyString($value, $message = "empty_string", $code = 400) {
    if (trim($value) === "") {
        throw new IMException($message, $code);
    }
    return true;
}

/**
 * Throws an exception with message and code if value is not numeric
 * @param type $value
 * @param type $message not_numeric
 * @param type $code 400
 * @return true
 * @throws Exception
 */
function throwIfNotNumeric($value, $message = "not_numeric", $code = 400) {
    if (!is_numeric($value)) {
        throw new IMException($message, $code);
    }
    return true;
}

/**
 * Throws an exception when value is not true (checks if boolean, if not no throw)
 * @param type $value to be tested
 * @param type $message optional
 * @param type $code optional
 * @return boolean
 * @throws Exception
 */
function throwIfFalse($value, $message = "not_true", $code = 400) {
    if (is_bool($value) && !$value) {
        throw new IMException($message, $code);
    }
    return true;
}

/**
 * Throws an exception with message and code if value is not boolean
 * @param type $value
 * @param type $message not_booelan
 * @param type $code 400
 * @return true
 * @throws Exception
 */
function throwIfNotBOOL($value, $message = "not_booelan", $code = 400) {

    if (!is_bool($value) && ($value != 0 && $value != 1)) {
        throw new IMException($message, $code);
    }
    return true;
}

/**
 * Throws an exception with message and code if array doesnt have value by given key
 * @param array $array of values
 * @param type $key key that we are looking for to exits
 * @param type $message not_booelan
 * @param type $code 400
 * @return value
 * @throws Exception
 */
function throwIfNotSetInArray(array $array, $key, $message = "not_in_array", $code = 400) {

    if (!array_key_exists($key, $array)) {
        if ($message === "not_in_array") {
            $message .= "_$key";
        }
        throw new IMException($message, $code);
    }
    
    if ($array[$key] == "null") {
        return null;
    }
    
    return $array[$key];
}

function throwIfNotSetInArrayAndNotEmpty(array $array, $key) {
    throwIfNotSetInArray($array, $key);
    throwIfEmptyString($array[$key],"empty_$key");
    return $array[$key];
}

function throwIfNotSetInArrayOrNotNumeric(array $array, $key) {
    throwIfNotSetInArray($array, $key);
    throwIfNotNumeric($array[$key], "not_numeric_$key");
    return $array[$key];
}

function throwIfNotSetInArrayOrNotNumericAndLarger(array $array, $key, $larger, $messageAdd = "") {
    $value = throwIfNotSetInArrayOrNotNumeric($array, $key);
    throwIfFalse($value > $larger, "must_be_bigger_$key".$messageAdd);
    return $value;
}

# Aditional functions

function isValidEmail($email) {
    return preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,4})$/", $email);
}

/**
 * Create groups of array by value of the item[valueToGroup]
 * @param array     $array
 * @param string    $valueToGroup   a name of call that will be grouped
 * @param function  $function       function($entry) that will parse entry and return it
 * @return array
 */
function groupArrayByValue($array, $valueToGroup, $function = null, $onlySingle = false) {
    $groupedArray = array();

    if (is_array($array) && !empty($array)) {
        foreach ($array as $entry) {
            $value  = $entry[$valueToGroup];
            if (!isset($groupedArray[$value])) {
                $groupedArray[$value] = array();
            }

            if (is_callable($function)) {
                $entry = $function($entry);
            }
            if ($onlySingle) {
                $groupedArray[$value] = $entry;
            } else {
                $groupedArray[$value][] = $entry;
            }
        }
    }

    return $groupedArray;
}

function postValueOrSavedValue($postKey, $savedData, $savedKey, $default = "") {
    
    if (is_array($postKey) && isset($_POST[$postKey[0]]) && isset($_POST[$postKey[0]][$postKey[1]])) {
        return $_POST[$postKey[0]][$postKey[1]];
    } else if (!is_array($postKey) && isset($_POST[$postKey])) {
        return $_POST[$postKey];
    } else if (isset($savedData[$savedKey])) {
        return $savedData[$savedKey];
    }

    return $default;
}

function formatAmount($amount) {
    return number_format($amount, 0, ',', ' ').' Kč';
}
