<?php

namespace MeilisearchLightClient;

class Response
{
    public $originalResponse = null;
    public $formattedResponse = null;
    public $toArray = false;


    /**
     * Define response content
     *
     * @param string $input Content of the API response
     * @return object
     */
    public function set($input)
    {
        $this->originalResponse = $input;

        return $this;
    }


    /**
     * Format a response
     * 
     * @return object
     */
    public function format()
    {
        $this->formattedResponse = json_decode($this->originalResponse, $this->toArray);
    }

    
    /**
     * Returns the content of the formatted response
     *
     * @return object Formatted response
     */
    public function get()
    {
        return $this->formattedResponse;
    }

}