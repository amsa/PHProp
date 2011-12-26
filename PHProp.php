<?php
/**
 * PHProp
 *
 * PHP Version 5.3
 * @author Amin Saeedi <amin.w3dev@gmail.com>
 * @copyright Copyright (c) 2009-2011. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GPL v3.0
 * @version 1.1
 */

/**
 * PHPRop 
 * Ini configuration parser with dependant variable support 
 * 
 * @package Configuration
 * @author Amin Saeedi <amin.w3dev@gmail.com>
 * @copyright Copyright (C) 2009-2011. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GPL v3.0
 * @version 1.1
 */
class PHPRop
{
    /**
     * @var IniObject
     */
    private $_obj;

    /**
     * @var mixed
     */
    private $_tmpValue;

    /**
     * @var string
     */
    private $_delimiter;

    /**
     * @var array
     */
    private $_varDeps = array();
    private static $_instance = null;

    /**
     * Initialization
     *
     * @param string ini filename
     * @param string delimiter
     */
    private function  __construct($iniFile, $delimiter) 
    {
        $this->_delimiter = $delimiter;
        $this->_obj = new iniObject();
        $this->_parseIni($iniFile);
    }

    /**
     * Singleton pattern constructor
     *
     * @param string ini filename
     * @param string delimiter, it is '.' by default but can be changed
     * @return object : object containing ini data
     */
    public static function parse($filename, $del=".")
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($filename, $del);
        }
        return self::$_instance->getObj();
        //return self::$_instance;
    }

    /**
     * Parses ini file and extract its keys and prepare it for object creation
     */
    private function _parseIni($iniFile)
    {
        $aParsedIni = parse_ini_file($iniFile, true, INI_SCANNER_RAW);
        $tmpArray = array();
        foreach ($aParsedIni as $key=>$value) { 
            if (strpos($key, ':') !== false) {
                $sections = explode(':', $key);
                if (count($sections) != 2) {
                    throw new Exception('Malformed section header!');
                }
                $currentSection = trim($sections[0]);
                $parentSection = trim($sections[1]);
                $value = array_merge(
                    $aParsedIni[$parentSection], 
                    $aParsedIni[$key]
                );
                $aParsedIni[$currentSection] = $value;
                unset($aParsedIni[$key]);
                $key = $currentSection;
            }
            if (is_array($value)) {
                foreach ($value as $vk=>$vv) {
                    $newKey = $key.".".$vk;
                    $tmpArray[$newKey] = $vv;
                    if (
                           is_string($vv) && 
                           preg_match_all('/\${([a-zA-Z0-9\.]+)}/', $vv, $match)
                        ) {
                        if (!isset($match[1])) continue;
                        $variableKey = $match[1];
                        foreach ($variableKey as &$var) {
                            if (strpos($var, '.') === false) {
                                $var = $key . '.' . $var;
                            }
                        }
                        $this->_varDeps[$newKey] = $variableKey;
                    }
                }
            }
        }	
        if (!empty($tmpArray)) {
            $aParsedIni = $tmpArray;
        }
        foreach ($aParsedIni as $key=>$value) { 	//extract parsed array keys
            if (array_key_exists($key, $this->_varDeps)) {
                $deps = &$this->_varDeps;
                $value = preg_replace_callback(
                    '/\${([a-zA-Z0-9\.]+)}/', 
                    function($match) use ($key, $aParsedIni, &$deps){
                        return $aParsedIni[array_shift($deps[$key])];
                    }, 
                    $value
                );
                $aParsedIni[$key] = $value;
            }
            $this->_tmpValue = $value;//set temporay value to current ini value
            $aXKey = explode($this->_delimiter, $key); 	//get ini key segments
            //set object properties recursively based on parsed ini
            $this->_recursiveInit($this->_obj, $aXKey);
        }
    }

    /**
     *
     * @param object Parent node
     * @param object Child Node
     */
    private function _recursiveInit($objParent, array $aChilds)
    {
        $child = $aChilds[0];
        if (count($aChilds) > 1) {
            if (!isset($objParent->$child)) {
                $objParent->$child = new iniObject();
            }
            array_shift($aChilds);  //drop first element of child array
            $this->_recursiveInit($objParent->$child, $aChilds);
        } else {
            //set the last child to temporary value
            $objParent->$child = $this->_tmpValue;
        }
    }

    /**
     * ini object getter
     *
     * @return object : object created from ini file
     */
    public function getObj()
    {
        return $this->_obj;
    }
}

/**
 * Customized stdClass
 */
class iniObject implements Countable, ArrayAccess
{
    public function count()
    {
        return count(get_object_vars($this));
    }
    
    public function offsetSet($offset, $value) 
    {
        $this->$offset = $value;
    }
    public function offsetExists($offset) 
    {
        return isset($this->$offset);
    }
    public function offsetUnset($offset) 
    {
        unset($this->$offset);
    }
    public function offsetGet($offset) 
    {
        return isset($this->$offset) ? $this->$offset : null;
    }
}
