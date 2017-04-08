<?php
// route without prefix => controller/action without current (and parent) module(s) IDs
return [
  //'confirm/<token:.+>' => 'main/confirm',
    '<action:(login|logout|profile|signup|captcha|confirm)>' => 'main/<action>',
  //'?' => 'main/login', // without URL-normalizer
];
