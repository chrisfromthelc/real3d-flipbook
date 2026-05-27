<?php

class PostTypeTest extends WP_UnitTestCase
{

    public function test_r3d_post_type_is_registered()
    {
        $this->assertTrue(post_type_exists('r3d'));
    }

    public function test_r3d_category_taxonomy_is_registered()
    {
        $this->assertTrue(taxonomy_exists('r3d_category'));
    }

    public function test_r3d_author_taxonomy_is_registered()
    {
        $this->assertTrue(taxonomy_exists('r3d_author'));
    }

    public function test_r3d_post_type_supports_title_and_thumbnail()
    {
        $this->assertTrue(post_type_supports('r3d', 'title'));
        $this->assertTrue(post_type_supports('r3d', 'thumbnail'));
    }

    public function test_r3d_post_type_is_excluded_from_search()
    {
        $post_type_obj = get_post_type_object('r3d');
        $this->assertTrue($post_type_obj->exclude_from_search);
    }

    public function test_r3d_post_type_has_archive()
    {
        $post_type_obj = get_post_type_object('r3d');
        $this->assertTrue($post_type_obj->has_archive);
    }

    public function test_r3d_category_is_hierarchical()
    {
        $taxonomy = get_taxonomy('r3d_category');
        $this->assertTrue($taxonomy->hierarchical);
    }
}
