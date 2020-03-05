<?php
class toba_session_files_handler extends toba_session_handler
{
    protected $default_settings = array(
        'session.save_handler' => 'files',
        'session.save_path' => '/var/lib/php/sessions/'
    );

    function __construct()
    {
        parent::__construct();
        $this->default_settings['session.save_path'] = \session_save_path();
    }
}
?>

