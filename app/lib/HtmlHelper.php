<?php

namespace app\lib;

class HtmlHelper
{
    public static function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}