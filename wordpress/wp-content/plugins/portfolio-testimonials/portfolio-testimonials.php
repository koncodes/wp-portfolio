<?php
/*
Plugin Name: Portfolio Testimonials
Description: Add testimonials to your portfolio WordPress site. 
Version: 1.0.0
Author: Konika Nahar
Text Domain: kn-testimonials
*/

namespace KN\PortfolioTestimonials;

const TEXT_DOMAIN = 'kn-testimonials';
const PLUGIN_FILE = __FILE__;

//include classes
require_once "classes/Singleton.php";
require_once "classes/Plugin.php";
require_once "classes/TestimonialPostType.php";
require_once "classes/TestimonialTag.php";
require_once "classes/TestimonialMeta.php";
require_once "classes/RecentTestimonialsShortcode.php";
require_once "classes/TestimonialSettings.php";


Plugin::getInstance();
