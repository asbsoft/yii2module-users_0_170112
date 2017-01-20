<?php
// route without prefix => controller/action without module uniqueId
return [
    'index/<page:\d+>'                                          => 'admin/index',
    '<action:(update|delete|view)>/<id:\d+>'                    => 'admin/<action>',
    '<action:(change-status)>/status-<id:\d+>/<value:\-?\d+>'   => 'admin/<action>',
    '<action:(index|login|logout|change-status)>'               => 'admin/<action>',
    '?'                                                         => 'admin/index',
];
