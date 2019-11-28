### 使用说明与示例
> 本篇介绍如何在 PHP 项目中调用禅道 SDK ,以下以 zentaoPHP、Tinkphp5、Laravel6 框架作为演示示例。

#### 简介
 - **禅道SDK-文档地址：** `https://www.kancloud.cn/ly978317/zentao`
 - 使用 SDK 之前，需要在引用的禅道 SDK 文件中填写一些必要配置信息。以下有三个使用参考示例，实际使用需根据实际项目情况进行调用。

#### 配置禅道SDK信息
```
//禅道部署域名
const ztUrl = 'http://zentao.*****.com';
//禅道登录账户
const ztAccount = 'admin';
//禅道登录密码
const ztPassword = '123456';
//禅道参数请求方式[ GET | PATH_INFO ]
const requestType = 'PATH_INFO';
```

#### 1.zentaoPHP 框架中引用 SDK 文件
 - 以下是 zentaoPHP 框架中调用禅道 SDK 文件获取部门列表的示例。SDK 文件可根据实际项目开发采用多种不同方式调用，不限定于示例中的一种。

```
public function deptBrowse()
{
    include_once('../../tools/zentao/zentao.php');
    $zentao = new \zentao\zentao\zentao();
    $params = array(
        'deptID' => 1
    );
    $result = $zentao->deptBrowse($params);
    echo $result;
}
```

#### 2.ThinkPHP 框架中引用 SDK 文件
 - 以下是 ThinkPHP5 框架中调用禅道 SDK 文件获取部门列表的示例。SDK 文件可根据实际项目开发采用多种不同方式调用，不限定于示例中的一种。

 ```
public function deptBrowse()
{
    include_once('../vendor/zentao/zentao.php');
    $zentao = new \zentao\zentao\zentao();
    $params = array(
        'deptID' => 1
    );
    $result = $zentao->deptBrowse($params);
    return $result;
}
 ```

#### 3. Laravel 框架中引用 SDK 文件
 - 以下是 Laravel6 框架中调用禅道 SDK 文件获取部门列表的示例。在 Laravel 中引用 禅道 SDK 文件后，还需引用 SDK 的命名空间才能使用。SDK 文件可根据实际项目开发采用多种不同方式调用，不限定于示例中的一种。

 ```
use zentao\zentao\zentao;

class IndexController
{
    public function deptBrowse()
    {
        require_once('../vendor/zentao/zentao.php');
        $zt     = new zentao();
        $params = array(
            'deptID' => 1
        );
        $result = $zt->deptBrowse($params);
        return $result;
    }
}
 ```

#### 返回结果示例

```
{
    "status": 1,
    "msg": "success",
    "result": {
        "title": "维护部门-LeiYong-禅道项目管理",
        "deptID": "1",
        "parentDepts": [
            {
                "id": "1",
                "name": "经理",
                "parent": "0",
                "path": ",1,",
                "grade": "1",
                "order": "0",
                "position": "",
                "function": "",
                "manager": ""
            }
        ],
        "sons": [
            {
                "id": "11",
                "name": "产品经理",
                "parent": "1",
                "path": ",1,11,",
                "grade": "2",
                "order": "10",
                "position": "",
                "function": "",
                "manager": ""
            },
            {
                "id": "12",
                "name": "项目经理",
                "parent": "1",
                "path": ",1,12,",
                "grade": "2",
                "order": "20",
                "position": "",
                "function": "",
                "manager": ""
            }
        ],
        "tree": [
            {
                "id": "1",
                "name": "经理",
                "parent": "0",
                "path": ",1,",
                "grade": "1",
                "order": "0",
                "position": "",
                "function": "",
                "manager": "",
                "managerName": "",
                "children": [
                    {
                        "id": "11",
                        "name": "产品经理",
                        "parent": "1",
                        "path": ",1,11,",
                        "grade": "2",
                        "order": "10",
                        "position": "",
                        "function": "",
                        "manager": "",
                        "managerName": ""
                    },
                    {
                        "id": "12",
                        "name": "项目经理",
                        "parent": "1",
                        "path": ",1,12,",
                        "grade": "2",
                        "order": "20",
                        "position": "",
                        "function": "",
                        "manager": "",
                        "managerName": ""
                    }
                ],
                "actions": {
                    "delete": false
                }
            },
            {
                "id": "2",
                "name": "开发",
                "parent": "0",
                "path": ",2,",
                "grade": "1",
                "order": "1",
                "position": "",
                "function": "",
                "manager": "",
                "managerName": ""
            },
            {
                "id": "3",
                "name": "测试",
                "parent": "0",
                "path": ",3,",
                "grade": "1",
                "order": "2",
                "position": "",
                "function": "",
                "manager": "",
                "managerName": ""
            },
            {
                "id": "4",
                "name": "市场",
                "parent": "0",
                "path": ",4,",
                "grade": "1",
                "order": "3",
                "position": "",
                "function": "",
                "manager": "",
                "managerName": ""
            },
            {
                "id": "8",
                "name": "客户",
                "parent": "0",
                "path": ",8,",
                "grade": "1",
                "order": "13",
                "position": "",
                "function": "",
                "manager": "",
                "managerName": ""
            }
        ]
    }
}
```