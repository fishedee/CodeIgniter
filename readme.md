FishCI
======

#概述
基于ci的3.0版本开发的增强CI框架，基本框架结构不变，加入一些语法糖，多了更强大的library以及helper，并修复ci存在的各种bug

#扩展helper
* 加入array_column_merge函数

#扩展library
* 加入微信sdk
* 加入QQ的sdk
* 加入七牛云存储的sdk
* 加入阿里云开放搜索服务的sdk
* 加入PHPExcel库
* 加入uedit库
* 加入PHPMailer库，ci自带的mail根本不能用，163邮箱和qq邮箱都不行
* 加入upload库，自动处理base64图像与普通图像
* 加入image库，切割压缩优化图片库
* 加入http库，包装curl库，接口跟前台jquery的ajax一致


#扩展功能
* @view 语法糖
* @trans 语法糖
* 加入MyException库，抛出异常自动打日志
* 加入phpunit单元测试支持，引入__initialize初始化，以及Mock方式
* 加入WARN级别的日志等级
* 加入Timer库，ci也能做定时任务了
* 加入chinese语言库，错误提示更友好
* 加入enum实现

#修复
* 修复ci的Disallowed Key Characters的bug
* 修复ci的redis session不支持hhvm的bug

#使用方法
跟标准ci一样，就这么简单
