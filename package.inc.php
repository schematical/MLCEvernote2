<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user1a
 * Date: 4/27/13
 * Time: 7:00 PM
 * To change this template use File | Settings | File Templates.
 */
define('__MLC_EVERNOTE__', dirname(__FILE__));
define('__MLC_EVERNOTE_CORE__', __MLC_EVERNOTE__ . '/_core');

define('__MLC_EVERNOTE_CORE_CTL__', __MLC_EVERNOTE_CORE__ . '/ctl');
define('__MLC_EVERNOTE_CORE_VIEW__', __MLC_EVERNOTE_CORE__ . '/view');
define('__MLC_EVERNOTE_CORE_MODEL__', __MLC_EVERNOTE_CORE__ . '/model');
define('__MLC_EVERNOTE_BATCH__', __MLC_EVERNOTE_CORE__ . '/batch');
define('__MLC_EVERNOTE_API__', __MLC_EVERNOTE_CORE__ . '/api');
define('__MLC_EVERNOTE_CG__', __MLC_EVERNOTE__ . '/_codegen');
require_once(__MLC_EVERNOTE_CORE_MODEL__ . '/_enum.inc.php');
require_once(__MLC_EVERNOTE_CORE__ . '/_lib/lib/Evernote/Client.php');


MLCApplicationBase::$arrClassFiles['MLCEvernoteDriver'] = __MLC_EVERNOTE_CORE_MODEL__ . '/MLCEvernoteDriver.class.php';

MLCApplicationBase::$arrClassFiles['MLCApiEvernote'] = __MLC_EVERNOTE_CORE_MODEL__ . '/MLCApiEvernote.class.php';





