<?php

/*
 * Configuration for the multi-DN LDAP authentication module.
 *
 * $Id: ldapmulti.php 3178 2012-09-28 09:14:49Z olavmrk $
 */

$ldapmulti = array(

    'feide.no' => array(
        'description'		=> 'Feide',
        /* for a description of options see equivalent options in ldap.php starting with auth.ldap. */
        'dnpattern'			=> 'uid=%username%,dc=feide,dc=no,ou=feide,dc=uninett,dc=no',
        'hostname'			=> 'ldap.uninett.no',
        'attributes'		=> null,
        'enable_tls'		=> true,
        'search.enable'		=> false,
        'search.base'		=> null,
        'search.attributes'	=> null,
        'search.username'	=> null,
        'search.password'	=> null,
    ),

    'uninett.no' => array(
        'description'		=> 'UNINETT',
        'dnpattern'			=> 'uid=%username%,ou=people,dc=uninett,dc=no',
        'hostname'			=> 'ldap.uninett.no',
        'attributes'		=> null,
    )

);
