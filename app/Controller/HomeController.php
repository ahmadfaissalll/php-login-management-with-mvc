<?php


namespace AhmadFaisal\Belajar\PHP\MVC\Controller;
require_once __DIR__ . '/../../vendor/autoload.php';

use AhmadFaisal\Belajar\PHP\MVC\App\View;

class HomeController {

  public function index(): void {
    $model = [
      'title' => 'Belajar PHP MVC',
      'content' => 'Selamat Belajar PHP MVC dari Programmer Zaman Now'
    ];

    View::render('Home/index', $model);
  }
  
  public function hello(): void {
    echo "HomeController.hello()";
  }

  public function world(): void {
    echo "HomeController.world()";
  }

  public function about(): void {
    echo "Author : Ahmad Faisal";
  }

}