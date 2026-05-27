<?php

class SanitizationTest extends WP_UnitTestCase
{

    public function test_r3d_sanitize_array_sanitizes_strings()
    {
        $input  = array( 'key' => '<script>alert(1)</script>' );
        $result = r3d_sanitize_array($input);
        $this->assertSame('', $result['key']);
    }

    public function test_r3d_sanitize_array_handles_nested_arrays()
    {
        $input = array(
        'level1' => array(
        'level2' => '<b>bold</b>',
        ),
        );
        $result = r3d_sanitize_array($input);
        $this->assertSame('bold', $result['level1']['level2']);
    }

    public function test_r3d_sanitize_array_preserves_clean_strings()
    {
        $input  = array( 'name' => 'My Flipbook' );
        $result = r3d_sanitize_array($input);
        $this->assertSame('My Flipbook', $result['name']);
    }
}
