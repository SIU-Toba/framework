<?php
/**
 * Created by IntelliJ IDEA.
 * User: andres
 * Date: 2/4/15
 * Time: 3:30 PM.
 */

namespace SIUToba\rest\seguridad\autenticacion\oauth2;

class token_info
{
    protected $user_id;
    protected $scopes;

    public function __construct()
    {
    }

    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function set_scopes($scopes)
    {
        $this->scopes = $scopes;
    }

    public function get_user_id()
    {
        return $this->user_id;
    }

    public function get_scopes()
    {
        return $this->scopes;
    }
}
