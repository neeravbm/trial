<?php

namespace RedTest\tests\entities\node\test_me\crud;

use RedTest\core\entities\User;
use RedTest\core\RedTest_Framework_TestCase;
use RedTest\entities\Node\TestMe;
use RedTest\core\Menu;

/**
 * Class AnonymousUserTest
 *
 * @package RedTest\tests\entities\node\test_me\crud
 */
class AnonymousUserTest extends RedTest_Framework_TestCase {
  /**
   * @var TestMe
   */
  private static $publishedTestMeContent;

  /**
   * @var TestMe
   */
  private static $unpublishedTestMeContent;

  /**
   * Create a published and an unpublished content of the type
   * "Test Me".
   */
  public static function setupBeforeClass() {
    $options = array(
      'required_fields_only' => FALSE,
      'status' => 'published',
    );

    // Log out the user just to make sure that there is no
    // logged in user at this point.
    User::logout();

    // Log in as user 1.
    $userObject = User::loginProgrammatically(1)->verify(get_class());

    // Create a published TestMe content with all its fields
    // filled with random values.
    self::$publishedTestMeContent = TestMe::createRandom(1, $options)->verify(
      get_class()
    );

    // Create an unpublished TestMe content with all its
    // fields filled with random values.
    $options['status'] = 'unpublished';
    self::$unpublishedTestMeContent = TestMe::createRandom(1, $options)->verify(
      get_class()
    );

    // Log the user out. After this step, the user will be
    // anonymous.
    $userObject->logout();
  }

  /**
   * Make sure that anonymous user has access to view the
   * published "Test Me" content.
   */
  public function testPublishedViewAccess() {
    // Use node_access() function to check whether user has
    // access to view the published content.
    $access = self::$publishedTestMeContent->hasViewAccess();
    $this->assertTrue(
      $access,
      'Anonymous user does not have permission to view a
      published "Test Me" content.'
    );

    // Use Menu class' hasAccess() function to check whether
    // user has access to node/<nid> page. This is a more
    // general function and can be used for custom paths as
    // well.
    $id = self::$publishedTestMeContent->getId();
    $access = Menu::hasAccess('node/' . $id);
    $this->assertTrue(
      $access,
      'Anonymous user does not have permission to view a
      published "Test Me" content.'
    );
  }

  /**
   * Make sure that anonymous user is able to view the
   * published "Test Me" content in "full" and "teaser" view
   * modes.
   */
  public function testPublishedView() {
    // Get the renderable array of the node in "full" view
    // mode. Note that a node in "full" view mode does not
    // have title inside it. The title is being rendered by
    // page tpl.
    $view = self::$publishedTestMeContent->view('full');
    $this->assertArrayHasKey(
      'field_test_me_body',
      $view,
      'Anonymous user is not able to view the body field of
      the published content in "full" view mode.'
    );
  }

  /**
   * Make sure that anonymous user does not have access to
   * view the published "Test Me" content.
   */
  public function testUnpublishedViewAccess() {
    // Use node_access() function to check whether user has
    // access to view the unpublished content.
    $access =
      self::$unpublishedTestMeContent->hasViewAccess();
    $this->assertFalse(
      $access,
      'Anonymous user has permission to view an unpublished
      "Test Me" content.'
    );

    // Use Menu class' hasAccess() function to check whether
    // user has access to node/<nid> page. This is a more
    // general function and can be used for custom paths as
    // well.
    $id = self::$unpublishedTestMeContent->getId();
    $access = Menu::hasAccess('node/' . $id);
    $this->assertFalse(
      $access,
      'Anonymous user has permission to view an unpublished
      "Test Me" content.'
    );
  }
}