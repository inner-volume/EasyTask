<?php
namespace EasyTask\Helper;

class UtilHelper
{
    /**
     * isWin
     * @return bool
     */
    public static function isWin()
    {
        return DIRECTORY_SEPARATOR == '\\';
    }
}

