<?
/*
	Esta interface deben implementarla los CIs que se usen para modificar un DB_R o un DB_T
*/
interface interface_abm
{
	public function cargar($clave);
	public function guardar();
	public function eliminar();
	public function reset();
}
?>