<?
/*
	Hay que pensar un par de tablas para manejar los logs de cambios, versiones, revisiones SVN, etc.
	Estas tablas deberian ser la base de la administracion de conversiones.
*/

class conversion_toba
/*
	Esta clase representa una conversion entre dos versiones del toba
*/
{
	protected $version;

	function __construct()
	{
		$this->version = $this->get_version();
	}
	
	function get_lista_cambios()
	{
		/*
			Busca todos los metodos que empiezan con "cambio_"
		*/	
	}
	
	function procesar()
	{
		/*
			Dispara los metodos que empiezan con "cambio_" dentro de una transaccion
			Si todo sale ok, deja un log de que los cambios impactaron en el sistema
		*/
	}
	
	function ejecutar_sql($sql, $instancia)
	{
		/*
			Ejecuta el SQL y arma un LOG
		*/	
	}
}
?>