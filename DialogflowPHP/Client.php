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
    private $_session_id;

    /**
     * API protocol version. Library developed using protocol version 20170712
     * https://dialogflow.com/docs/reference/agent/#protocol_version
     * 
     * @var    string
     * @access private
     */
    private $_protocol_version = '20170712';


    
    /**
     * Initialises a new client object qualified to query a Dialogflow agent.
     *
     * @param string        $_token      The developer access token for the agent.
     * @param string|number $_session_id Current session id.
     * 
     * @throws ClientException When no $_token is provided.
     */
    public function __construct($_token = null, $_session_id = 0)
    {
        if ($_token === null) {
            $msg = 'No token provided. Interaction with an agent requires its ' /
            'developer access token';
            throw new ClientException($msg);
        } else {
            self::_validateToken($_token);
            $this->_token = $_token;
            $this->_session_id = $_session_id;
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
            'sessionId' => $this->_session_id
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($curl);
        curl_close($curl);
        if ($return_as_json) {
            return $response;
        }
        return json_decode($response);
    }


    /**
     * Retrieves the specified contexts for the current session.
     *
     * @return string JSON encoded string containing contexts for the current 
     *                session.
     */
    public function getContexts() 
    {
        $curl = curl_init();
        curl_setopt_array(
            $curl, array(
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_URL=>'https://api.dialogflow.com/v1/contexts?v='.
                    $this->_protocol_version.'&sessionId='.$this->_session_id,
                CURLOPT_HTTPHEADER=>array(
                    'Content-Type: application/json', 
                    'Authorization: Bearer '.$this->_token
                )
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    /**
     * Adds a new active context to the specified session.
     *
     * @param integer $lifespan   The number of queries this context will remain 
     *                            active after being invoked.
     * @param string  $name       The name of the context.
     * @param array   $parameters Key/value pairs of parameters being passed through 
     *                            the context.
     * 
     * @return void
     * @todo   Verify context was created.
     */
    public function createContext($lifespan, $name, $parameters=[]) 
    {
        $curl = curl_init();
        curl_setopt_array(
            $curl, array(
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_URL=>'https://api.dialogflow.com/v1/contexts?v='.
                    $this->_protocol_version.'&sessionId='.$this->_session_id,
                CURLOPT_HTTPHEADER=>array(
                    'Content-Type: application/json', 
                    'Authorization: Bearer '.$this->_token
                ),
                CURLOPT_POST=>true,
                CURLOPT_POSTFIELDS=>json_encode(
                    array(
                        'lifespan'=>$lifespan, 
                        'name'=>$name, 
                        'parameters'=>$parameters
                    )
                )
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
    }

}

?>


