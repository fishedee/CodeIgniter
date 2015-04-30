FishCI
======

#概述
基于ci的3.0版本开发的增强CI框架，基本框架结构不变，加入一些语法糖，多了更强大的library以及helper，并修复ci存在的各种bug

#功能
* 加入underscore的helper
* 加入微信sdk以及QQ的sdk
* 加入PHPExcel库
* 加入uedit库
* 加入PHPMailer库，ci自带的mail根本不能用，163邮箱和qq邮箱都不行
* 加入更易用的upload库和image库
* 加入curl库包装的http库，接口跟前台jquery的ajax一致
* 加入chinese语言库，错误提示更友好
* 加入标准的MyException库
* 加入枚举体库以补充PHP没有枚举类型
* 加入Timer库，ci也能做定时任务了

#语法糖
* @view 语法糖
  原来controller中读取view的写法
```php
function index()
{
  $result = $this->model->get();
  if( $result['code'] != 0){
    $this->load->view('json',$result);
  }
  $this->load->view('json',$result);
}
```
 现在controller中读取view的写法
```php
/**
* @view json
*/
function index()
{
  return $this->model->get();
}
```

#修复
* 修复ci的Disallowed Key Characters的bug

#使用方法
跟标准ci一样，就这么简单
