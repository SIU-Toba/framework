<?php
use rest\respuesta\respuesta_rest;


/**
 * Esta clase muestra una jerarquía de recursos y como se resuelven las rutas.
 * La jerarquía de archivos se mapea a las url con algunas restricciones.
 *
 * Una clase puede manejar los pedidos a /[nombre_clase]/*, es decir a su propio
 * nombre o sus subrecursos.
 *
 * Todas las carpetas [nombre_recurso], tienen que tener un archivo [nombre_recurso]
 * que las maneje.
 *
 * Los subrecursos se pueden definir de 3 formas. Sea el recurso 'padre', para definir
 * el subrecurso 'hijo', accesible via /padre/{id_padre}/hijo se puede:
 *
 *  metodo: Crear metodos en padre.php con el nombre del subrecurso.
 *          put_hijo($id_padre), get_hijo($id_padre, $id_hijo)
 *
 *  archivo: Creando un hijo.php dentro de la carpeta /padre. Si padre no estaba en una
 *           carpeta es necesario crear una, y colocar padre.php adentro de la misma.
 *           Al mismo nivel crear hijo.php. Las acciones serán del formato
 *           post($id_padre), delete($id_padre, $id_hijo)
 *
 *  carpeta: Crear la carpeta /padre/hijo con el archivo hijo.php
 *
 * Todos los subrecursos reciben los id's de los padres en orden, y su propio id en caso
 * de que sea un pedido a un recurso. Si es a la colección, los metodos se prefijan con
 * 'c'. cget, cpost
 *
 * Class padres
 */

class padres
{


	function get($id)
	{
		$data = array(
			'metodo' => 'GET',
			'url' => "GET /padres/{$id}",
			'accion' => 'padres::get($id)'
		);
		$respuesta = new respuesta_rest($data);
		return $respuesta;
	}

	function get_list()
	{
		$data = array(
			'metodo' => 'GET',
			'url' => "GET /padres",
			'accion' => 'padres::get()'
		);
		$respuesta = new respuesta_rest($data);
		return $respuesta;
	}

	function post_list()
	{
		return "POST /padres => padres::collection_post()";
	}

	function put($id)
	{
		return "PUT /padres/{$id} => padres::put($id)";
	}

	function get_hijos_c_list($id_padre)
	{
		return "GET /padres/{$id_padre}/hijos_c => padres::collection_get_hijos_c($id_padre)";
	}

	function get_hijos_c($id_padre, $id_hijo_c)
	{
		return "GET /padres/{$id_padre}/hijos_c/{$id_hijo_c} => padres::get_hijos_c($id_padre, $id_hijo_c)";
	}
}