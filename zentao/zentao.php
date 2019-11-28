<?php

namespace zentao\zentao;

/**
 * This is the PHP-SDK class of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD,  www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yong Lei <chunsheng@cnezsoft.com>
 * @package     api
 * @version     $Id: control.php 5143 2013-07-15 06:11:59Z leiyong208@gmail.com $
 * @link        http://www.zentao.net
 */
class zentao
{
    //ZenTaoPMS deploys domain names.
    const ztURL = 'http://****.com';
    //ZenTaoPMS login account.
    const ztAccount = 'admin';
    //ZenTaoPMS login password.
    const ztPassword = 'asd123456';
    //Parameter request method. [GET|PATH_INFO]
    const requestType = 'PATH_INFO';
    //Session authentication.
    public $sessionAuth = '';
    //Interface request parameter.
    public $params = array();
    //Session random number for some encryption and verification.
    public $sessionRand = 0;
    //Return result.
    public $returnResult = array(
        'status' => 0,
        'msg'    => 'error',
        'result' => array()
    );

    /**
     * Get the session ID required for the session.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->params      = array(
            'm' => 'api',
            'f' => 'getSessionID'
        );
        $result            = $this->getUrl(self::ztURL);
        $resultData        = json_decode($result);
        $sessionData       = json_decode($resultData->data);
        $this->sessionAuth = $sessionData->sessionName . '=' . $sessionData->sessionID;
        $this->login();
    }

    /**
     * User login verification.
     *
     * @access public
     * @return string
     */
    public function login()
    {
        $this->params = array(
            'account'  => self::ztAccount,
            'password' => self::ztPassword
        );
        if (self::requestType == 'GET') {
            $this->params = array_merge($this->params, array(
                'm' => 'user',
                'f' => 'login'
            ));
            $result       = $this->getUrl(self::ztURL);
        } elseif (self::requestType == 'PATH_INFO') {
            $result = $this->postUrl(self::ztURL . '/user-login.json');
        }
        return $result;
    }

