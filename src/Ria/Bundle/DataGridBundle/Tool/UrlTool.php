<?php
declare(strict_types=1);

namespace Ria\Bundle\DataGridBundle\Tool;

class UrlTool
{

    public function changeRequestQueryString(string $url, mixed $mixedKey = [], string $value = null): string
    {
        if (is_string($mixedKey)) {
            $changeTab = ["$mixedKey" => $value];
        } else {
            $changeTab = $mixedKey;
        }

        $parseTab = parse_url($url);

        $queryString = "";

        if (array_key_exists("query", $parseTab)) {
            $queryString = $parseTab["query"];
        }

        $query = RequestParamsTool::parseQueryString($queryString);

        foreach ($changeTab as $key => $val) {
            $query[$key] = $val;
        }

        $parseTab["query"] = http_build_query($query);

        return
            ((isset($parseTab['scheme'])) ? $parseTab['scheme'] . '://' : '')
            .((isset($parseTab['user'])) ? $parseTab['user'] . ((isset($parseTab['pass'])) ? ':' . $parseTab['pass'] : '') .'@' : '')
            .((isset($parseTab['host'])) ? $parseTab['host'] : '')
            .((isset($parseTab['port'])) ? ':' . $parseTab['port'] : '')
            .((isset($parseTab['path'])) ? $parseTab['path'] : '')
            .((isset($parseTab['query'])) ? '?' . $parseTab['query'] : '')
            .((isset($parseTab['fragment'])) ? '#' . $parseTab['fragment'] : '');
    }
}
