<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', true);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE') or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ') or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS') or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

defined('ADD_ENROLL') or define('ADD_ENROLL', 1);
defined('ADD_RENT') or define('ADD_RENT', 2);
defined('ADD_ORDER') or define('ADD_ORDER', 3);
defined('EDIT_ENROLL') or define('EDIT_ENROLL', 4);
defined('EDIT_RENT') or define('EDIT_RENT', 5);
defined('EDIT_ORDER') or define('EDIT_ORDER', 6);
defined('REFUND_ENROLL') or define('REFUND_ENROLL', 7);
defined('REFUND_RENT') or define('REFUND_RENT', 8);
defined('STOP_ENROLL') or define('STOP_ENROLL', 9);
defined('RESUME_ENROLL') or define('RESUME_ENROLL', 10);

# defined('REFUND_ORDER') or define('REFUND_ORDER', 9);
# defined('STOP_ENROLL') or define('STOP_ENROLL', 10);
# defined('RESUME_ENROLL') or define('RESUME_ENROLL', 11);
# defined('STOP_RENT') or define('STOP_RENT', 12);
# defined('RESUME_RENT') or define('RESUME_RENT', 13);

defined('TRANSFER_ENROLL') or define('TRANSFER_ENROLL', 14);
defined('TRANSFER_RENT') or define('TRANSFER_RENT', 15);
defined('TRANSFER_ORDER') or define('TRANSFER_ORDER', 16);

defined('REFUND_ORDER') or define('REFUND_ORDER', 20);

defined('ADD_OTHER') or define('ADD_OTHER', 23);
defined('REFUND_OTHER') or define('REFUND_OTHER', 24);
defined('ADD_COMMISSION') or define('ADD_COMMISSION', 25);
defined('STOP_RENT') or define('STOP_RENT', 26);
defined('RESUME_RENT') or define('RESUME_RENT', 27);
defined('BRANCH_TRANSFER') or define('BRANCH_TRANSFER', 28);

defined('SUB_ORDER_ID') or define('SUB_ORDER_ID', 1);
defined('PRIMARY_COURSE_ID') or define('PRIMARY_COURSE_ID', 4);

defined('SMS_ID')             OR define('SMS_ID','');
defined('SMS_KEY')             OR define('SMS_KEY','');
defined('SMS_ID2')             OR define('SMS_ID2','');
defined('SMS_KEY2')             OR define('SMS_KEY2','');
defined('SMS_ID3')             OR define('SMS_ID3','');
defined('SMS_KEY3')             OR define('SMS_KEY3','');
defined('SMS_ID4')             OR define('SMS_ID4','');
defined('SMS_KEY4')             OR define('SMS_KEY4','');
defined('SMS_FEE') or define('SMS_FEE', array('sms' => 8.4, 'lms' => 25.9, 'mms' => 60));
