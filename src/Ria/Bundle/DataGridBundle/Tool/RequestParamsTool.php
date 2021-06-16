<?php
declare(strict_types=1);

namespace Ria\Bundle\DataGridBundle\Tool;

class RequestParamsTool
{

    public static function parseQueryString(?string $queryString): array
    {
        if (!$queryString) {
            return [];
        }

        $data = preg_replace_callback('/(?:^|(?<=&))[^=[]+/', function($match) {
            return bin2hex(urldecode($match[0]));
        }, $queryString);

        parse_str($data, $values);

        return array_combine(array_map('hex2bin', array_keys($values)), $values);
    }

}