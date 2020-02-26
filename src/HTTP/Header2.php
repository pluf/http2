<?php
/*
 * Copyright (c) 2003-2005, Michael Wallner <mike@iworks.at>.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 * 
 *     * Redistributions of source code must retain the above copyright notice, 
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright 
 *       notice, this list of conditions and the following disclaimer in the 
 *       documentation and/or other materials provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE 
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL 
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR 
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, 
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE 
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 */

/**
 * HTTP::Header
 *
 * PHP versions 5
 *
 * @category  HTTP
 * @package   HTTP_Header2
 * @author    Wolfram Kriesing <wk@visionp.de>
 * @author    Davey Shafik <davey@php.net>
 * @author    Michael Wallner <mike@php.net>
 * @copyright 2003-2005 The Authors
 * @license   BSD, revised
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/HTTP_Header2
 */

/**
 * Requires HTTP
 */
require_once 'HTTP2.php';

/**
 * HTTP_Header2
 *
 * @category HTTP
 * @package  HTTP_Header2
 * @author   Wolfram Kriesing <wk@visionp.de>
 * @author   Davey Shafik <davey@php.net>
 * @author   Michael Wallner <mike@php.net>
 * @license  BSD, revised
 * @version  $Revision$
 * @link     http://pear.php.net/package/HTTP_Header2
 */
class HTTP_Header2 extends HTTP2
{

    /**#@+
     * Information Codes
     */
    const STATUS_100 = '100 Continue';
    const STATUS_101 = '101 Switching Protocols';
    const STATUS_102 = '102 Processing';
    const STATUS_INFORMATIONAL = 1;
    /**#@-*/

    /**#+
     * Success Codes
     */
    const STATUS_200 = '200 OK';
    const STATUS_201 = '201 Created';
    const STATUS_202 = '202 Accepted';
    const STATUS_203 = '203 Non-Authoritative Information';
    const STATUS_204 = '204 No Content';
    const STATUS_205 = '205 Reset Content';
    const STATUS_206 = '206 Partial Content';
    const STATUS_207 = '207 Multi-Status';
    const STATUS_SUCCESSFUL = 2;
    /**#@-*/

    /**#@+
     * Redirection Codes
     */
    const STATUS_300 = '300 Multiple Choices';
    const STATUS_301 = '301 Moved Permanently';
    const STATUS_302 = '302 Found';
    const STATUS_303 = '303 See Other';
    const STATUS_304 = '304 Not Modified';
    const STATUS_305 = '305 Use Proxy';
    const STATUS_306 = '306 (Unused)';
    const STATUS_307 = '307 Temporary Redirect';
    const STATUS_REDIRECT = 3;
    /**#@-*/

    /**#@+
     * Error Codes
     */
    const STATUS_400 = '400 Bad Request';
    const STATUS_401 = '401 Unauthorized';
    const STATUS_402 = '402 Payment Granted';
    const STATUS_403 = '403 Forbidden';
    const STATUS_404 = '404 File Not Found';
    const STATUS_405 = '405 Method Not Allowed';
    const STATUS_406 = '406 Not Acceptable';
    const STATUS_407 = '407 Proxy Authentication Required';
    const STATUS_408 = '408 Request Time-out';
    const STATUS_409 = '409 Conflict';
    const STATUS_410 = '410 Gone';
    const STATUS_411 = '411 Length Required';
    const STATUS_412 = '412 Precondition Failed';
    const STATUS_413 = '413 Request Entity Too Large';
    const STATUS_414 = '414 Request-URI Too Large';
    const STATUS_415 = '415 Unsupported Media Type';
    const STATUS_416 = '416 Requested range not satisfiable';
    const STATUS_417 = '417 Expectation Failed';
    const STATUS_422 = '422 Unprocessable Entity';
    const STATUS_423 = '423 Locked';
    const STATUS_424 = '424 Failed Dependency';
    const STATUS_CLIENT_ERROR = 4;
    /**#@-*/

    /**#@+
     * Server Errors
     */
    const STATUS_500 = '500 Internal Server Error';
    const STATUS_501 = '501 Not Implemented';
    const STATUS_502 = '502 Bad Gateway';
    const STATUS_503 = '503 Service Unavailable';
    const STATUS_504 = '504 Gateway Time-out';
    const STATUS_505 = '505 HTTP Version not supported';
    const STATUS_507 = '507 Insufficient Storage';
    const STATUS_SERVER_ERROR = 5;
    /**#@-*/


    /**
     * Default Headers
     *
     * The values that are set as default, are the same as PHP sends by default.
     *
     * @var     array
     * @access  private
     */
    private $_headers = array(
        'content-type'  => 'text/html',
        'pragma'        => 'no-cache',
        'cache-control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0'
    );

    /**
     * HTTP version
     *
     * @var     string
     * @access  private
     */
    private $_httpVersion = '1.0';

    /**
     * @var     bool
     */
    public $prettify = false;

