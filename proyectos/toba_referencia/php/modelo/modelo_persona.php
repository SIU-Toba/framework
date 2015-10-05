<?php

class modelo_filtro
{
	function __construct($campos)
	{
		
	}
}

class modelo_persona
{
	protected $id;

	static function get_personas($where = "", $order_by = "", $limit = "")
	{
		if ($order_by == "") {
			$order_by = "ORDER BY pers.id ASC";
		}
		$sql = "SELECT 
					pers.id, 
					pers.nombre, 
					pers.fecha_nac,
					case when pers.imagen is not null then 'Si' else 'No' end as imagen
				FROM 
					ref_persona pers
				WHERE  $where $order_by $limit";
		$datos = toba::db()->consultar($sql);
		return $datos;
	}
	
	static function get_cant_personas($where = "")
	{
		$sql = "SELECT 
					count(*) as cantidad
				FROM 
					ref_persona pers
				WHERE $where";
		$datos = toba::db()->consultar_fila($sql);
		return $datos['cantidad'];
	}
	
	static function get_deportes($id_persona)
	{
        $sql = "SELECT
					deporte,
					dia_semana,
					hora_inicio,
					hora_fin
				FROM ref_persona_deportes
				WHERE persona = " . quote($id_persona);
        return toba::db()->consultar($sql);
	}
	
	static function get_juegos($id_persona, $de_mesa = -1)
	{
		$where_de_mesa = '';
		if ($de_mesa == 1) {
			$where_de_mesa = " AND j.de_mesa IS TRUE ";
		} elseif ($de_mesa == 0) {
			$where_de_mesa = " AND j.de_mesa IS FALSE ";
		}
		
        $sql = "SELECT
					pj.juego,
					pj.dia_semana,
					pj.hora_inicio,
					pj.hora_fin
				FROM ref_persona_juegos as pj
				JOIN ref_juegos as j ON (pj.juego = j.id)
				WHERE pj.persona = " . quote($id_persona) .
				$where_de_mesa;
		return toba::db()->consultar($sql);		
	}
	
	static function insert($datos)
	{
		$sql = "INSERT INTO ref_persona (nombre, fecha_nac) VALUES (" . quote($datos['nombre']) . ", " . quote($datos['fecha_nac']) . ")";
		toba::db()->ejecutar($sql);
		return toba::db()->ultimo_insert_id("ref_persona_id_seq");
	}

	public static function validar($datos)
	{
		//es de juguete esta validacion - Habría que chequear tipos, y diferenciar si está
		//modificando o creando, si tiene permisos y otras reglas de negocio.
		$errores = array();
		if(!isset($datos['nombre']) && !isset($datos['imagen'])){
			$errores['nombre'] = 'el campo es obligatorio a menos que se provea una imagen';
			$errores['imagen'] = 'el campo es obligatorio a menos que se provea un nombre';
		}
		return $errores;
	}
	
	//-------------------------------------
	//---		DINAMICO
	//-------------------------------------
	
	function __construct($id)
	{
		$this->id = (int)$id;
	}
	
	function update($datos)
	{
		$sql = "UPDATE ref_persona SET nombre = ".quote($datos['nombre'])." WHERE id = ".quote($this->id);
		return toba::db()->ejecutar($sql);
	}

    function update_imagen($datos){
        $imagen = base64_decode($datos['imagen']);

        $sentencia = toba::db()->sentencia_preparar("UPDATE ref_persona SET imagen = ? WHERE id = ".quote($this->id));
        toba::db()->sentencia_agregar_binarios($sentencia, array($imagen));
        return toba::db()->sentencia_ejecutar($sentencia);
    }
	
	function delete()
	{
        $sql = "DELETE FROM ref_persona WHERE id = " . quote($this->id);
        return toba::db()->ejecutar($sql);
	}
	
	function get_datos($incluir_imagen = false)
	{
        $imagen = ($incluir_imagen)? 'imagen,': '';
		$sql = "SELECT
					id,
					nombre,
					fecha_nac,
					planilla_pdf_firmada,
					$imagen
					(imagen IS NOT NULL) as tiene_imagen
				FROM ref_persona WHERE id = ".quote($this->id);
		$fila = toba::db()->consultar_fila($sql);
        if($incluir_imagen && $fila['imagen']){
            $fila['imagen'] = base64_encode(stream_get_contents($fila['imagen']));
        }
        return $fila;
	}
}