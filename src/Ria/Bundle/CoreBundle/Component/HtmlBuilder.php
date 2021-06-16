<?php

namespace Ria\Bundle\CoreBundle\Component;

final class HtmlBuilder
{

    public static function tagAttributes(array $options) : string
    {
        if (empty($options)) {
            return '';
        }

        $attributePairs = [];
        foreach ($options as $key => $val) {
            if (is_int($key)) {
                $attributePairs[] = $val;
            } else {
                $val              = htmlspecialchars((string)$val, ENT_QUOTES);
                $attributePairs[] = "{$key}=\"{$val}\"";
            }
        }

        return join(' ', $attributePairs);
    }

    public static function tag(string $name, string $content, array $options = [], bool $closeTag = true) : string
    {
        $html = "<$name " . self::tagAttributes($options) . '>';
        return $closeTag ? "$html$content</$name>" : $html;
    }

}