<?php

/*********************************************************************
* Extension configuration file for ext "metadata".
*
* Generated by ext 09-09-2014 11:36 UTC
*
* https://github.com/t3elmar/Ext
*********************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Metadata extraction',
  'description' => 'PHP-based metadata extraction.',
  'category' => 'service',
  'author' => 'Fabien Udriot',
  'author_email' => 'fabien.udriot@typo3.org',
  'author_company' => 'Ecodev',
  'state' => 'stable',
  'internal' => '',
  'uploadfolder' => '0',
  'modify_tables' => '',
  'clearCacheOnLoad' => 0,
  'version' => '2.1.0-dev',
  'constraints' => 
  array (
    'depends' => 
    array (
      'cms' => '',
      'typo3' => '6.2.0-6.2.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'user' => 'fab1en',
  'comment' => 'Change log https://forge.typo3.org/versions/2843 - CMS 6.2 compatibility. Credits goes to Felix Nagel for this release.',
);

?>