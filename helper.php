<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* @package JFusion_collabtive
* @author Mohammed Serbouti <serboutim@raynov.ma>
* @url www.raynov.ma
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
class JFusionHelper_collabtive
{
    var $joomlaSessionName = '';
    var $joomlaSessionId = '';
    var $joomlaSessionUseCookies = '';
    var $joomlaSessionCookieParams = '';
    /**
     * Returns the name for this plugin
     *
     * @return string
     */
    function getJname()
    {
        return 'collabtive';
    }

    /**
     * @return string
     */
    function getCookieName() {
        $cookie_name = "PHPSESSID";
        return $cookie_name;
    }

    /**
     * Backup Joomla session info and start one for the software
     *
     * @param array $options login options
     */
    function startSession($options = array()) {
        $params = JFusionFactory::getParams($this->getJname());
		$this->joomlaSessionName = session_name();
		$this->joomlaSessionId = session_id();
		$this->joomlaSessionCookieParams = session_get_cookie_params();

		//close Joomla session
		session_write_close();
        //initialize  session
		if (!$this->joomlaSessionUseCookies = ini_get('session.use_cookies')) {
			ini_set('session.use_cookies', 1);
		}
		ini_set('session.save_handler', 'files');
        
        //set cookie lifetime
		$lifetime = $params->get('cookie_expires', null)?$params->get('cookie_expires', null):3600;
		$cookie_name = $this->getCookieName();
		$cookie_domain = $params->get('cookie_domain', null);
		$cookie_path = $params->get('cookie_path', null);
		$secure = $params->get('secure', null);
		$httponly = $params->get('httponly', null);
		$session_name = $cookie_name ;
		session_set_cookie_params($lifetime, $cookie_path, $cookie_domain, $secure, $httponly);
		session_name($session_name);
		session_start();
    }

    /**
     * Close session and restore Joomla
     */
    function closeSession() {
		session_write_close();
    	session_set_cookie_params($this->joomlaSessionCookieParams['lifetime'], $this->joomlaSessionCookieParams['path'], $this->joomlaSessionCookieParams['domain'], $this->joomlaSessionCookieParams['secure'], $this->joomlaSessionCookieParams['httponly']);
        ini_set('session.use_cookies', $this->joomlaSessionUseCookies);
		session_name($this->joomlaSessionName);
		session_id($this->joomlaSessionId);
		session_start();
    }

    /**
     * @param $getVar
     * @return mixed
     */
   /* function getConfig( $getVar ) {
        static $config = array();

        if (isset($config[$getVar])) {
            return $config[$getVar];
        }

        $params = JFusionFactory::getParams($this->getJname());
        $source_path = $params->get('source_path');

        $paths = $this->includeFramework($source_path);
        $IP = $source_path;
        foreach($paths as $path) {
            include($path);
        }
        $config[$getVar] = (isset($$getVar)) ? $$getVar : '';
        return $config[$getVar];
    }*/

    /**
     * @param $source_path
     * @return array
     */
   /* function includeFramework( & $source_path ) {
        //check for trailing slash and generate file path
        if (substr($source_path, -1) == DS) {
            //remove it so that we can make it compatible with collabtive MW_INSTALL_PATH
            $source_path = substr($source_path, 0, -1);
        }

        $return[] = $source_path . DS. 'includes'. DS. 'DefaultSettings.php';
        $return[] = $source_path . DS. 'LocalSettings.php';

        $paths[] = $source_path . DS. 'includes'. DS. 'Defines.php';
        $paths[] = $source_path . DS. 'includes'. DS. 'IP.php';
        $paths[] = $source_path . DS. 'includes'. DS. 'WebRequest.php';
        $paths[] = $source_path . DS. 'includes'. DS. 'SiteConfiguration.php';
        defined ('collabtive') or define( 'collabtive',TRUE );
        defined ('MW_INSTALL_PATH') or define('MW_INSTALL_PATH', $source_path);
        foreach($paths as $path) {
            include_once($path);
        }
        return $return;
    }*/
}
