<?php


namespace AhmadFaisal\Belajar\PHP\MVC\Controller;

use AhmadFaisal\Belajar\PHP\MVC\Config\Database;
use AhmadFaisal\Belajar\PHP\MVC\Repository\SessionRepository;
use AhmadFaisal\Belajar\PHP\MVC\Repository\UserRepository;
use AhmadFaisal\Belajar\PHP\MVC\Domain\User;
use AhmadFaisal\Belajar\PHP\MVC\Domain\Session;
use AhmadFaisal\Belajar\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase {

  private HomeController $homeController;
  private UserRepository $userRepository;
  private SessionRepository $sessionRepository;

  protected function setUp(): void{
    $this->homeController = new HomeController();
    $connection = Database::getConnection();
    $this->userRepository = new UserRepository($connection);
    $this->sessionRepository = new SessionRepository($connection);

    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();
  }

  /**
   * @test
   */
  public function guest() {
    $this->homeController->index();

    $this->expectOutputRegex("[Login Management]");
  }

  /**
   * @test
   */
  public function userLogin() {
    $user = new User();
    $user->id = 'isal';
    $user->name = 'isal';
    $user->password = 'isal';
    $this->userRepository->save($user);

    $session = new Session();
    $session->id = uniqid();
    $session->userId = 'isal';
    $this->sessionRepository->save($session);

    $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

    $this->homeController->index();

    $this->expectOutputRegex("[Hello isal]");
  }

}