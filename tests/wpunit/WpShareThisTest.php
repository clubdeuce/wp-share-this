<?php
use Clubdeuce\WPShareThis\WP_Share_This;

require_once dirname( dirname( __DIR__ ) ) . '/wp-share-this.php';

/**
 * Class WpShareThisTest
 * @coversDefaultClass \Clubdeuce\WPShareThis\WP_Share_This
 */
class WpShareThisTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

	/**
	 * @var WP_UnitTest_Factory_For_Attachment
	 */
	protected $attachment_factory;

	/**
	 * @var WP_UnitTest_Factory_For_Post
	 */
	protected $post_factory;

    protected function _before()
    {
    	$this->post_factory       = new WP_UnitTest_Factory_For_Post();
	    $this->attachment_factory = new WP_UnitTest_Factory_For_Attachment();

	    WP_Share_This::initialize();
    	WP_Share_This::register_id( 'foo' );
	    WP_Share_This::register_service( 'facebook');
    }

    protected function _after()
    {
    }

	/**
	 * @covers ::the_sharing_links
	 * @covers ::register_service
	 * @covers ::_render_sharing_link
	 * @covers ::_item_sharing_property
	 * @covers ::_item_sharing_count
	 */
	public function testTheSharingLinks()
	{
		/**
		 * @var WP_Post $post
		 */
		$post    = $this->post_factory->create_and_get();
		$image   = $this->attachment_factory->create_upload_object(dirname(__DIR__) . '/_data/overheard-facebook.png', $post->ID);
		$excerpt = wp_trim_excerpt($post->post_excerpt);
		$title   = $post->post_title;

		set_post_thumbnail( $post->ID, $image );

		ob_start();
		WP_Share_This::the_sharing_links( $post );

		$html = ob_get_clean();

		$this->assertInternalType('string', $html);
		$this->assertRegExp('#class="st-custom-button.*?" #', $html);
		$this->assertRegExp('#<div data-network="facebook".*?\>#', $html);
		$this->assertRegExp('#data-url="http://example\.org/\?p=[0-9]*?"#', $html);
		$this->assertRegExp('#data-image="http://example\.org/wp-content/uploads/[0-9]{4}/[0-9]{2}/overheard-facebook[-]*[0-9]*?\.png"#', $html);
		$this->assertRegExp("#data-title=\"{$title}\"#", $html);
		$this->assertRegExp("#data-description=\"{$excerpt}\"#", $html);
		$this->assertRegExp('#<span class="count"><\/span>#', $html);
	}

	/**
	 * @covers ::initialize
	 */
	public function testHooks()
	{
		$this->assertGreaterThan(0, has_action('wp_head',            array( WP_Share_This::class, '_wp_head')));
		$this->assertGreaterThan(0, has_action('wp_enqueue_scripts', array( WP_Share_This::class, '_wp_enqueue_scripts')));
	}

	/**
	 * @covers  ::_wp_enqueue_scripts
	 * @depends testHooks
	 */
	public function testScriptIsRegistered()
	{
		do_action('wp_enqueue_scripts');
		$this->assertTrue(wp_script_is('sharethis', 'enqueued'));
	}

}