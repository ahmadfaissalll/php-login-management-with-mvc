<?php

namespace AhmadFaisal\Belajar\PHP\MVC\Service;

use PHPUnit\Framework\TestCase;
use AhmadFaisal\Belajar\PHP\MVC\Repository\UserRepository;
use AhmadFaisal\Belajar\PHP\MVC\Config\Database;
use AhmadFaisal\Belajar\PHP\MVC\Domain\User;
use AhmadFaisal\Belajar\PHP\MVC\Exception\ValidationException;
use AhmadFaisal\Belajar\PHP\MVC\Model\UserLoginRequest;
use AhmadFaisal\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use AhmadFaisal\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use AhmadFaisal\Belajar\PHP\MVC\Model\UserRegisterRequest;
use AhmadFaisal\Belajar\PHP\MVC\Repository\SessionRepository;

class UserServiceTest extends TestCase {

  private UserRepository $userRepository;
  private UserService $userService;
  private SessionRepository $sessionRepository;

  protected function setUp(): void {
    $connection = Database::getConnection();

    $this->sessionRepository = new SessionRepository($connection);
    $this->sessionRepository->deleteAll();

    $this->userRepository = new UserRepository($connection);
    $this->userRepository->deleteAll();
    
    $this->userService = new UserService($this->userRepository);
  }

  public function testRegisterSuccess() {
    $request = new UserRegisterRequest();
    $request->id = 'isal';
    $request->name = 'isal';
    $request->password = 'isal';
    $this->userService->register($request);

    $result = $this->userRepository->findById($request->id);

    self::assertEquals($request->id, $result->id);
    self::assertEquals($request->name, $result->name);
    self::assertTrue( password_verify('isal', $result->password) );
  }

  public function testRegisterDuplicateUser() {
    $request = new UserRegisterRequest();
    $request->id = 'isal';
    $request->name = 'isal';
    $request->password = 'isal';
    $this->userService->register($request);

    $this->expectExceptionMessage('User Id already exist');

    $request = new UserRegisterRequest();
    $request->id = 'isal';
    $request->name = 'isal';
    $request->password = 'isal';
    $this->userService->register($request);
  }

  public function testRegisterValidationError() {
    $this->expectExceptionMessage('Id, Name, Password can not blank');

    $request = new UserRegisterRequest();
    $request->id = 'f';
    $request->name = '';
    $request->password = 'f';
    $this->userService->register($request);
  }

  public function testLoginSuccess() {
    $user = new User();
    $user->id = 'isal';
    $user->name = 'isal';
    $user->password = password_hash('isal', PASSWORD_BCRYPT);

    $this->userRepository->save($user);

    $request = new UserLoginRequest();
    $request->id = 'isal';
    $request->password = 'isal';

    $response = $this->userService->login($request);

    self::assertEquals($user->id, $response->user->id);
    $this->assertEquals($user->name, $response->user->name);
    self::assertTrue( password_verify($request->password, $response->user->password) );
  }

  public function testLoginNotFound() {
    $this->expectException(ValidationException::class);

    $request = new UserLoginRequest();
    $request->id = 'kosong';
    $request->password = 'kosong';

    $this->userService->login($request);
  }

  public function testLoginWrongPassword() {
    $user = new User();
    $user->id = 'billy';
    $user->name = 'billy';
    $user->password = password_hash('billy', PASSWORD_BCRYPT);

    $this->userRepository->save($user);
    
    $this->expectException(ValidationException::class);

    $request = new UserLoginRequest();
    $request->id = 'billy';
    $request->password = 'salah';

    $this->userService->login($request);
  }

  public function testUpdateSucces() {
    $user = new User();
    $user->id = 'billy';
    $user->name = 'billy';
    $user->password = password_hash('billy', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $request = new UserProfileUpdateRequest();
    $request->id = 'billy';
    $request->name = 'bully';

    $this->userService->updateProfile($request);

    $result = $this->userRepository->findById($user->id);

    self::assertEquals($request->id, $result->id);
    self::assertEquals($request->name, $result->name);
    self::assertTrue( password_verify('billy', $result->password) );
  }

  public function testUpdateValidationError() {
    $this->expectException(ValidationException::class);
    
    $request = new UserProfileUpdateRequest();
    $request->id = '';
    $request->name = '';

    $this->userService->updateProfile($request);
  }

  public function testUpdateNotFound() {
    $this->expectException(ValidationException::class);

    $request = new UserProfileUpdateRequest();
    $request->id = 'billy';
    $request->name = 'bully';

    $this->userService->updateProfile($request);
  }

  public function testUpdatePasswordSuccess() {
    $user = new User();
    $user->id = 'james';
    $user->name = 'james';
    $user->password = password_hash('james', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $request = new UserPasswordUpdateRequest();
    $request->id = $user->id;
    $request->oldPassword = 'james';
    $request->newPassword = 'james123';
    $this->userService->updatePassword($request);

    $result = $this->userRepository->findById($request->id);

    self::assertTrue( password_verify('james123', $result->password) );
  }

  public function testUpdatePasswordValidationError() {
    $this->expectExceptionMessage("Old Password, New Password can not blank");

    $request = new UserPasswordUpdateRequest();
    $request->id = 'james';
    $request->oldPassword = '';
    $request->newPassword = 'efef';

    $this->userService->updatePassword($request);
  }

  public function testUpdatePasswordOldPasswordWrong() {
    $user = new User();
    $user->id = 'james';
    $user->name = 'james';
    $user->password = password_hash('james', PASSWORD_BCRYPT);
    $this->userRepository->save($user);

    $this->expectExceptionMessage("Old Password is wrong");

    $request = new UserPasswordUpdateRequest();
    $request->id = 'james';
    $request->oldPassword = 'jdjdj';
    $request->newPassword = 'efef';

    $this->userService->updatePassword($request);
  }

  public function testUpdatePasswordNotFound() {
    $this->expectExceptionMessage("User is not found");

    $request = new UserPasswordUpdateRequest();
    $request->id = 'billy';
    $request->oldPassword = 'bully';
    $request->newPassword = 'bully';

    $this->userService->updatePassword($request);
  }

}