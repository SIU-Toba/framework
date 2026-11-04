<?php

require_once 'nucleo/lib/rest/rest_test_case.php';

class recurso_deportesTest extends rest_test_case {


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
		$response = $this->ejecutar('GET', "/personas/$id/deportes");

		$deportes = $response->get_data();
		
		$this->assertEquals($response->get_status(), 200);
		$this->assertTrue(count($deportes) > 0);
		$this->assertArrayHasKey('deporte', $deportes[0]);
		$this->assertArrayHasKey('hora_fin', $deportes[0]);
		$this->assertArrayHasKey('hora_inicio', $deportes[0]);

	}

}