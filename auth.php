<?php


// no direct access
defined('_JEXEC' ) or die('Restricted access' );

/**
 * JFusion Authentication Class for collabtive 1.1
 * @package JFusion_collabtive
 */
class JFusionAuth_collabtive extends JFusionAuth {
    /**
     * @param array|object $userinfo
     * @return string
     */
    function generateEncryptedPassword($userinfo)
    {
        $testcrypt = sha1($userinfo->password_clear) ;
        return $testcrypt;
    }
}
