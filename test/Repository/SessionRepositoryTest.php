<?php

namespace AhmadFaisal\Belajar\PHP\MVC\Repository;

use PHPUNIT\Framework\Assert;
use PHPUnit\Framework\TestCase;
use AhmadFaisal\Belajar\PHP\MVC\Config\Database;
use AhmadFaisal\Belajar\PHP\MVC\Domain\Session;
use AhmadFaisal\Belajar\PHP\MVC\Domain\User;
use AhmadFaisal\Belajar\PHP\MVC\Repository\UserRepository;

class SessionRepositoryTest extends TestCase {

  private SessionRepository $sessionRepository;
  private UserRepository $userRepository;

  protected function setUp(): void {
    $this->userRepository = new UserRepository(Database::getConnection());
    $this->sessionRepository = new SessionRepository(Database::getConnection());
    
    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();

    $user = new User();
    $user->id = 'isal';
    $user->name = 'isal';
    $user->password = password_hash('isal', PASSWORD_BCRYPT);
    
    $this->userRepository->save($user);
  }

  public function testSaveSuccess() {
    $session = new Session();
    $session->id = uniqid();
    $session->userId = 'isal';

    $this->sessionRepository->save($session);

    $result = $this->sessionRepository->findById($session->id);

    Assert::assertEquals($session->id, $result->id);
    self::assertEquals($session->userId, $result->userId);
  }

  public function testDeleteByIdSuccess() {
    $session = new Session();
    $session->id = uniqid();
    $session->userId = 'isal';

    $this->sessionRepository->save($session);

    $result = $this->sessionRepository->findById($session->id);

    Assert::assertEquals($session->id, $result->id);
    self::assertEquals($session->userId, $result->userId);

    $this->sessionRepository->deleteById($session->id);

    $result = $this->sessionRepository->findById($session->id);
    self::assertNull($result);
  }
  
  public function testFindByIdNotFound() {
    $result = $this->sessionRepository->findById('notFound');
    self::assertNull($result);
  }

  public function tearDown(): void {
    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();
  }

}