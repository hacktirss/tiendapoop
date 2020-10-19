<?php

/**
 * Description of Session
 * omicrom®
 * © 2019, Detisa 
 * http://www.detisa.com.mx
 * @author Tirso Bautista Anaya
 * @version 1.0
 * @since ago 2019
 */
class Session {

    const SESSION_STARTED = TRUE;
    const SESSION_NOT_STARTED = FALSE;

    // The state of the session
    private $sessionState = self::SESSION_NOT_STARTED;
    // THE only instance of the class
    private static $instance;

    private function __construct() {        
        
    }

    /**
     *  Returns THE instance of 'Session'.
     *  The session is automatically initialized if it wasn't.
     * 
     *  @param string $sessionName
     *  @return object
     */
    public static function getInstance($sessionName) {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        self::$instance->startSession($sessionName);

        return self::$instance;
    }

    /**
     *    (Re)starts the session.
     *   
     *    @param string $sessionName
     *    @return    bool    TRUE if the session has been initialized, else FALSE.
     * */
    public function startSession($sessionName) {        
        if ($this->sessionState == self::SESSION_NOT_STARTED) {
            session_name($sessionName);
            $this->sessionState = session_start();
        }        
        return $this->sessionState;
    }

    /**
     *    Stores datas in the session.
     *    Example: $instance->foo = 'bar';
     *   
     *    @param    name    Name of the datas.
     *    @param    value    Your datas.
     *    @return    void
     * */
    public function __set($name, $value) {
        $_SESSION[$name] = $value;
    }

    /**
     *    Gets datas from the session.
     *    Example: echo $instance->foo;
     *   
     *    @param    name    Name of the datas to get.
     *    @return    mixed    Datas stored in session.
     * */
    public function __get($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
    }

    public function __isset($name) {
        return isset($_SESSION[$name]);
    }

    public function __unset($name) {
        unset($_SESSION[$name]);
    }

    /**
     *    Destroys the current session.
     *   
     *    @return    bool    TRUE is session has been deleted, else FALSE.
     * */
    public function destroy() {
        if ($this->sessionState == self::SESSION_STARTED) {
            $this->sessionState = !session_destroy();
            unset($_SESSION);
            $this->clear_duplicate_cookies();
            return !$this->sessionState;
        }

        return FALSE;
    }    
        
    /**
     * Every time you call session_start(), PHP adds another
     * identical session cookie to the response header. Do this
     * enough times, and your response header becomes big enough
     * to choke the web server.
     *
     * This method clears out the duplicate session cookies. You can
     * call it after each time you've called session_start(), or call it
     * just before you send your headers.
     */
    private function clear_duplicate_cookies() {
        // If headers have already been sent, there's nothing we can do
        if (headers_sent()) {
            return;
        }

        $cookies = array();
        foreach (headers_list() as $header) {
            // Identify cookie headers
            if (strpos($header, 'Set-Cookie:') === 0) {
                $cookies[] = $header;
            }
        }
        // Removes all cookie headers, including duplicates
        header_remove('Set-Cookie');

        // Restore one copy of each cookie
        foreach (array_unique($cookies) as $cookie) {
            header($cookie, false);
        }
    }

}
