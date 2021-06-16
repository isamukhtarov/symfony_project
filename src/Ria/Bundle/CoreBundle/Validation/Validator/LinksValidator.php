<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Validation\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LinksValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (empty($value)) return;

        preg_match_all('/<a href=\\"([^\\"]*)\\">(.*)<\\/a>/iU', $value, $matches);

        $notValidUrls = [];
        foreach ($matches[1] as $i => $link) {
            if (!$this->isValidDomain($link))
                $notValidUrls[] = $matches[2][$i];
        }

        if (!empty($notValidUrls)) {
            $message = sprintf("%s <br> <b>%s</b>",
                'Check the correctness of the following links:',
                implode('<br>', $notValidUrls)
            );
            $this->context->buildViolation($message)->addViolation();
        }
    }

    private function isValidDomain(string $url): bool
    {
        if (preg_match('/mailto:[^\?]*/', $url))
            return true;

        if (filter_var($url, FILTER_VALIDATE_URL) === false)
            return false;

        $domain = parse_url($url)['host'] ?? null;

        if (is_null($domain))
            return true;

        return (preg_match("/(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]/i", $domain) // valid chars check
            && preg_match("/^.{1,253}$/", $domain) // overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)); // length of each label
    }
}