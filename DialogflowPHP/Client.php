<?php

namespace DialogflowPHP;

use DialogflowPHP\Exceptions\ClientException;

class Client
{

    /** 
     * Developer access token for the agent.
     * 
     * @var    string 
     * @access private
     */
    private $_token;

    /**
     * Session id for chat.
     *
     * @var    string
     * @access private
     */
    private $_sessionId;


    /**
     * Initialises a new client object qualified to query a Dialogflow agent.
     *
     * @param string        $_token     The developer access token for the agent.
     * @param string|number $_sessionId Current session id.
     * 
     * @throws ClientException When no $_token is provided.
     */
    public function __construct($_token = null, $_sessionId = 0)
    {
        if ($_token === null) {
            $msg = 'No token provided. Interaction with an agent requires its ' /
            'developer access token';
            throw new ClientException($msg);
        } else {
            self::_validateToken($_token);
            $this->_token = $_token;
            $this->_sessionId = $_sessionId;
        }        
    }


    /**
     * Ensures the supplied agent developer access token is valid.
     *
     * @param string $_token Agent developer access token.
     * 
     * @return bool True if a valid token is supplied.
     * @throws InvalidArgumentException If an invalid token is supplied.
     */
    private static function _validateToken($_token)
    {
        if (!is_string($_token)) {
            throw new \InvalidArgumentException(
                'Supplied agent developer access token ('.$_token.') is not a '.
                'string. Token is instead of type ('.gettype($_token).')'
            );
        }
        return true;
    }


    /**
     * Poses a question to the agent and returns the response.
     *
     * @param string  $query          Question to pose to agent.
     * @param boolean $return_as_json Return agent output as JSON string.
     * 
     * @return array|string Agent output as a an associative array or JSON string.
     * @todo   Error code checking, language selection.
     */
    public function query($query, $return_as_json=false) 
    {
        $curl = curl_init('https://api.dialogflow.com/v1/query?v=20170712');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt(
            $curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json', 
                'Authorization: Bearer '.$this->_token
            )
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = array(
            'query' => array($query), 'lang' => 'en',
            'sessionId' => $this->_sessionId
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($curl);
        curl_close($curl);
        if ($return_as_json) {
            return $response;
        }
        return json_decode($response);
    }

}

?>


