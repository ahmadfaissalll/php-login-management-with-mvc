<?php

namespace AhmadFaisal\Belajar\PHP\MVC\App {

  function header(string $value) {
    echo $value;
  }

}

namespace AhmadFaisal\Belajar\PHP\MVC\Service {

  function setcookie(string $name, string $value) {
    echo "$name: $value";
  }

}