    /**
     * Get a list of departments.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function deptBrowse($optionalParams = array())
    {
        $this->params = array(
            'm' => 'dept',
            'f' => 'browse'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') === 0) {
            $sessionData = json_decode($resultData->data);
            if (!empty($sessionData->tree)) {
                $returnResult = array(
                    'status' => 1,
                    'msg'    => 'success',
                    'result' => array(
                        'title'       => $sessionData->title,
                        'deptID'      => $sessionData->deptID,
                        'parentDepts' => $sessionData->parentDepts,
                        'sons'        => $sessionData->sons,
                        'tree'        => $sessionData->tree
                    )
                );
            }
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add a new department.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function deptManageChild($optionalParams = array())
    {
        $returnResult = $this->returnResult;
        if (self::requestType == 'GET') {
            $this->params = array();
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '?m=dept&f=manageChild&t=json');
        } elseif (self::requestType == 'PATH_INFO') {
            $this->params = array();
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '/dept-manageChild.json');
        }
        if (strpos($result, 'reload')) {
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array()
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get user list.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function companyBrowse($optionalParams = array())
    {
        $this->params = array(
            'm' => 'company',
            'f' => 'browse',
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title' => $resultList->title,
                    'users' => $resultList->users
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add user optional information.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function userCreateInfo($optionalParams = array())
    {
        $this->params = array(
            'm' => 'user',
            'f' => 'create'
        );

        $returnResult = $this->returnResult;
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList        = json_decode($resultData->data);
            $returnResult      = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'     => $resultList->title,
                    'depts'     => $resultList->depts,
                    'groupList' => $resultList->groupList,
                    'roleGroup' => $resultList->roleGroup
                )
            );
            $this->sessionRand = $resultList->rand;
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * New users.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function userCreate($optionalParams = array())
    {
        //Get the random number required for encryption.
        $this->userCreateInfo();
        $returnResult                     = $this->returnResult;
        $this->params                     = array();
        $optionalParams['password1']      = md5($optionalParams['password1'] . $this->sessionRand);
        $optionalParams['password2']      = md5($optionalParams['password2'] . $this->sessionRand);
        $optionalParams['verifyPassword'] = md5(md5(self::ztPassword) . $this->sessionRand);
        if (self::requestType == 'GET') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '?m=user&f=create&dept=' . $optionalParams['dept'] . '&t=json');
        } elseif (self::requestType == 'PATH_INFO') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '/user-create-' . $optionalParams['dept'] . '.json');
        }
        $resultData = json_decode($result);
        if (strcmp($resultData->result, 'success') == 0) {
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => $resultData->message
            );
            return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
        }
        $returnResult['result'] = $resultData->message;
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get product list.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function productAll($optionalParams = array())
    {
        $this->params = array(
            'm' => 'product',
            'f' => 'all'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'        => $resultList->title,
                    'products'     => $resultList->products,
                    'productStats' => $resultList->productStats
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get added product optional information.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function productCreateInfo($optionalParams = array())
    {
        $this->params = array(
            'm' => 'product',
            'f' => 'create'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'    => $resultList->title,
                    'products' => $resultList->products,
                    'lines'    => $resultList->lines,
                    'poUsers'  => $resultList->poUsers,
                    'qdUsers'  => $resultList->qdUsers,
                    'rdUsers'  => $resultList->rdUsers,
                    'groups'   => $resultList->groups
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add a single product.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function productCreate($optionalParams = array())
    {
        $returnResult = $this->returnResult;
        $this->params = array();
        if (self::requestType == 'GET') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '?m=product&f=create&t=json');
        } elseif (self::requestType == 'PATH_INFO') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '/product-create.json');
        }
        $resultData = json_decode($result);
        if (strcmp($resultData->result, 'success') == 0) {
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => $resultData->message
            );
            return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
        }
        $returnResult['result'] = $resultData->message;
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get item list.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function projectAll($optionalParams = array())
    {
        $this->params = array(
            'm' => 'project',
            'f' => 'all'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'        => $resultList->title,
                    'projects'     => $resultList->projects,
                    'projectStats' => $resultList->projectStats,
                    'teamMembers'  => $resultList->teamMembers,
                    'users'        => $resultList->users
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Gets optional information for adding items.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function projectCreateInfo($optionalParams = array())
    {
        $this->params = array(
            'm' => 'project',
            'f' => 'create'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'       => $resultList->title,
                    'projects'    => $resultList->projects,
                    'groups'      => $resultList->groups,
                    'allProducts' => $resultList->allProducts
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add a single item.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function projectCreate($optionalParams = array())
    {
        $this->params = array();
        $returnResult = $this->returnResult;
        if (self::requestType == 'GET') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '?m=project&f=create&t=json');
        } elseif (self::requestType == 'PATH_INFO') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '/project-create.json');
        }
        $resultData = json_decode($result);
        if (strcmp($resultData->result, 'success') == 0) {
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => $resultData->message
            );
        }
        $returnResult['result'] = $resultData->message;
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get task list.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function projectTask($optionalParams = array())
    {
        $this->params = array(
            'm' => 'project',
            'f' => 'task'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'    => $resultList->title,
                    'projects' => $resultList->projects,
                    'project'  => $resultList->project,
                    'products' => $resultList->products,
                    'tasks'    => $resultList->tasks
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add task optional information.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function taskCreateInfo($optionalParams = array())
    {
        $this->params = array(
            'm' => 'task',
            'f' => 'create'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'            => $resultList->title,
                    'projects'         => $resultList->projects,
                    'users'            => $resultList->users,
                    'stories'          => $resultList->stories,
                    'moduleOptionMenu' => $resultList->moduleOptionMenu,
                    'project'          => $resultList->project
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add a single task.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function taskCreate($optionalParams = array())
    {
        $returnResult = $this->returnResult;
        $this->params = array(
            'status' => 'wait',
            'after'  => 'toTaskList'
        );
        if (self::requestType == 'GET') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '?m=task&f=create&projectID=' . $optionalParams['project'] . '&t=json');
        } elseif (self::requestType == 'PATH_INFO') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '/task-create-' . $optionalParams['project'] . '.json');
        }
        $resultData = json_decode($result);
        if (strcmp($resultData->result, 'success') == 0) {
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => $resultData->message
            );
        }
        $returnResult['result'] = $resultData->message;
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Optional information for completing a single task.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function taskFinishInfo($optionalParams = array())
    {
        $this->params = array(
            'm' => 'task',
            'f' => 'finish'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'   => $resultList->title,
                    'users'   => $resultList->users,
                    'task'    => $resultList->task,
                    'project' => $resultList->project,
                    'actions' => $resultList->actions
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Complete a single task.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function taskFinish($optionalParams = array())
    {
        $returnResult = $this->returnResult;
        $this->params = array(
            'status' => 'done'
        );
        $taskID       = $optionalParams['taskID'];
        unset($optionalParams['taskID']);
        if (self::requestType == 'GET') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '?m=task&f=finish&taskID=' . $taskID . '&t=json');
        } elseif (self::requestType == 'PATH_INFO') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '/task-finish-' . $taskID . '.json');
        }
        if (strpos($result, 'task-view-' . $taskID . '.json') || strpos($result, 'taskID=' . $taskID)) {
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array()
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get BUG List.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function bugBrowse($optionalParams = array())
    {
        $this->params = array(
            'm' => 'bug',
            'f' => 'browse'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'       => $resultList->title,
                    'products'    => $resultList->products,
                    'productID'   => $resultList->productID,
                    'productName' => $resultList->productName,
                    'product'     => $resultList->product,
                    'moduleName'  => $resultList->moduleName,
                    'modules'     => $resultList->modules,
                    'browseType'  => $resultList->browseType,
                    'bugs'        => $resultList->bugs
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add single BUG optional information.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function bugCreateInfo($optionalParams = array())
    {
        $this->params = array(
            'm' => 'bug',
            'f' => 'create'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'            => $resultList->title,
                    'productID'        => $resultList->productID,
                    'productName'      => $resultList->productName,
                    'projects'         => $resultList->projects,
                    'moduleOptionMenu' => $resultList->moduleOptionMenu,
                    'users'            => $resultList->users,
                    'stories'          => $resultList->stories,
                    'builds'           => $resultList->builds
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add a single bug.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function bugCreate($optionalParams = array())
    {
        $returnResult = $this->returnResult;
        $this->params = array(
            'status' => 'active'
        );
        if (self::requestType == 'GET') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '?m=bug&f=create&productID=' . $optionalParams['product'] . '&t=json');
        } elseif (self::requestType == 'PATH_INFO') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '/bug-create-' . $optionalParams['product'] . '.json');
        }
        $resultData = json_decode($result);
        if (strcmp($resultData->result, 'success') == 0) {
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => $resultData->message
            );
        }
        $returnResult['result'] = $resultData->message;
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Optional information for solving a single bug.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function bugResolveInfo($optionalParams = array())
    {
        $this->params = array(
            'm' => 'bug',
            'f' => 'resolve'
        );
        $this->params = array_merge($this->params, $optionalParams);
        $result       = $this->getUrl(self::ztURL);
        $resultData   = json_decode($result);
        $returnResult = $this->returnResult;
        if (strcmp($resultData->status, 'success') == 0) {
            $resultList   = json_decode($resultData->data);
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array(
                    'title'    => $resultList->title,
                    'products' => $resultList->products,
                    'bug'      => $resultList->bug,
                    'users'    => $resultList->users,
                    'builds'   => $resultList->builds,
                    'actions'  => $resultList->actions
                )
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Solve a single bug.
     *
     * @param array $optionalParams
     * @access public
     * @return string
     */
    public function bugResolve($optionalParams = array())
    {
        $returnResult = $this->returnResult;
        $this->params = array(
            'status' => 'resolved'
        );
        $bugID        = $optionalParams['bugID'];
        unset($optionalParams['bugID']);
        if (self::requestType == 'GET') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '?m=bug&f=resolve&bugID=' . $bugID . '&t=json');
        } elseif (self::requestType == 'PATH_INFO') {
            $this->params = array_merge($this->params, $optionalParams);
            $result       = $this->postUrl(self::ztURL . '/bug-resolve-' . $bugID . '.json');
        }
        if (strpos($result, 'bug-view-' . $bugID . '.json') || strpos($result, 'bugID=' . $bugID)) {
            $returnResult = array(
                'status' => 1,
                'msg'    => 'success',
                'result' => array()
            );
        }
        return json_encode($returnResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Send a get request.
     *
     * @param string $url
     * @access public
     * @return string
     */
    public function getUrl($url)
    {
        $ch = curl_init();
        if (self::requestType == 'GET') {
            $this->params = array_merge($this->params, array('t' => 'json'));
            if (!empty($this->params) && count($this->params)) {
                if (strpos($url, '?') !== false) {
                    $url .= http_build_query($this->params);
                } else {
                    $url .= '?' . http_build_query($this->params);
                }
            }
        } elseif (self::requestType == 'PATH_INFO') {
            $params = implode('-', $this->params);
            $url    = $url . '/' . $params . '.json';
        }
        curl_setopt($ch, CURLOPT_COOKIE, $this->sessionAuth);
        curl_setopt($ch, CURLOPT_REFERER, self::ztURL);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Send a post request.
     *
     * @param string $url
     * @access public
     * @return string
     */
    public function postUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIE, $this->sessionAuth);
        curl_setopt($ch, CURLOPT_REFERER, self::ztURL);
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($this->params)) {
            if (is_array($this->params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->params));
            } else if (is_string($this->params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
            }
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}