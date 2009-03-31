<?php
php_referencia::instancia()->agregar(__FILE__);

/*
*	En este dao las b�squedas son est�ticas, generalmente terminar�an en una SQL
* 
*/
class dao
{
	static function get_paises()
	{
		return array(
				array('id' => 'ar', 'valor' => 'Argentina'),
				array('id' => 'br', 'valor' => 'Brasil')
			);
	}
	
	static function get_provincias($pais)
	{
		if ($pais == 'ar') {
			return array(
					array('id' => 'ba', 'valor' => 'Buenos Aires'),
					array('id' => 'sj', 'valor' => 'San Juan')
				);
		} 
		return array();
	}

	/*
	*  Para obtener la localidad s�lo necesitar�a la provincia, pero para mostrar las dependencias de multiples maestros 
	*  se usa el pa�s tambi�n
	*/
	static function get_localidades($pais, $provincia)
	{
		if ($pais == 'ar') {
			if ($provincia == 'ba') {
				return array( array('id' => 'bb', 'valor' => 'Bah�a Blanca'));
			} elseif ($provincia == 'sj') {
				return array( array('id' => 'sj', 'valor' => 'San Juan'));
			}
		} 
		return array();
	}
	
	
	static function get_descripcion($localidad)
	{
		if ($localidad == 'bb') {
			return 'Esta es la descripci�n de Bah�a Blanca';
		} elseif ($localidad == 'sj') {
			return 'Esta es la descripci�n de San Juan';
		}
	}
}

?>