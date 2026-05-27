<?php

class RestApiTest extends WP_UnitTestCase
{

    public function test_create_endpoint_is_registered()
    {
        $routes = rest_get_server()->get_routes();
        $this->assertArrayHasKey('/flipbook/v1/create', $routes);
    }

    public function test_update_endpoint_is_registered()
    {
        $routes = rest_get_server()->get_routes();
        $this->assertArrayHasKey('/flipbook/v1/update', $routes);
    }

    public function test_create_requires_authentication()
    {
        wp_set_current_user(0);
        $request = new WP_REST_Request('POST', '/flipbook/v1/create');
        $request->set_param('title', 'Test');
        $response = rest_get_server()->dispatch($request);
        $this->assertContains($response->get_status(), array( 401, 403 ));
    }

    public function test_update_requires_authentication()
    {
        wp_set_current_user(0);
        $request  = new WP_REST_Request('POST', '/flipbook/v1/update');
        $response = rest_get_server()->dispatch($request);
        $this->assertContains($response->get_status(), array( 401, 403 ));
    }
}
