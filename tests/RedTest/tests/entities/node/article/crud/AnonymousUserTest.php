<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 2/19/15
 * Time: 5:00 PM
 */

namespace RedTest\tests\entities\node\article\crud;

use RedTest\core\entities\User;
use RedTest\core\RedTest_Framework_TestCase;
use RedTest\entities\Node\Article;
use RedTest\core\Menu;
use RedTest\forms\entities\Node\ArticleForm;


/**
 * Class AnonymousUserTest
 *
 * @package RedTest\tests\test\crud
 */
class AnonymousUserTest extends RedTest_Framework_TestCase {

  /**
   * @var Article
   */
  private static $publishedArticleObject;

  /**
   * @var array
   */
  private static $publishedArticleFields;

  /**
   * @var Article
   */
  private static $unpublishedArticleObject;

  /**
   * @var array
   */
  private static $unpublishedArticleFields;

  /**
   * Create an authenticated user and log in as that user.
   */
  public static function setupBeforeClass() {
    User::logout();

    $userObject = User::loginProgrammatically(1)->verify(get_class());

    // Create a published article.
    $articleForm = new ArticleForm();
    $articleForm->verify(get_class());

    self::$publishedArticleFields = $articleForm->fillRandomValues(
      array('required_fields_only' => FALSE)
    )->verify(get_class());

    self::$publishedArticleObject = $articleForm->submit()->verify(get_class());

    self::$publishedArticleObject->checkValues(self::$publishedArticleFields)
      ->verify(get_class());

    // Create an unpublished article.
    $articleForm = new ArticleForm();
    $articleForm->verify(get_class());

    $options = array(
      'required_fields_only' => FALSE,
      'status' => 'unpublished',
    );
    self::$unpublishedArticleFields = $articleForm->fillRandomValues($options)
      ->verify(get_class());

    self::$unpublishedArticleObject = $articleForm->submit()->verify(
      get_class()
    );

    self::$unpublishedArticleObject->checkValues(
      self::$unpublishedArticleFields
    )->verify(get_class());

    User::logout();
  }

  /**
   * Make sure that anonymous user does not have access to create an article.
   */
  public function testCreateAccess() {
    $this->assertFalse(
      Menu::hasAccess('node/add/article'),
      'Anonymous user has access to create an article.'
    );

    $this->assertFalse(
      Article::hasCreateAccess(),
      "Anonymous user has access to create an article."
    );
  }

  /**
   * Make sure that anonymous user has access to view a published article but
   * not an unpublished article.
   */
  public function testViewAccess() {
    $this->assertTrue(
      Menu::hasAccess('node/' . self::$publishedArticleObject->getId()),
      'Anonymous user does not have permission to view a published article.'
    );

    $this->assertTrue(
      self::$publishedArticleObject->hasViewAccess(),
      'Anonymous user does not have permission to view a published article.'
    );

    $this->assertFalse(
      Menu::hasAccess('node/' . self::$unpublishedArticleObject->getId()),
      'Anonymous user has permission to view an unpublished article.'
    );

    $this->assertFalse(
      self::$unpublishedArticleObject->hasViewAccess(),
      'Anonymous user has permission to view an unpublished article.'
    );
  }

  /**
   * Make sure that anonymous user is able to view an already created article
   * with all the fields that were filled other than the title.
   */
  public function testView() {
    $fields_to_be_checked = self::$publishedArticleFields;
    unset($fields_to_be_checked['title']);

    $view = self::$publishedArticleObject->view('full');
    foreach ($fields_to_be_checked as $field_name => $value) {
      $this->assertArrayHasKey(
        $field_name,
        $view,
        "Anonymous user is not able to view $field_name."
      );
    }

    $view = self::$publishedArticleObject->view('teaser');
    foreach ($fields_to_be_checked as $field_name => $value) {
      $this->assertArrayHasKey(
        $field_name,
        $view,
        "Anonymous user is not able to view $field_name."
      );
    }
  }

  /**
   * Make sure that anonymous user does not have access to update an article.
   */
  public function testUpdateAccess() {
    $this->assertFalse(
      Menu::hasAccess(
        'node/' . self::$publishedArticleObject->getId() . '/edit'
      ),
      'Anonymous user has permission to edit an article.'
    );

    $this->assertFalse(
      self::$publishedArticleObject->hasUpdateAccess(),
      'Anonymous user has permission to edit an article.'
    );
  }

  /**
   * Make sure that anonymous user does not have access to delete an article.
   */
  public function testDeleteAccess() {
    $this->assertFalse(
      self::$publishedArticleObject->hasDeleteAccess(),
      'Anonymous user has permission to delete an article.'
    );
  }

  /**
   * Make sure that authenticated user is able to create new articles.
   *
   * @depends testCreateAccess
   */
  /*public function testCreate() {
    $articleForm = new ArticleForm();

    list($success, $fields, $msg) = $articleForm->fillDefaultValues(
      self::$options
    );
    $this->assertTrue($success, $msg);

    list($success, $articleObject, $msg) = $articleForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $articleObject->checkValues($fields);
    $this->assertTrue($success, $msg);
  }*/

  /*public function testAllDefault() {
    $this->assertEquals(
      'node_add',
      Menu::getPageCallback('node/add/test'),
      "Page callback to add a Test node is incorrect."
    );

    $this->assertTrue(
      Test::hasCreateAccess(),
      "Authenticated user does not have access to create a Test node."
    );

    list($success, $tagsObjects, $msg) = Tags::createDefault(5);
    $this->assertTrue($success, $msg);

    $testForm = new TestForm();

    $options = array(
      'required_fields_only' => FALSE,
      'references' => array(
        'taxonomy_terms' => array(
          'tags' => $tagsObjects,
        ),
      ),
    );

    list($success, $fields, $msg) = $testForm->fillDefaultValues(
      $options
    );
    $this->assertTrue($success, $msg);

    list($success, $nodeObject, $msg) = $testForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $nodeObject->checkValues($fields);
    $this->assertTrue($success, $msg);

    $testForm = new TestForm($nodeObject->getId());

    list($success, $nodeObject, $msg) = $testForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $nodeObject->checkValues($fields);
    $this->assertTrue($success, $msg);

    $testForm = new TestForm($nodeObject->getId());

    list($success, $fields, $msg) = $testForm->fillDefaultValues(
      $options
    );
    $this->assertTrue($success, $msg);

    list($success, $nodeObject, $msg) = $testForm->submit();
    $this->assertTrue($success, $msg);

    list($success, $msg) = $nodeObject->checkValues($fields);
    $this->assertTrue($success, $msg);

    $this->assertTrue(
      $nodeObject->hasViewAccess(),
      "Authenticated user does not have access to view a Test node."
    );

    $this->assertTrue(
      $nodeObject->hasUpdateAccess(),
      "Authenticated user does not have access to update a Test node."
    );

    $this->assertTrue(
      $nodeObject->hasDeleteAccess(),
      "Authenticated user does not have access to delete a Test node."
    );
  }*/
}