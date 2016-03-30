<?php
//CMS cache file, DO NOT modify me!
//Created on July 16, 2010, 12:19

$PSN_INFO = array (
  1 => 
  array (
    'PSNID' => '1',
    'Name' => '首页',
    'PSN' => 'relate::../../',
    'URL' => 'http://sesoe.com',
    'Description' => '流行网',
    'PermissionReadG' => '2,1,3',
    'publish_path' => '../../',
    'publish_type' => 'local',
  ),
  2 => 
  array (
    'PSNID' => '2',
    'Name' => '内容页',
    'PSN' => 'relate::../../sesoe',
    'URL' => 'http://sesoe.com/sesoe',
    'Description' => NULL,
    'PermissionReadG' => NULL,
    'publish_path' => '../../sesoe',
    'publish_type' => 'local',
  ),
  3 => 
  array (
    'PSNID' => '3',
    'Name' => 'js静态调用文件',
    'PSN' => 'relate::../sesoe/',
    'URL' => 'http://sesoe.com/sesoe/',
    'Description' => NULL,
    'PermissionReadG' => NULL,
    'publish_path' => '../sesoe/',
    'publish_type' => 'local',
  ),
  4 => 
  array (
    'PSNID' => '4',
    'Name' => '友情链接',
    'PSN' => 'relate::../../about',
    'URL' => 'http://sesoe.com/about',
    'Description' => NULL,
    'PermissionReadG' => NULL,
    'publish_path' => '../../about',
    'publish_type' => 'local',
  ),
  5 => 
  array (
    'PSNID' => '5',
    'Name' => 'sesoeftp',
    'PSN' => 'ftp::zsesoecom1:198110@sesoe.com:21/httpdocs/sesoe',
    'URL' => 'http://sesoe.com/sesoe/',
    'Description' => 'sesoeftp 远程发布',
    'PermissionReadG' => NULL,
    'publish_path' => '/httpdocs/sesoe',
    'publish_type' => 'ftp',
    'publish_ftp_host' => 'sesoe.com',
    'publish_ftp_port' => '21',
    'publish_ftp_user' => 'zsesoecom1',
    'publish_ftp_pass' => '198110',
  ),
);
?>