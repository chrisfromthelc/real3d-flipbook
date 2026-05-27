<?php

class ActivationTest extends WP_UnitTestCase
{

    public function test_version_constant_is_defined()
    {
        $this->assertTrue(defined('REAL3D_FLIPBOOK_VERSION'));
        $this->assertSame('4.23', REAL3D_FLIPBOOK_VERSION);
    }

    public function test_plugin_file_constant_is_defined()
    {
        $this->assertTrue(defined('REAL3D_FLIPBOOK_FILE'));
    }

    public function test_singleton_returns_instance()
    {
        $instance = Real3DFlipbook::get_instance();
        $this->assertInstanceOf(Real3DFlipbook::class, $instance);
    }

    public function test_singleton_returns_same_instance()
    {
        $a = Real3DFlipbook::get_instance();
        $b = Real3DFlipbook::get_instance();
        $this->assertSame($a, $b);
    }
}
