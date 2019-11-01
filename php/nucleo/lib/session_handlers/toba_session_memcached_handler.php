<?php
class toba_session_memcached_handler extends toba_session_handler
{
    protected $default_settings = array(
        'session.save_handler' => 'memcached',
        'session.save_path' => '127.0.0.1:11211',
        //--------------------------------------------//
        //  DE USO IMPORTATE + SASL
        //--------------------------------------------//
        //'memcached.sess_sasl_username' => null,
        //'memcached.sess_sasl_password' => null

        //--------------------------------------------//
        //  SETEOS EXTRA
        //--------------------------------------------//
      /*'memcached.sess_locking' => 'On',
        'memcached.sess_lock_wait_min' => 150,
        'memcached.sess_lock_wait_max' => 150,
        'memcached.sess_lock_retries' => 200,
        'memcached.sess_lock_expire' => 0,
        'memcached.sess_prefix' => 'memc.sess.key.',
        'memcached.sess_persistent' => 'Off',
        'memcached.sess_consistent_hash' => 'On',
        'memcached.sess_consistent_hash_type' => 'ketama',
        'memcached.sess_remove_failed_servers' => 'Off',*/
    );
}
?>
