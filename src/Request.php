<?php

namespace MeilisearchLightClient;

class Request
{
    // cURL options
    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT  => 20,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_SSL_VERIFYPEER  => false,
        CURLOPT_SSL_VERIFYHOST  => false,
        CURLOPT_TIMEOUT         => 180, // 3 minutes
        CURLOPT_HEADER          => false,
        CURLOPT_USERAGENT       => 'Meilisearch Light Client ',
        CURLOPT_ENCODING        => 1,
    );


    // Response content
    public $content = null;

    // Meilisearch server URL
    public $host = null;

    // Meilisearch API Key
    public $key = null;

    // Headers HTTP
    public $headers = [];

    // HTTP method
    public $httpMethod = 'GET';

    // HTTP status code
    public $httpCode = 0;

    // Datas to be transmitted
    public $datas = null;


    /**
     * Constructor
     *
     * @param string $host Meilisearch server URL (http://...:7700)
     */
    public function __construct($host)
    {
        $this->host = $host;
    }


    /**
     * Call Meilisearch API with options
     *
     * @param array $params Key and headers
     * @param string $method HTTP method (GET, POST, PUT, ...)
     * @param string $endpoint Endpoint (ex : indexes/...)
     * @param array $datas Datas
     */
    public function call($params, $method, $endpoint, $datas = null)
    {
        if (!array_key_exists('key', $params))
            throw new \Exception('call: key entry is missing in $params parameter');

        if (array_key_exists('headers', $params))
            $this->headers = $params['headers'];

        $this->key = $params['key'];

        $this->httpMethod = $method;

        $url = $this->host.'/'.$endpoint;

        $this->datas = $datas;


        // Exécution de la requête
        $response = $this->_doCurl($url);

        $this->content = $response;
    }


    /**
     * Execute the cURL request
     *
     * @return string Response
     */
    private function _doCurl($url)
    {
        $ch = curl_init();

        $opts = self::$CURL_OPTS;
        $opts[CURLOPT_URL] = $url;
        $opts[CURLOPT_CUSTOMREQUEST] = $this->httpMethod;

        $headers = [];
        $headers[] = 'Authorization: Bearer '.$this->key;
        if (count($this->headers) > 0)
        {
            foreach ($this->headers as $h)
                $headers[] = $h;
        }

        $opts[CURLOPT_HTTPHEADER] = $headers;

        if ($this->httpMethod == 'POST' || $this->httpMethod == 'PATCH')
        {
            // If it is a file that is transmitted (starts with @)
            if (!is_array($this->datas) && strpos($this->datas, '@') === 0)
            {
                $postData = file_get_contents(str_replace('@', '', $this->datas));
            }
            else
            {
                if (is_array($this->datas))
                    $postData = json_encode($this->datas);
                else
                    $postData = $this->datas;
            }
            
            $opts[CURLOPT_POST] = true;
            $opts[CURLOPT_POSTFIELDS] = $postData;
        }

        curl_setopt_array($ch, $opts);

        $response = curl_exec($ch);

        if (!curl_exec($ch))
        {
            throw new \Exception('cURL error: "'.curl_error($ch).'" - Code: '. curl_errno($ch));
        }

        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $this->isOk();
         
        return $response;
    }



    /**
     * Returns the HTTP status code
     *
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->httpCode;
    }


    /**
     * Whether API return is OK or not
     *
     * @return bool
     */
    public function isOk()
    {
        return 20 === intval(substr($this->httpCode, 0, 2));
    }


    /**
     * Return the response
     *
     * @return object \MeilisearchLightClient\Response
     */
    public function getResponse($toArray = false)
    {
        $Response = new Response;
        $Response->toArray = $toArray;
        $Response->set($this->content)->format();

        return $Response;
    }
}