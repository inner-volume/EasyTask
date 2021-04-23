<?php
namespace EasyTask;

class Constant
{
    /**
     * one
     * env_set_constant
     */

    /**
     * server_prefix_key
     */
    const SERVER_PREFIX_KEY = 'server_prefix_key';

    /**
     * server_daemon_key
     */
    const SERVER_DAEMON_KEY = 'server_daemon_key';

    /**
     * server_prefix_val
     */
    const SERVER_PREFIX_VAL = 'easy_task';

    /**
     * server_notify_key
     */
    const SERVER_NOTIFY_KEY = 'server_notify_key';

    /**
     * server_error_register_switch_key
     */
    const SERVER_CLOSE_ERROR_REGISTER_SWITCH_KEY = 'server_close_error_register_switch_key';

    /**
     * server_runtime_path
     */
    const SERVER_RUNTIME_PATH = 'server_runtime_path';

    /**
     * server_auto_recover_key
     */
    const SERVER_AUTO_RECOVER_KEY = 'server_auto_recover_key';

    /**
     * two
     * server_info_constant
     */

    /**
     * server_task_empty_tip
     */
    const SERVER_TASK_EMPTY_TIP = 'please add a process task to execute';

    /**
     * server_process_open_close_disabled_tip
     */
    const SERVER_PROCESS_OPEN_CLOSE_DISABLED_TIP = 'please enable the disabled functions popen and pclose';

    /**
     * server_task_same_name_tip
     */
    const SERVER_TASK_SAME_NAME_TIP = 'the same task name already exists';

    /**
     * server_prefix_runtime_path_empty_tip
     */
    const SERVER_PREFIX_RUNTIME_PATH_EMPTY_TIP = 'the running directory must be set before setting the task prefix';

    /**
     * server_notify_must_open_error_register_tip
     */
    const SERVER_NOTIFY_MUST_OPEN_ERROR_REGISTER_TIP = 'you must enable exception registration before using the exception notification function';

    /**
     * server_notify_params_check_tip
     */
    const SERVER_NOTIFY_PARAMS_CHECK_TIP = 'the parameter must be a string type or a closure type';

    /**
     * SERVER_CHECK_CLOSURE_TYPE_TIP
     */
    const SERVER_CHECK_CLOSURE_TYPE_TIP = 'the func parameter must belong to the closure type';
}