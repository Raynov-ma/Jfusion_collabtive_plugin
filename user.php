<?php

/**
* @package JFusion_collabtive
* @author Mohammed Serbouti <serboutim@raynov.ma>
* @url www.raynov.ma
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC' ) or die('Restricted access' );

/**
 * Load the JFusion framework
 */
require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.jfusion.php');
require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.abstractuser.php');
require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.jplugin.php');

/**
 * JFusion User Class for collabtive 1.1
 * @package JFusion_collabtive
 */
class JFusionUser_collabtive extends JFusionUser {

    /**
     * @param object $userinfo
     *
     * @return null|object
     */
    function getUser($userinfo)
    {

		list($identifier_type,$identifier) = $this->getUserIdentifier($userinfo,'name','email');

        $db = JFusionFactory::getDatabase($this->getJname());

        $query = 'SELECT ID as userid, name as username, name as name, email as email, pass as password, NULL as password_salt, NULL as activation, NULL as reason, lastlogin as lastvisit, NULL as group_id '.
        		'FROM #__user '.
        		'WHERE '.$identifier_type.'=' . $db->Quote($identifier);

        $db->setQuery($query );
        $result = $db->loadObject();

        if ($result) {
        		$query = 'SELECT role FROM #__roles_assigned WHERE user = ' . $result->ID;
        		$db->setQuery($query );
        		$result->group_id = $db->loadResult();  
                $query = 'SELECT name FROM #__roles WHERE ID = ' . $result->group_id;
        		$db->setQuery($query );
                $result->group_name = $db->loadResult();
                
            $result->groups = array($result->group_id);
            $result->groupnames = array($result->group_name);

			$result->activation = '';

        }
        return $result;
    }

    /**
     * @return string
     */
    function getJname()
    {
        return 'collabtive';
    }

    /**
     * @param object $userinfo
     *
     * @return array
     */
    function deleteUser($userinfo)
    {

        $status = array('error' => array(),'debug' => array());
        $db = JFusionFactory::getDatabase($this->getJname());

		$query = 'DELETE FROM #__user WHERE name = '.$db->quote($userinfo->username);
		$db->setQuery($query);
        if (!$db->query()) {
       		$status['error'][] = JText::_('USER_DELETION_ERROR') . ' ' .  $db->stderr();
        } else {
            	$status['debug'][] = JText::_('USER_DELETION') . ' ' . $userinfo->username;
		}
        
		return $status;
    }

    /**
     * @param object $userinfo
     * @param array $options
     *
     * @return array
     */
    function destroySession($userinfo, $options)
    {
        $helper = JFusionFactory::getHelper($this->getJname());
        $helper->startSession($options);
        session_destroy();
        session_unset();
        setcookie("PHPSESSID", "");  
        $helper->closeSession();
		return array();
     }

    /**
     * @param object $userinfo
     * @param array $options
     *
     * @return array|string
     */
    function createSession($userinfo, $options)
    {$status = array('error' => array(),'debug' => array());
    $helper = JFusionFactory::getHelper($this->getJname());
    //save joomla session and start collabtive session
    $helper->startSession($options);
            $now = time();
            $_SESSION['userid'] = $userinfo->userid;
            $_SESSION['username'] = stripslashes($userinfo->username);
            $_SESSION['lastlogin'] = $now;
            $_SESSION['userlocale'] = 'en';
            $_SESSION['usergender'] = '';
            $_SESSION["userpermissions"] = $this->getUserRole($userinfo->userid);
    //get joomla session and save collabtive session 
    $helper->closeSession();
		return $status;
    }
    
