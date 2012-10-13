<?php

namespace Bitly;

/**
 * Shortens a bit.ly link using their V3 API
 */
Class Bitly
{
    protected $_bitlyApi = 'http://api.bit.ly/v3/';
    protected $_domain = 'bit.ly';
    protected $_applicationLogin = '';
    protected $_applicationKey = '';
    protected $_userLogin;
    protected $_userKey;
    
    /**
     * Setup this class to be able to use the bit.ly API
     * 
     * @param string $applicationLogin
     * @param string $applicationKey
     */
    public function __construct($applicationLogin, $applicationKey)
    {
        $this->_applicationLogin = $applicationLogin;
        $this->_applicationKey = $applicationKey;
    }
    
    /**
     * Override application credentials with those of the user
     * 
     * @param string $userLogin
     * @param string $userKey
     */
    public function setCredentials($userLogin, $userKey)
    {
        $this->_userLogin = $userLogin;
        $this->_userKey = $userKey;
    }
    
    /**
     * Returns a user defined login or the aplication login as fallback
     * 
     * @return string
     */
    public function getActiveLogin()
    {
        if (!empty($this->_userLogin) AND !empty($this->_userKey)) {
            return $this->_userLogin;
        }
        
        return $this->_applicationLogin;
    }
    
    /**
     * Returns a user defined key or the aplication key as fallback
     * 
     * @return string
     */
    public function getActiveKey()
    {
        if (!empty($this->_userLogin) AND !empty($this->_userKey)) {
            return $this->_userKey;
        }
        
        return $this->_applicationKey;
    }
    
    /**
     * Shortens a URL
     * 
     * @param string $originalUrl
     * 
     * @return array
     */
    public function shorten($originalUrl)
    {
        $result = array();
        $url = sprintf(
            '%s/shorten?login=%s&apiKey=%s&format=json&longUrl=%s',
            $this->_bitlyApi,
            $this->getActiveLogin(),
            $this->getActiveKey(),
            urlencode($originalUrl)
        );
        if ($this->_domain != 'bit.ly') {
            $url .= "&domain=" . $this->_domain;
        }
        $output = json_decode($this->_bitlyGetCurl($url));
        if (isset($output->{'data'}->{'hash'})) {
            $result['url'] = $output->{'data'}->{'url'};
            $result['hash'] = $output->{'data'}->{'hash'};
            $result['global_hash'] = $output->{'data'}->{'global_hash'};
            $result['long_url'] = $output->{'data'}->{'long_url'};
            $result['new_hash'] = $output->{'data'}->{'new_hash'};
        }
        return $result;
    }
    
    /**
     * "Borrowed" from https://github.com/Falicon/BitlyPHP/blob/master/bitly.php
     */
    protected function _bitlyGetCurl($uri) {
      $output = "";
      try {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $output = curl_exec($ch);
      } catch (\Exception $e) {
      }
      return $output;
    }
}