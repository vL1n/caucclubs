<?php
//配置文件
return [

    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__STATIC__' => '/static',
    ],
    'paginate'               => [
        'type'      => 'BootstrapDetailed',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
];
