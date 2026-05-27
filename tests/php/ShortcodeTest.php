<?php

class ShortcodeTest extends WP_UnitTestCase
{

    public function test_real3dflipbook_shortcode_is_registered()
    {
        $this->assertTrue(shortcode_exists('real3dflipbook'));
    }

    public function test_shortcode_with_invalid_id_renders_container()
    {
        $output = do_shortcode('[real3dflipbook id="999999"]');
        $this->assertNotEmpty(trim($output));
        $this->assertStringContainsString('real3dflipbook', $output);
    }
}
