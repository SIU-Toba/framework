<?php
require_once('pantalla_perfil_datos.php');

class pantalla_api_alto_nivel extends pantalla_perfil_datos
{
	function generar_layout()
	{
		foreach($this->sql as $id => $sql) {
			$sql2 = toba::perfil_de_datos()->filtrar($sql);
			echo "<hr><h1>".($id + 1)."</h1>";

			$datos = toba::db()->consultar($sql);
			$tit1 = "<pre>" . $sql . "</pre>";
			$this->tabla($datos,$tit1);

			echo "<hr>";
			$datos = toba::db()->consultar($sql2);
			$tit2 = "<pre>" . $sql2 . "</pre>";
			$this->tabla($datos,$tit2);
		}
		echo "<hr>";
		echo "<hr>";
		echo "<hr>";

		foreach($this->sql2 as $id => $sql) {
			$sql2 = toba::perfil_de_datos()->filtrar($sql,'referencia');
			echo "<hr><h1>".($id + 1)."</h1>";

			$datos = toba::db('referencia')->consultar($sql);
			$tit1 = "<pre>" . $sql . "</pre>";
			$this->tabla($datos,$tit1);

			echo "<hr>";
			$datos = toba::db('referencia')->consultar($sql2);
			$tit2 = "<pre>" . $sql2 . "</pre>";
			$this->tabla($datos,$tit2);
		}
	}
}
?>