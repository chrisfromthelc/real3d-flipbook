<?php

class DataLayerTest extends WP_UnitTestCase
{

    public function test_r3d_get_flipbook_returns_null_for_nonexistent_post()
    {
        $this->assertNull(r3d_get_flipbook(999999));
    }

    public function test_r3d_get_flipbook_returns_null_for_wrong_post_type()
    {
        $post_id = $this->factory->post->create(array( 'post_type' => 'post' ));
        $this->assertNull(r3d_get_flipbook($post_id));
    }

    public function test_r3d_get_flipbook_returns_options_for_valid_flipbook()
    {
        $post_id = $this->factory->post->create(array( 'post_type' => 'r3d' ));
        $options = array( 'name' => 'Test Flipbook', 'viewMode' => 'webgl' );
        update_post_meta($post_id, 'r3d_flipbook_options', $options);

        $result = r3d_get_flipbook($post_id);

        $this->assertIsArray($result);
        $this->assertSame('Test Flipbook', $result['name']);
        $this->assertSame($post_id, $result['id']);
        $this->assertSame($post_id, $result['post_id']);
    }

    public function test_r3d_save_flipbook_creates_post_meta()
    {
        $post_id = $this->factory->post->create(array( 'post_type' => 'r3d' ));
        $options = array( 'viewMode' => 'webgl' );

        $result = r3d_save_flipbook($post_id, 'My Flipbook', $options);

        $this->assertTrue($result);
        $saved = get_post_meta($post_id, 'r3d_flipbook_options', true);
        $this->assertSame('My Flipbook', $saved['name']);
        $this->assertSame($post_id, $saved['post_id']);
    }

    public function test_r3d_save_flipbook_returns_false_for_wrong_post_type()
    {
        $post_id = $this->factory->post->create(array( 'post_type' => 'post' ));
        $result  = r3d_save_flipbook($post_id, 'Test', array());
        $this->assertFalse($result);
    }

    public function test_r3d_save_flipbook_preserves_existing_notes()
    {
        $post_id = $this->factory->post->create(array( 'post_type' => 'r3d' ));
        $existing = array( 'name' => 'Old', 'notes' => 'Keep these notes' );
        update_post_meta($post_id, 'r3d_flipbook_options', $existing);

        r3d_save_flipbook($post_id, 'Updated', array( 'viewMode' => 'webgl' ));

        $saved = get_post_meta($post_id, 'r3d_flipbook_options', true);
        $this->assertSame('Keep these notes', $saved['notes']);
        $this->assertSame('Updated', $saved['name']);
    }

    public function test_r3d_delete_flipbook_data_removes_meta()
    {
        $post_id = $this->factory->post->create(array( 'post_type' => 'r3d' ));
        update_post_meta($post_id, 'r3d_flipbook_options', array( 'name' => 'Test' ));

        r3d_delete_flipbook_data($post_id);

        $this->assertEmpty(get_post_meta($post_id, 'r3d_flipbook_options', true));
    }

    public function test_r3d_get_all_flipbooks_returns_all_published()
    {
        $id1 = $this->factory->post->create(array( 'post_type' => 'r3d', 'post_status' => 'publish' ));
        $id2 = $this->factory->post->create(array( 'post_type' => 'r3d', 'post_status' => 'publish' ));
        $id3 = $this->factory->post->create(array( 'post_type' => 'r3d', 'post_status' => 'draft' ));

        update_post_meta($id1, 'r3d_flipbook_options', array( 'name' => 'Book 1' ));
        update_post_meta($id2, 'r3d_flipbook_options', array( 'name' => 'Book 2' ));
        update_post_meta($id3, 'r3d_flipbook_options', array( 'name' => 'Draft Book' ));

        $all = r3d_get_all_flipbooks();

        $this->assertCount(2, $all);
        $this->assertArrayHasKey($id1, $all);
        $this->assertArrayHasKey($id2, $all);
        $this->assertArrayNotHasKey($id3, $all);
    }

    public function test_r3d_resolve_flipbook_by_name_finds_matching_post()
    {
        $post_id = $this->factory->post->create(
            array(
            'post_type'   => 'r3d',
            'post_title'  => 'Unique Flipbook Title',
            'post_status' => 'publish',
            )
        );
        update_post_meta($post_id, 'r3d_flipbook_options', array( 'name' => 'Unique Flipbook Title' ));

        $result = r3d_resolve_flipbook_by_name('Unique Flipbook Title');

        $this->assertIsArray($result);
        $this->assertSame($post_id, $result['id']);
    }

    public function test_r3d_resolve_flipbook_by_name_returns_null_for_no_match()
    {
        $this->assertNull(r3d_resolve_flipbook_by_name('Nonexistent Book 12345'));
    }

    public function test_r3d_migrate_legacy_flipbook_migrates_from_options()
    {
        $post_id = $this->factory->post->create(array( 'post_type' => 'r3d', 'post_title' => 'Legacy Book' ));
        $legacy_id = 42;

        update_post_meta($post_id, 'flipbook_id', $legacy_id);
        update_option('real3dflipbook_42', array( 'viewMode' => 'webgl', 'pages' => array() ));

        $result = r3d_migrate_legacy_flipbook($post_id);

        $this->assertIsArray($result);
        $this->assertSame($post_id, $result['id']);
        $this->assertSame('Legacy Book', $result['name']);

        $this->assertEmpty(get_post_meta($post_id, 'flipbook_id', true));
        $this->assertFalse(get_option('real3dflipbook_42'));

        $saved = get_post_meta($post_id, 'r3d_flipbook_options', true);
        $this->assertIsArray($saved);
    }
}
