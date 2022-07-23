<?php

namespace AhmadFaisal\Belajar\PHP\MVC\Middleware {

  require_once __DIR__ . '/../Helper/helper.php';

    use AhmadFaisal\Belajar\PHP\MVC\Config\Database;
    use AhmadFaisal\Belajar\PHP\MVC\Domain\User;
    use AhmadFaisal\Belajar\PHP\MVC\Domain\Session;
    use AhmadFaisal\Belajar\PHP\MVC\Repository\SessionRepository;
    use AhmadFaisal\Belajar\PHP\MVC\Repository\UserRepository;
    use AhmadFaisal\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

  class MustLoginMiddlewareTest extends TestCase
  {

    private MustLoginMiddleware $middleware;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
      $this->middleware = new MustLoginMiddleware();
      $this->sessionRepository = new SessionRepository(Database::getConnection());

      $this->userRepository = new UserRepository(Database::getConnection());

      $this->sessionRepository->deleteAll();
      $this->userRepository->deleteAll();

      putenv('mode=test');
    }

    public function testBeforeGuest()
    {
      $this->middleware->before();

      $this->expectOutputRegex("[Location: /users/login]");
    }

    public function testBeforeLoginUser() {
      $user = new User();
      $user->id = 'adam';
      $user->name = 'adam';
      $user->password = password_hash('adam', PASSWORD_BCRYPT);
      $this->userRepository->save($user);

      $session = new Session();
      $session->id = uniqid();
      $session->userId = "adam";
      $this->sessionRepository->save($session);

      $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

      $this->middleware->before();

      $this->expectOutputString("");
    }

  }
}