    /**
     * Constructor
     *
     * Sets HTTP version.
     *
     * @return  object  HTTP_Header2
     */
    public function __construct()
    {
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            $this->setHttpVersion(substr($_SERVER['SERVER_PROTOCOL'], -3));
        }
    }

    /**
     * Set HTTP version
     *
     * @param mixed $version HTTP version, either 1.0 or 1.1
     * 
     * @return bool Returns true on success or false if version doesn't
     *              match 1.0 or 1.1 (note: 1 will result in 1.0)
     */
    public function setHttpVersion($version)
    {
        $version = round((float) $version, 1);
        if ($version < 1.0 || $version > 1.1) {
            return false;
        }
        $this->_httpVersion = sprintf('%0.1f', $version);
        return true;
    }

    /**
     * Get HTTP version
     *
     * @return  string
     */
    public function getHttpVersion()
    {
        return $this->_httpVersion;
    }

    /**
     * Set Header
     *
     * The default value for the Last-Modified header will be current
     * date and time if $value is omitted.
     *
     * @param string $key   The name of the header.
     * @param string $value The value of the header. (NULL to unset header)
     * 
     * @return bool Returns true on success or false if $key was empty or
     *              $value was not of an scalar type.
     */
    public function setHeader($key, $value = null)
    {
        if (empty($key) || (isset($value) && !is_scalar($value))) {
            return false;
        }

        $key = strToLower($key);
        if ($key == 'last-modified') {
            if (!isset($value)) {
                $value = $this->date(time());
            } elseif (is_numeric($value)) {
                $value = $this->date($value);
            }
        }

        if (isset($value)) {
            $this->_headers[$key] = $value;
        } else {
            unset($this->_headers[$key]);
        }

        return true;
    }

    /**
     * Get Header
     *
     * If $key is omitted, all stored headers will be returned.
     *
     * @param string $key The name of the header to fetch.
     *
     * @return mixed Returns string value of the requested header, array values
     *               of all headers or false if header $key is not set.
     */
    public function getHeader($key = null)
    {
        if (!isset($key)) {
            return $this->_headers;
        }

        $key = strToLower($key);

        if (!isset($this->_headers[$key])) {
            return false;
        }

        return $this->_headers[$key];
    }

    /**
     * Send Headers
     *
     * Send out the header that you set via setHeader().
     *
     * @param array $keys    Headers to (not) send, see $include.
     * @param array $include If true only $keys matching headers will be sent,
     *                       if false only header not matching $keys will be
     *                       sent.
     *
     * @return  bool    Returns true on success or false if headers are already
     *                  sent.
     */
    public function sendHeaders($keys = array(), $include = true)
    {
        if (headers_sent()) {
            return false;
        }

        if (count($keys)) {
            array_change_key_case($keys, CASE_LOWER);
            foreach ($this->_headers as $key => $value) {
                if ($include ? in_array($key, $keys) : !in_array($key, $keys)) {
                    header(($this->prettify ? uctitle($key) : $key) .': '. $value);
                }
            }
        } else {
            foreach ($this->_headers as $header => $value) {
                header(($this->prettify ? uctitle($header) : $header) .': '. $value);
            }
        }
        return true;
    }

    /**
     * Send Satus Code
     *
     * Send out the given HTTP-Status code. Use this for example when you
     * want to tell the client this page is cached, then you would call
     * sendStatusCode(304). {@see HTTP_Header2_Cache::exitIfCached()}
     *
     * @param int $code The status code to send, i.e. 404, 304, 200, etc.
     * 
     * @return bool Returns true on success or false if headers are already
     *                  sent.
     */
    public function sendStatusCode($code)
    {
        if (headers_sent()) {
            return false;
        }

        if ($code == (int) $code && defined('HTTP_Header2::STATUS_'. $code)) {
            $code = constant('HTTP_Header2::STATUS_'. $code);
        }

        if (strncasecmp(PHP_SAPI, 'cgi', 3)) {
            header('HTTP/'. $this->_httpVersion .' '. $code);
        } else {
            header('Status: '. $code);
        }
        return true;
    }

    /**
     * Date to Timestamp
     *
     * Converts dates like
     *      Mon, 31 Mar 2003 15:26:34 GMT
     *      Tue, 15 Nov 1994 12:45:26 GMT
     * into a timestamp, strtotime() didn't do it in older versions.
     *
     * @param string $date The GMT date.
     *
     * @deprecated Use PHPs strtotime() instead.
     *
     * @return mixed Returns int unix timestamp or false if the date doesn't
     *               seem to be a valid GMT date.
     */
    public function dateToTimestamp($date)
    {
        if (!is_string($date)) {
            throw new InvalidArgumentException(
                "Date must be a string, not " . gettype($date)
            );
        }

        $months = array(
            null => 0, 'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4,
            'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9,
            'Oct' => 10, 'Nov' => 11, 'Dec' => 12
        );

        if (-1 < $timestamp = strtotime($date)) {
            return $timestamp;
        }

        $regex = '~[^,]*,\s(\d+)\s(\w+)\s(\d+)\s(\d+):(\d+):(\d+).*~';
        if (!preg_match($regex, $date, $m)) {
            return false;
        }

        // [0] => Mon, 31 Mar 2003 15:42:55 GMT
        // [1] => 31 [2] => Mar [3] => 2003 [4] => 15 [5] => 42 [6] => 55
        return mktime($m[4], $m[5], $m[6], $months[$m[2]], $m[1], $m[3]);
    }

    /**
     * Redirect
     *
     * This function redirects the client. This is done by issuing a Location
     * header and exiting.  Additionally to HTTP2::redirect() you can also add
     * parameters to the url.
     *
     * If you dont need parameters to be added, simply use HTTP2::redirect()
     * otherwise use HTTP_Header2::redirect().
     * 
     * @param string $url     The URL to redirect to, if none is given it
     *                        redirects to the current page.
     * @param array  $param   Array of query string parameters to add; usually a
     *                        set of key => value pairs; if an array entry
     *                        consists only of an value it is used as key and
     *                        the respective value is fetched from
     *                        $GLOBALS[$value]
     * @param bool   $session Whether the session name/id should be added
     * 
     * @see     HTTP2::redirect()
     * @author  Wolfram Kriesing <wk@visionp.de>
     * 
     * @return  void
     */
    public function redirect($url = null, $param = array(), $session = false)
    {
        if (!isset($url)) {
            $url = $_SERVER['PHP_SELF'];
        }

        $qs = array();

        if ($session) {
            $qs[] = session_name() .'='. session_id();
        }

        if (is_array($param) && count($param)) {
            if (count($param)) {
                foreach ($param as $key => $val) {
                    if (is_string($key)) {
                        $qs[] = urlencode($key) .'='. urlencode($val);
                    } else {
                        $qs[] = urlencode($val) .'='. urlencode(@$GLOBALS[$val]);
                    }
                }
            }
        }

        if ($qstr = implode('&', $qs)) {
            $purl = parse_url($url);
            $url .= (isset($purl['query']) ? '&' : '?') . $qstr;
        }

        parent::redirect($url);
    }

    /**#@+
     * @author  Davey Shafik <davey@php.net>
     * @param   int $http_code HTTP Code to check
     */

    /**
     * Return HTTP Status Code Type
     *
     * @param string $http_code HTTP status code
     *
     * @return int|false
     */
    public function getStatusType($http_code)
    {
        if (is_int($http_code) && defined('HTTP_Header2::STATUS_' .$http_code)
            || defined($http_code)
        ) {
            $type = substr($http_code, 0, 1);
            switch ($type) {
            case HTTP_Header2::STATUS_INFORMATIONAL:
            case HTTP_Header2::STATUS_SUCCESSFUL:
            case HTTP_Header2::STATUS_REDIRECT:
            case HTTP_Header2::STATUS_CLIENT_ERROR:
            case HTTP_Header2::STATUS_SERVER_ERROR:
                return $type;
                break;
            default:
                return false;
                break;
            }
        } else {
            return false;
        }
    }

    /**
     * Return Status Code Message
     *
     * @param string $http_code HTTP status code
     *
     * @return string|false
     */
    public function getStatusText($http_code)
    {
        if ($this->getStatusType($http_code)) {
            if (is_int($http_code) && defined('HTTP_Header2::STATUS_' .$http_code)) {
                return substr(constant('HTTP_Header2::STATUS_' .$http_code), 4);
            } else {
                return substr($http_code, 4);
            }
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is Information (1xx)
     * 
     * @param string $http_code HTTP status code
     *
     * @return boolean
     */
    public function isInformational($http_code)
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type[0] == HTTP_Header2::STATUS_INFORMATIONAL;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is Successful (2xx)
     * 
     * @param string $http_code HTTP status code
     *
     * @return boolean
     */
    public function isSuccessful($http_code)
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type[0] == HTTP_Header2::STATUS_SUCCESSFUL;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is a Redirect (3xx)
     * 
     * @param string $http_code HTTP status code
     *
     * @return boolean
     */
    public function isRedirect($http_code)
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type[0] == HTTP_Header2::STATUS_REDIRECT;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is a Client Error (4xx)
     * 
     * @param string $http_code HTTP status code
     *
     * @return boolean
     */
    public function isClientError($http_code)
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type[0] == HTTP_Header2::STATUS_CLIENT_ERROR;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is Server Error (5xx)
     * 
     * @param string $http_code HTTP status code
     *
     * @return boolean
     */
    public function isServerError($http_code)
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type[0] == HTTP_Header2::STATUS_SERVER_ERROR;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is Server OR Client Error (4xx or 5xx)
     * 
     * @param string $http_code HTTP status code
     *
     * @return boolean
     */
    public function isError($http_code)
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return (   ($status_type == HTTP_Header2::STATUS_CLIENT_ERROR)
                    || ($status_type == HTTP_Header2::STATUS_SERVER_ERROR)
                   );
        } else {
            return false;
        }
    }
    /**#@-*/
}