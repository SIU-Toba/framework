<?php


class hijos_a {

	function collection_get($id_padre)
	{
		return "GET /padres/{$id_padre}/hijos_a => hijos_a::collection_get($id_padre)";
	}

	function get($id_padre, $id_hijo_a)
	{
		return "GET /padres/{$id_padre}/hijos_a/{$id_hijo_a} => hijos_a::get($id_padre, $id_hijo_a)";
	}

}