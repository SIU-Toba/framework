<?php

class hijos_b {

	function collection_get($id_padre)
	{
		return "GET /padres/{$id_padre}/hijos_b => hijos_b::collection_get($id_padre)";
	}

	function get($id_padre, $id_hijo_b)
	{
		return "GET /padres/{$id_padre}/hijos_b/{$id_hijo_b} => hijos_b::get($id_padre, $id_hijo_b)";
	}
}