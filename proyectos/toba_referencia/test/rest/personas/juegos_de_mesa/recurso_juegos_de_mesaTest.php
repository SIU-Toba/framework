<?php

require_once 'nucleo/lib/rest/rest_test_case.php';

class recurso_juegos_de_mesaTest extends rest_test_case {


	protected function setUp()
	{
		parent::setUp();
		$app = $this->setupRest();

		$user = new SIUToba\rest\seguridad\rest_usuario();
		$user->set_usuario('user');
		$this->mock_autenticador($user, $app);
	}

	public function testGetList()
	{
		$id = 2;
		$response = $this->ejecutar('GET', "/personas/$id/juegos-de-mesa");

		$juegos_de_mesa = $response->get_data();

		$this->assertEquals($response->get_status(), 200);
		$this->assertTrue(count($juegos_de_mesa) > 0);
		$this->assertArrayHasKey('juego', $juegos_de_mesa[0]);
		$this->assertArrayHasKey('hora_fin', $juegos_de_mesa[0]);
		$this->assertArrayHasKey('hora_inicio', $juegos_de_mesa[0]);

	}

}