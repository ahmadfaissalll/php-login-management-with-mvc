<?php

namespace AhmadFaisal\Belajar\PHP\MVC\App;

use PHPUnit\Framework\TestCase;


class ViewTest extends TestCase {

  /**
   * @test
   */
  public function render() {

    View::render('Home/index', ['PHP Login Management']);

    $this->expectOutputRegex('[PHP Login Management]');
    $this->expectOutputRegex('[html]');
    $this->expectOutputRegex('[body]');
    $this->expectOutputRegex('[Login Management]');
    $this->expectOutputRegex('[Register]');
    $this->expectOutputRegex('[Login]');
  }

}