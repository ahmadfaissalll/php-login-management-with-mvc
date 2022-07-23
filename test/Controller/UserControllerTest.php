<?php



namespace AhmadFaisal\Belajar\PHP\MVC\Controller {

  require_once __DIR__ . '/../Helper/helper.php';

  use AhmadFaisal\Belajar\PHP\MVC\Repository\UserRepository;
  use AhmadFaisal\Belajar\PHP\MVC\Domain\User;
  use AhmadFaisal\Belajar\PHP\MVC\Domain\Session;
  use AhmadFaisal\Belajar\PHP\MVC\Config\Database;
  use AhmadFaisal\Belajar\PHP\MVC\Repository\SessionRepository;
    use AhmadFaisal\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

  class UserControllerTest extends TestCase
  {

    private UserController $userController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
      $this->userController = new UserController();

      $this->sessionRepository = new SessionRepository(Database::getConnection());
      $this->sessionRepository->deleteAll();

      $this->userRepository = new UserRepository(Database::getConnection('prod'));
      $this->userRepository->deleteAll();

      putenv('mode=test');
    }

    public function testRegister()
    {
      $this->userController->register();

      $this->expectOutputRegex('[Id]');
      $this->expectOutputRegex('[Register]');
      $this->expectOutputRegex('[Name]');
      $this->expectOutputRegex('[Password]');
      $this->expectOutputRegex('[Register new User]');
    }

    public function testPostRegisterSuccess()
    {
      $_POST['id'] = 'isal';
      $_POST['name'] = 'isal';
      $_POST['password'] = 'isal';

      $this->userController->postRegister();

      $this->expectOutputRegex("[Location: /users/login]");
    }

    public function testPostRegisterValidationError()
    {
      $_POST['id'] = '';
      $_POST['name'] = '';
      $_POST['password'] = '';

      $this->userController->postRegister();

      $this->expectOutputRegex('[Id]');
      $this->expectOutputRegex('[Register]');
      $this->expectOutputRegex('[Name]');
      $this->expectOutputRegex('[Password]');
      $this->expectOutputRegex('[Register new User]');
      $this->expectOutputRegex('[Id, Name, Password can not blank]');
    }

    public function testPostRegisterDuplicate()
    {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password = 'isal';

      $this->userRepository->save($user);

      $_POST['id'] = 'isal';
      $_POST['name'] = 'isal';
      $_POST['password'] = 'isal';

      $this->userController->postRegister();

      $this->expectOutputRegex('[Id]');
      $this->expectOutputRegex('[Register]');
      $this->expectOutputRegex('[Name]');
      $this->expectOutputRegex('[Password]');
      $this->expectOutputRegex('[Register new User]');
      $this->expectOutputRegex('[User Id already exist]');
    }

    public function testLogin() {
      $this->userController->login();

      $this->expectOutputRegex('[Login User]');
      $this->expectOutputRegex('[Id]');
      $this->expectOutputRegex('[Password]');
    }
    
