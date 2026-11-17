<?php

namespace Tests\Feature;

use Tests\TestCase;

class RedirectIfUppercaseTest extends TestCase
{
    /**
     * Test that a GET request with uppercase letters in the URL path is redirected (301) to lowercase.
     */
    public function test_redirects_uppercase_path_to_lowercase()
    {
        // /Minsk -> /minsk (should be 301 redirect)
        $response = $this->get('/Minsk');
        $response->assertRedirect('/minsk');
        $this->assertEquals(301, $response->getStatusCode());
    }

    /**
     * Test that query parameters are preserved and their case is NOT changed.
     */
    public function test_preserves_query_parameters_and_their_case()
    {
        // /Minsk?page=2&Token=AbCd -> /minsk?page=2&Token=AbCd
        $response = $this->get('/Minsk?page=2&Token=AbCd');
        $response->assertRedirect('/minsk?page=2&Token=AbCd');
        $this->assertEquals(301, $response->getStatusCode());
    }

    /**
     * Test that query parameters with uppercase characters do NOT trigger redirect if the path is lowercase.
     */
    public function test_does_not_redirect_if_only_query_params_have_uppercase()
    {
        // /minsk?page=2&Token=AbCd (path is already lowercase)
        // Note: we can use a real route or home route '/' to test this since '/' is always lowercase.
        $response = $this->get('/?Token=AbCd');
        
        // It should NOT redirect, meaning it should load normally (status 200)
        $response->assertStatus(200);
    }

    /**
     * Test that fully lowercase URLs do not trigger any redirect.
     */
    public function test_does_not_redirect_lowercase_urls()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test that POST requests are NOT redirected by the middleware.
     */
    public function test_does_not_redirect_post_requests()
    {
        // Attempt a POST request on a route with uppercase (even if not registered, it should either 404 or csrf-fail, but NOT 301 redirect)
        $response = $this->post('/Login');
        
        // It should not be a redirect, or at least not a 301 redirection to lowercase path
        $this->assertNotEquals(301, $response->getStatusCode());
    }
}
