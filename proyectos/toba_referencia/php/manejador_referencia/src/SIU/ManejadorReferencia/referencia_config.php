<?php


class referencia_config
{
    private static $_config = [
            'logo_nombre' => "",
            'logo_iso' => "",
            'logo_espera' =>  "",
            'logo_login' => "",
            'main_color' => "#890c71",
            'corte-0' => 'rgb(153, 51, 153)',
            'corte-1' => 'rgb(204, 102, 204)',
            'corte-2' => ''
    ];


    public static function getLogoNombre()
    {
        return self::$_config['logo_nombre'];
    }

    public static function setLogoNombre($value)
    {
        self::$_config['logo_nombre'] = $value;
    }

    public static function getIsoLogo()
    {
        return self::$_config['logo_iso'];
    }

    public static function setIsoLogo($value)
    {
        self::$_config['logo_iso'] = $value;
    }

    public static function getLogoEspera()
    {
        return self::$_config['logo_espera'];
    }

    public static function setLogoEspera($value)
    {
        self::$_config['logo_espera'] = $value;
    }

    public static function getLogoLogin()
    {
        return self::$_config['logo_login'];
    }

    public static function setLogoLogin($value)
    {
        self::$_config['logo_login'] = $value;
    }

    public static function getMainColor()
    {
        return self::$_config['main_color'];
    }

    public static function setMainColor($value)
    {
        self::$_config['main_color'] = $value;
    }

    public static function getCorteControl0()
    {
        return self::$_config['corte-0'];
    }

    public static function setCorteControl0($value)
    {
        self::$_config['corte-0'] = $value;
    }

    public static function getCorteControl1()
    {
        return self::$_config['corte-1'];
    }

    public static function setCorteControl1($value)
    {
        self::$_config['corte-1'] = $value;
    }
    public static function getCorteControl2()
    {
        return self::$_config['corte-2'];
    }

    public static function setCorteControl2($value)
    {
        self::$_config['corte-2'] = $value;
    }
}
