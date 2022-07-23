<?php

namespace AhmadFaisal\Belajar\PHP\MVC\Service;

require_once __DIR__ . '/../Helper/helper.php';

use AhmadFaisal\Belajar\PHP\MVC\Config\Database;
use AhmadFaisal\Belajar\PHP\MVC\Domain\Session;
use AhmadFaisal\Belajar\PHP\MVC\Domain\User;
use AhmadFaisal\Belajar\PHP\MVC\Repository\SessionRepository;
use AhmadFaisal\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase {

  private SessionService $sessionService;
  private SessionRepository $sessionRepository;
  private UserRepository $userRepository;

  protected function setUp(): void {
    $this->sessionRepository = new SessionRepository(Database::getConnection());
    $this->userRepository = new UserRepository(Database::getConnection());
    $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();

    $user = new User();
    $user->id = 'isal';
    $user->name = 'isal';
    $user->password = password_hash('isal', PASSWORD_BCRYPT);

    $this->userRepository->save($user);
  }

  public function testCreate() {
    $session = $this->sessionService->create('isal');

    $this->expectOutputRegex("[X-SAL-SESSION: $session->id]");

    $result = $this->sessionRepository->findById($session->id);

    self::assertEquals($session->id, $result->id);
    self::assertEquals('isal', $result->userId);
  }

  public function testDestroy() {
    $session = new Session();
    $session->id = uniqid();
    $session->userId = 'isal';

    $this->sessionRepository->save($session);

    $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

    $this->sessionService->destroy();

    $this->expectOutputRegex("[X-SAL-SESSION: ]");
    
    // $result = $this->sessionService->current();
    $result = $this->sessionRepository->findById($session->id);

    self::assertNull($result);
  }

  public function testCurrent() {
    $session = new Session();
    $session->id = uniqid();
    $session->userId = 'isal';

    $this->sessionRepository->save($session);

    $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

    $user = $this->sessionService->current();

    self::assertEquals($session->userId, $user->id);
  }

}