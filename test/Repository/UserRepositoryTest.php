<?php

namespace AhmadFaisal\Belajar\PHP\MVC\Repository;

use PHPUnit\Framework\TestCase;
use AhmadFaisal\Belajar\PHP\MVC\Config\Database;
use AhmadFaisal\Belajar\PHP\MVC\Domain\User;
use AhmadFaisal\Belajar\PHP\MVC\Repository\SessionRepository;

use function PHPUnit\Framework\assertNull;

class UserRepositoryTest extends TestCase {

  private UserRepository $userRepository;
  private SessionRepository $sessionRepository;

  protected function setUp(): void {
    $this->userRepository = new UserRepository(Database::getConnection());
    $this->sessionRepository = new SessionRepository(Database::getConnection());

    $this->sessionRepository->deleteAll();
    $this->userRepository->deleteAll();
  }

  public function testSaveSuccess() {
    $user = new User();
    $user->id = 'faisal';
    $user->name = 'faisal';
    $user->password = 'rahasia';

    $this->userRepository->save($user);

    $result = $this->userRepository->findById($user->id);

    self::assertEquals($user->id, $result->id);
    self::assertEquals($user->name, $result->name);
    self::assertEquals($user->password, $result->password);
  }

  public function testFindByIdNotFound() {
    $user = $this->userRepository->findById('notfound');
    self::assertNull($user);
  }

  public function testUpdate() {
    $user = new User();
    $user->id = 'faisal';
    $user->name = 'faisal';
    $user->password = 'rahasia';
    $this->userRepository->save($user);

    // change name
    $user->name = 'isal';
    $this->userRepository->update($user);

    $result = $this->userRepository->findById($user->id);

    $this->assertEquals($user->id, $result->id);
    $this->assertEquals($user->name, $result->name);
    $this->assertEquals($user->password, $result->password);
  }

}