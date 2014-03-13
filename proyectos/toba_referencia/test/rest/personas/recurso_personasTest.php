<?php

require_once 'lib/rest/toba/rest_test_case.php';

class recurso_personasTest extends \rest\toba\rest_test_case {


	protected function setUp()
	{
		parent::setUp();
		$app = $this->setupRest();

		$user = new \rest\seguridad\rest_usuario();
		$user->set_usuario('user');
		$this->mock_autenticador($user, $app);
	}

	public function testGetList()
	{
		$response = $this->ejecutar('GET', '/personas');

		$personas = $response->get_data();

		$this->assertEquals($response->get_status(), 200);
		$this->assertTrue(count($personas) > 0);
		$this->assertArrayHasKey('id', $personas[0]);
		$this->assertArrayHasKey('nombre', $personas[0]);
		$this->assertArrayHasKey('fecha_nac', $personas[0]);

		return $personas[0];
	}

	/**
	 * @depends testGetList
	 */
	public function testGetListFiltro($persona)
	{
		$nombre = $persona['nombre'];

		$get = array('nombre' => "es_igual_a;$nombre");
		$response = $this->ejecutar('GET', '/personas', $get);

		$persona_res = $response->get_data();

		$this->assertEquals($response->get_status(), 200);

		foreach ($persona_res as $p) {
			$this->assertEquals($nombre, $p['nombre']);
		}

		$this->assertEquals(1, count($persona_res), 200);
	}

	public function testPost()
	{
		$post = array(
			'nombre' => 'Julia',
			'fecha_nac' => '1990-03-03'
			);

		$response = $this->ejecutar('POST', '/personas', array(), $post);
		$res_post = $response->get_data();

		$this->assertEquals($response->get_status(), 201);
		$this->assertArrayHasKey('id', $res_post);

		$response = $this->ejecutar('GET', '/personas/'.$res_post['id']);
		$res_get = $response->get_data();

		$this->assertEquals($response->get_status(), 200);

		$this->assertEquals($res_post['id'], $res_get['id']);
		$this->assertEquals($post['nombre'], $res_get['nombre']);
		$this->assertEquals($post['fecha_nac'], $res_get['fecha_nac']);
		return $res_post['id'];
	}

	/**
	 * Traigo un id valido
	 * @depends testPost
	 **/
	public function testPUT($id_posteado)
	{
		//str random
		$nombre = substr( "abcdefghijklmnopqrstuvwxyz", mt_rand(0, 25) , 1) .substr( md5( time() ), 1);

		$post = array('nombre' => $nombre);
		$response = $this->ejecutar('PUT', '/personas/'.$id_posteado, array(), $post);
		$res_put = $response->get_data();

		$this->assertEquals($response->get_status(), 204); //ok, no content
		$this->assertEmpty($res_put);

		$response = $this->ejecutar('GET', '/personas/'.$id_posteado);
		$res_get = $response->get_data();

		$this->assertEquals($response->get_status(), 200);
		$this->assertEquals($id_posteado, $res_get['id']);
		$this->assertEquals($nombre, $res_get['nombre']);
	}

	/**
	 * @depends testPost
	 */
	public function  testPUTError($id_valido)
	{
		$post = array('campo_erroneo' => 'a');
		$response = $this->ejecutar('PUT', '/personas/' . $id_valido, array(), $post);
		$res_put = $response->get_data();

		$this->assertEquals($response->get_status(), 400); //ok, no content
		$this->assertNotEmpty($res_put);
	}


	/**
	 * @depends testPost
	 */
	public function  testDelete($id_valido)
	{
		$response = $this->ejecutar('DELETE', '/personas/' . $id_valido);
		$res_put = $response->get_data();

		$this->assertEquals($response->get_status(), 204); //ok, no content
		$this->assertEmpty($res_put);
	}

	public function testGet404()
	{
		$id = 'no-existe';

		$response = $this->ejecutar('GET', '/personas/'.$id);

		$persona_res = $response->get_data();

		$this->assertEquals($response->get_status(), 404);
	}




}