<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 6/2/15
 * Time: 1:40 PM
 */

namespace RedTest\tests\entities\node\article\comment;


use RedTest\core\RedTest_Framework_TestCase;
use RedTest\core\entities\User;
use RedTest\entities\Comment\ArticleComment;
use RedTest\entities\Node\Article;
use RedTest\forms\entities\Comment\ArticleCommentForm;

/**
 * Class AnonymousUserTest
 *
 * @package RedTest\tests\entities\node\article\comment
 */
class AnonymousUserTest extends RedTest_Framework_TestCase {

  //protected static $deleteCreatedEntities = FALSE;

  /**
   * Make sure that user is anonymous.
   */
  public static function setupBeforeClass() {
    User::logout();
  }

  /**
   * Make sure that anonymous user does not have permission to post comments.
   */
  /*public function testCommentPostAccess() {
    $this->assertFalse(
      user_access('post comments'),
      "Anonymous user does has permission to post comments."
    );
  }*/

  /**
   * Make sure that authenticated user is able to create comments.
   */
  public function testCommentPost() {
    list($success, $userObject, $msg) = User::loginProgrammatically(1);
    $this->assertTrue($success, $msg);

    list($success, $articleObject, $msg) = Article::createDefault();
    $this->assertTrue($success, $msg);

    $userObject->logout();

    $articleCommentForm = new ArticleCommentForm(
      NULL,
      $articleObject->getId()
    );
    $this->assertTrue($articleCommentForm->getInitialized(), $articleCommentForm->getErrors());

    list($success, $fields, $msg) = $articleCommentForm->fillDefaultValues(array('required_fields_only' => FALSE));
    $this->assertTrue($success, $msg);

    list($success, $articleCommentObject, $msg) = $articleCommentForm->submit();
    $this->assertTrue($success, $msg);
  }
}