<?php

/**
* @package JFusion_collabtive
* @author Mohammed Serbouti
* @copyright Copyright (C) 2008 JFusion. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC' ) or die('Restricted access' );

/**
 * Load the JFusion framework
 */
//require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.jfusion.php');
//require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.abstractadmin.php');

/**
 * JFusion Admin Class for collabtive 1.1
 * @package JFusion_collabtive
 */
class JFusionAdmin_collabtive extends JFusionAdmin{

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
    function getTablename()
    {
        return 'user';
    }

    /**
     * @return array
     */
    function setupFromPath($forumPath)
    {
            $source_path = $params->get('source_path');
            
            $myfile = $forumPath . DS . 'onfig' . DS . 'standard' . DS . 'config.php';
            if (($file_handle = @fopen($myfile, 'r')) === false) {
            JError::raiseWarning(500, JText::_('WIZARD_FAILURE') . ": $myfile " . JText::_('WIZARD_MANUAL'));
            } else {
                require_once ($myfile);

            $params['database_host'] = $db_host;
            $params['database_type'] = 'mysql';
            $params['database_name'] = $db_name;
            $params['database_user'] = $db_user;
            $params['database_password'] = $db_pass;
            $params['database_prefix'] = '';
            $params['source_url'] = '';
            $params['cookie_name'] = '';
            $params['source_path'] = $forumPath;
            }
        return $params;
    }

    /**
     * Returns the a list of users of the integrated software
     *
     * @param int $limitstart start at
     * @param int $limit number of results
     *
     * @return array
     */
    function getUserList($limitstart = 0, $limit = 0)
    {
        // initialise some objects
        $db = JFusionFactory::getDatabase($this->getJname());
        $query = 'SELECT name, email from #__user';
        $db->setQuery($query,$limitstart,$limit);
        $userlist = $db->loadObjectList();

        return $userlist;
    }

    /**
     * @return int
     */
    function getUserCount()
    {
        //getting the connection to the db
        $db = JFusionFactory::getDatabase($this->getJname());
        $query = 'SELECT count(*) from #__user';
        $db->setQuery($query );

        //getting the results
        return $db->loadResult();
    }

    /**
     * get default user group list
     *
     * @return array array with object with default user group list
     */
    function getUsergroupList()
    {
        //getting the connection to the db
        $db = JFusionFactory::getDatabase($this->getJname());
        $query = 'SELECT  ID, name FROM #__roles';
        $db->setQuery($query);
        $usergrouplist = $db->loadObjectList();
        //append the default usergroup
        $default_group = new stdClass;
        $default_group->id = 2;
        $default_group->name = 'User';
        $usergrouplist[] = $default_group;
        return $usergrouplist;
    }

    /**
     * @return string
     */
    function getDefaultUsergroup()
    {
        return 'User';
    }

    /**
     * @return bool
     */
    function allowRegistration()
    { //allowRegistration from collabtive web site

        return false;
    }
    /**
     * do plugin support multi usergroups
     *
     * @return bool
     */
    function isMultiGroup()
	{
		return false;
	}

    /**
     * do plugin support multi usergroups
     *
     * @return string UNKNOWN or JNO or JYES or ??
     */
    function requireFileAccess()
	{
		return 'JNO';
	}
}

