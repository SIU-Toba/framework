<?php
require_once("nucleo/negocio/objeto_cn.php");	//Ancestro de todos los OE

class objeto_cn_ent extends objeto_cn
{
	var $entidad;

	function __construct($id, $resetear=false)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::__construct($id, $resetear);
	}

	//-------------------------------------------------------------------------------
	//----- Manejo del ENTIDADES
	//-------------------------------------------------------------------------------

	/*
		En este lugar es donde tengo que rutear los eventos del CI
		en llamadas al metodo EDITAR de la entidad.
		
		** Como utilizo la definicion del CI para hacer eso???

			- Metodo de entrada (ej: editar_entidad) + parametros!!
			- Atencion, el mecanismo de obtencion de parametros no tiene que romper compatibilidad
			- El metodo de entrada tiene que mapear a $this->entidad->editar(buffer, accion, parametros);

		** Como guardo la linea activa???

			- Metodo de entrada comun + parametros!
			- Esto tiene que mapear a una estructura interna que pueda funcionar!

		** Que metodos ofrezco para controlar derechos o controles antes de la llamadas?

			- 1) redefinir el metodo standart y meterle un IF
			- 2) Crear una entrada nueva
		
	*/

	//-------------------------------------------------------------------------------
	// VAlidacion transaccion
	//-------------------------------------------------------------------------------

	/*
		Puede ser que algunas validacines queden del lado de la entidad?
		Se puede especializar el marco de la transaccion?
	
	*/
}
?>
