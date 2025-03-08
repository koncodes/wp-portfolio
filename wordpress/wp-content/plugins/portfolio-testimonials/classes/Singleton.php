<?php

namespace KN\PortfolioTestimonials;

abstract class Singleton
{
    protected static $instance;

    abstract protected function __construct();

    public static function getInstance() {
        // self:: refers to the class that it is written in
        // static:: refers to the class that implements/calls the method
        if (!static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }
}