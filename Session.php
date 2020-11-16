<?php

namespace mayjhao\phphmvc;

class Session
{
    protected const FLASH_KEY = 'flash_messages';
    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }
    
    public function getFlash($key)
    {
        return isset($_SESSION[self::FLASH_KEY][$key])?$_SESSION[self::FLASH_KEY][$key]['value']:'';
    }
    
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key]??false;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }
    public function __destruct()
    {
        $flashMessages = isset($_SESSION[self::FLASH_KEY])?$_SESSION[self::FLASH_KEY]: [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}