   function getUserRole($role)
    {
        $db = JFusionFactory::getDatabase($this->getJname());
        $role = (int) $role;

        $query = "SELECT * FROM #__roles WHERE ID = $role";
        $db->setQuery( $query );
        $therole = $db->loadObject();

        $result["projects"] = unserialize($therole->projects);
        $result["tasks"] = unserialize($therole->tasks);
        $result["milestones"] = unserialize($therole->milestones);
        $result["messages"] = unserialize($therole->messages);
        $result["files"] = unserialize($therole->files);
        $result["timetracker"] = unserialize($therole->timetracker);
        $result["chat"] = unserialize($therole->chat);
        $result["admin"] = unserialize($therole->admin);

        if (!empty($result)) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * @param string $username
     *
     * @return string
     */
    function filterUsername($username)
    {
        //no username filtering implemented yet
        return $username;
    }

    /**
     * @param object $userinfo
     * @param object $existinguser
     * @param array $status
     *
     * @return void
     */
    function updatePassword($userinfo, &$existinguser, &$status)
    {
        $existinguser->password = sha1($userinfo->password_clear);
        $db = JFusionFactory::getDatabase($this->getJname());
        $query = 'UPDATE #__user SET pass = ' . $db->quote($existinguser->password). ' WHERE ID  = ' . $existinguser->userid;
        $db = JFusionFactory::getDatabase($this->getJname());
        $db->setQuery($query );
        if (!$db->query()) {
            $status['error'][] = JText::_('PASSWORD_UPDATE_ERROR')  . $db->stderr();
        } else {
	        $status['debug'][] = JText::_('PASSWORD_UPDATE') . ' ' . substr($existinguser->password,0,6) . '********';
        }
    }

    /**
     * @param object $userinfo
     * @param object $existinguser
     * @param array $status
     *
     * @return void
     */
    function updateUsername($userinfo, &$existinguser, &$status)
    {
//        $db = JFusionFactory::getDatabase($this->getJname());
//        $query = 'UPDATE #__user SET name = ' . $db->quote($existinguser->username). ' WHERE ID  = ' . $existinguser->userid;
//        $db = JFusionFactory::getDatabase($this->getJname());
//        $db->setQuery($query );
//        if (!$db->query()) {
//            $status['error'][] = JText::_('USERNAME_UPDATE_ERROR')  . $db->stderr();
//        } else {
//	        $status['debug'][] = JText::_('USERNAME_UPDATE') . ' ' . $existinguser->username . '->' . $userinfo->username;
//        }
    }

    /**
     * @param object $userinfo
     * @param object $existinguser
     * @param array $status
     *
     * @return void
     */
    function updateEmail($userinfo, &$existinguser, &$status)
    {
        //we need to update the email
        $db = JFusionFactory::getDatabase($this->getJname());
        $query = 'UPDATE #__user SET email ='.$db->quote($userinfo->email) .' WHERE ID =' . $existinguser->userid;
        $db->setQuery($query);
        if (!$db->query()) {
            $status['error'][] = JText::_('EMAIL_UPDATE_ERROR') . $db->stderr();
        } else {
	        $status['debug'][] = JText::_('EMAIL_UPDATE'). ': ' . $existinguser->email . ' -> ' . $userinfo->email;
        }
    }


    /**
     * @param object $userinfo
     * @param object $existinguser
     * @param array $status
     *
     * @return void
     */
    function blockUser($userinfo, &$existinguser, &$status)
    {
        $status['debug'][] = JText::_('BLOCK_UPDATE'). ': ' . $existinguser->block . ' -> ' . $userinfo->block;
    }

    /**
     * @param object $userinfo
     * @param object $existinguser
     * @param array $status
     *
     * @return void
     */
    function unblockUser($userinfo, &$existinguser, &$status)
    {
        $status['debug'][] = JText::_('BLOCK_UPDATE'). ': ' . $existinguser->block . ' -> ' . $userinfo->block;
    }

    /**
     * @param object $userinfo
     * @param object $existinguser
     * @param array $status
     *
     * @return void
     */
    function activateUser($userinfo, &$existinguser, &$status)
    {
        $status['debug'][] = JText::_('ACTIVATION_UPDATE'). ': ' . $existinguser->activation . ' -> ' . $userinfo->activation;
    }

    /**
     * @param object $userinfo
     * @param object $existinguser
     * @param array $status
     *
     * @return void
     */
    function inactivateUser($userinfo, &$existinguser, &$status)
    {
        $status['debug'][] = JText::_('ACTIVATION_UPDATE'). ': ' . $existinguser->activation . ' -> ' . $userinfo->activation;
    }

    /**
     * @param object $userinfo
     * @param array $status
     *
     * @return void
     */
    function createUser($userinfo, &$status)
    {
        //we need to create a new collabtive user
        $db = JFusionFactory::getDatabase($this->getJname());
        $params = JFusionFactory::getParams($this->getJname());
//        $source_path = $params->get('source_path');

        $usergroups =$userinfo->groups[0];// JFusionFunction::getCorrectUserGroups($this->getJname(),$userinfo);
        if (empty($usergroups)) {
            $status['error'][] = JText::_('ERROR_CREATE_USER') . ' ' . JText::_('USERGROUP_MISSING');
        } else {
            //prepare the user variables
            $user = new stdClass;
            $user->ID = NULL;
            $user->name = $userinfo->username;
            $user->email = $userinfo->email;

            if (isset($userinfo->password_clear)) {
                $user->pass = sha1($userinfo->password_clear);
            } else {
                $user->pass = $userinfo->password;
            }
            $user->lastlogin = time();
            //now append the new user data
            if (!$db->insertObject('#__user', $user )) {
                //return the error
                $status['error'] = JText::_('USER_CREATION_ERROR'). ': ' . $db->stderr();
            } else {
           $query = 'INSERT INTO #__roles_assigned (user, role) VALUES (' . (int)$user->id . ',' .(int) $usergroups[0] . ')';
           $db->setQuery($query);
           if (!$db->query()) {
               $status['error'][] = JText::_('USER_CREATION_ERROR') . $db->stderr();
           } else {
               $status['debug'][] = JText::_('USER_CREATION');
           }
           }
        }
    }
}