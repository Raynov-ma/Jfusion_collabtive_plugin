<?php

/**
* @package JFusion_SMF
* @author JFusion development team
* @copyright Copyright (C) 2008 JFusion. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC' ) or die('Restricted access' );

/**
 * JFusion Public Class for SMF 1.1.x
 * For detailed descriptions on these functions please check the model.abstractpublic.php
 * @package JFusion_SMF
 */
class JFusionPublic_collabtive extends JFusionPublic {
    /**
     * @var $callbackdata object
     */
//    var $callbackdata = null;
    /**
     * @var bool $callbackbypass
     */
//    var $callbackbypass = null;


    /**
     * @return string
     */
    function getJname()
	{
		return 'collabtive';
	}
    /**
     * @return string
     */
    function getLostPasswordURL() {
        return 'account/forgotten_password.php';
    }

    /**
     * @return string
     */
    function getRegistrationURL()
	{
		return '';
	}
	
    

    /**
     * @return string
     */
    function getLostUsernameURL()
	{
		return '';
	}

}