<?php

spl_autoload_register(function ($class) {
    $paths = ['./models/', './controllers/', './mail/', './interfaces/'];
    foreach ($paths as $path) {
        $file = "{$path}{$class}.php";
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});