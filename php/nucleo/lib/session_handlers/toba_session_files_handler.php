<?php
class toba_session_files_handler extends toba_session_handler
{
    protected $default_settings = array(
        'session.save_handler' => 'files',
        'session.save_path' => '/var/lib/php/sessions/'
    );
}
?>