    public function testLoginSuccess() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);

      $this->userRepository->save($user);

      $_POST['id'] = 'isal';
      $_POST['password'] = 'isal';

      $this->userController->postLogin();
  
      $this->expectOutputRegex('[Location: /]');
      $this->expectOutputRegex('[X-SAL-SESSION: ]');
    }

    public function testLoginValidationError() {
      $_POST['id'] = '';
      $_POST['password'] = '';

      $this->userController->postLogin();

      $this->expectOutputRegex('[Login User]');
      $this->expectOutputRegex('[Id]');
      $this->expectOutputRegex('[Password]');
      $this->expectOutputRegex('[Id, Password can not blank]');
    }
    
    public function testLoginUserNotFound() {
      $_POST['id'] = 'notFound';
      $_POST['password'] = 'notFound';
  
      $this->userController->postLogin();
  
      $this->expectOutputRegex('[Login User]');
      $this->expectOutputRegex('[Id]');
      $this->expectOutputRegex('[Password]');
      $this->expectOutputRegex('[Id or password is wrong]');
    }

    public function testLoginWrongPassword() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);

      $this->userRepository->save($user);

      $_POST['id'] = 'isal';
      $_POST['password'] = 'notFound';
  
      $this->userController->postLogin();
  
      $this->expectOutputRegex('[Login User]');
      $this->expectOutputRegex('[Id]');
      $this->expectOutputRegex('[Password]');
      $this->expectOutputRegex('[Id or password is wrong]');
    }

    public function testLogout() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $session = new Session();
      $session->id = uniqid();
      $session->userId = $user->id;
      $this->sessionRepository->save($session);

      $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

      $this->userController->logout();

      $this->expectOutputRegex('[Location: /]');
      $this->expectOutputRegex('[X-SAL-SESSION: ]');
    }

    public function testUpdateProfile() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $session = new Session();
      $session->id = uniqid();
      $session->userId = $user->id;
      $this->sessionRepository->save($session);

      $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

      $this->userController->updateProfile();

      $this->expectOutputRegex("[Profile]");
      $this->expectOutputRegex("[Id]");
      $this->expectOutputRegex("[isal]");
      $this->expectOutputRegex("[Name]");
      $this->expectOutputRegex("[isal]");
    }

    public function testPostUpdateProfileSuccess() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $session = new Session();
      $session->id = uniqid();
      $session->userId = $user->id;
      $this->sessionRepository->save($session);

      $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

      $_POST['name'] = 'budy';
      $this->userController->postUpdateProfile();

      $this->expectOutputRegex('[Location: /]');

      $result = $this->userRepository->findById('isal');
      self::assertEquals('budy', $result->name);
    }

    public function testPostUpdateProfileValidationError() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $session = new Session();
      $session->id = uniqid();
      $session->userId = $user->id;
      $this->sessionRepository->save($session);

      $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

      $_POST['name'] = '';
      $this->userController->postUpdateProfile();

      $this->expectOutputRegex("[Profile]");
      $this->expectOutputRegex("[Id]");
      $this->expectOutputRegex("[isal]");
      $this->expectOutputRegex("[Name]");
      $this->expectOutputRegex('[Name can not blank]');
    }

    public function testUpdatePassword() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $session = new Session();
      $session->id = uniqid();
      $session->userId = $user->id;
      $this->sessionRepository->save($session);

      $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

      $this->userController->updatePassword();

      $this->expectOutputRegex("[Password]");
      $this->expectOutputRegex("[Id]");
      $this->expectOutputRegex("[isal]");
      $this->expectOutputRegex("[Old Password]");
      $this->expectOutputRegex("[New Password]");
    }

    public function testUpdatePasswordSuccess() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $session = new Session();
      $session->id = uniqid();
      $session->userId = $user->id;
      $this->sessionRepository->save($session);

      $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

      $_POST['oldPassword'] = 'isal';
      $_POST['newPassword'] = 'isal123';

      $this->userController->postUpdatePassword();

      $this->expectOutputRegex("[Location: /]");

      $result = $this->userRepository->findById($user->id);

      self::assertTrue( password_verify('isal123', $result->password) );
    }

    public function testUpdatePasswordOldPasswordWrong() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $session = new Session();
      $session->id = uniqid();
      $session->userId = $user->id;
      $this->sessionRepository->save($session);

      $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

      $_POST['oldPassword'] = 'salah';
      $_POST['newPassword'] = 'isal123';

      $this->userController->postUpdatePassword();

      $this->expectOutputRegex('[Password]');
      $this->expectOutputRegex('[Id]');
      $this->expectOutputRegex('[Old Password is wrong]');
      $this->expectOutputRegex('[isal]');
      $this->expectOutputRegex('[Old Password]');
      $this->expectOutputRegex('[New Password]');
    }

    public function testUpdatePasswordValidationError() {
      $user = new User();
      $user->id = 'isal';
      $user->name = 'isal';
      $user->password =  password_hash('isal', PASSWORD_BCRYPT);
      $this->userRepository->save($user);


      $session = new Session();
      $session->id = uniqid();
      $session->userId = $user->id;
      $this->sessionRepository->save($session);

      $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

      $_POST['oldPassword'] = '';
      $_POST['newPassword'] = '';

      $this->userController->postUpdatePassword();

      $this->expectOutputRegex('[Old Password, New Password can not blank]');
      $this->expectOutputRegex('[Password]');
      $this->expectOutputRegex('[Id]');
      $this->expectOutputRegex('[isal]');
      $this->expectOutputRegex('[Old Password]');
      $this->expectOutputRegex('[New Password]');

    }

  }
}