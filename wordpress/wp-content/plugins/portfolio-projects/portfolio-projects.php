<?php
/*
Plugin Name: Portfolio Projects
Description: Add projects to your portfolio WordPress site. 
Version: 1.0.0
Author: Konika Nahar
Text Domain: kn-projects
*/

namespace KN\PortfolioProjects;

const TEXT_DOMAIN = 'kn-projects';
const PLUGIN_FILE = __FILE__;

//include classes
require_once "classes/Singleton.php";
require_once "classes/Plugin.php";
require_once "classes/ProjectPostType.php";
require_once "classes/ProjectLanguage.php";
require_once "classes/ProjectMeta.php";
require_once "classes/RecentProjectsShortcode.php";
require_once "classes/ProjectSettings.php";


Plugin::getInstance();
