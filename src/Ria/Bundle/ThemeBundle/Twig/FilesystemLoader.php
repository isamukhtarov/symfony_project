<?php
declare(strict_types=1);

namespace Ria\Bundle\ThemeBundle\Twig;

use Ria\Bundle\ThemeBundle\ActiveTheme;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader as BaseFilesystemLoader;

class FilesystemLoader extends BaseFilesystemLoader
{

    /** Identifier of the main namespace. */
    const MAIN_NAMESPACE = '__main__';

    private $rootPath;

    private ActiveTheme $activeTheme;

    /**
     * @param string|array $paths    A path or an array of paths where to look for templates
     * @param string|null  $rootPath The root path common to all relative paths (null for getcwd())
     */
    public function __construct($paths = [], string $rootPath = null)
    {
        $this->rootPath = (null === $rootPath ? getcwd() : $rootPath).\DIRECTORY_SEPARATOR;
        if (false !== $realPath = realpath($rootPath)) {
            $this->rootPath = $realPath.\DIRECTORY_SEPARATOR;
        }

        if ($paths) {
            $this->setPaths($paths);
        }

        parent::__construct($paths, $rootPath);
    }

    public function setActiveTheme(ActiveTheme $activeTheme)
    {
        $this->activeTheme = $activeTheme;
    }

    /**
     * @return string|null
     */
    protected function findTemplate(string $name, bool $throw = true)
    {
        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (isset($this->errorCache[$name])) {
            if (!$throw) {
                return null;
            }

            throw new LoaderError($this->errorCache[$name]);
        }

        try {
            $this->validateName($name);

            [$namespace, $shortname] = $this->parseName($name);
        } catch (LoaderError $e) {
            if (!$throw) {
                return null;
            }

            throw $e;
        }

        if (!isset($this->paths[$namespace])) {
            $this->errorCache[$name] = sprintf('There are no registered paths for namespace "%s".', $namespace);

            if (!$throw) {
                return null;
            }

            throw new LoaderError($this->errorCache[$name]);
        }

        foreach ($this->paths[$namespace] as $path) {
            if (!$this->isAbsolutePath($path)) {
                $path = $this->rootPath.$path;
            }

            if ($namespace == 'RiaWeb') {
                $path = $path . DIRECTORY_SEPARATOR . $this->activeTheme->getCurrentTheme();
            }

            if (is_file($path.'/'.$shortname)) {
                if (false !== $realpath = realpath($path.'/'.$shortname)) {
                    return $this->cache[$name] = $realpath;
                }

                return $this->cache[$name] = $path.'/'.$shortname;
            }
        }

        $this->errorCache[$name] = sprintf('Unable to find template "%s" (looked into: %s).', $name, implode(', ', $this->paths[$namespace]));

        if (!$throw) {
            return null;
        }

        throw new LoaderError($this->errorCache[$name]);
    }

    private function normalizeName(string $name): string
    {
        return preg_replace('#/{2,}#', '/', str_replace('\\', '/', $name));
    }

    private function parseName(string $name, string $default = self::MAIN_NAMESPACE): array
    {
        if (isset($name[0]) && '@' == $name[0]) {
            if (false === $pos = strpos($name, '/')) {
                throw new LoaderError(sprintf('Malformed namespaced template name "%s" (expecting "@namespace/template_name").', $name));
            }

            $namespace = substr($name, 1, $pos - 1);
            $shortname = substr($name, $pos + 1);

            return [$namespace, $shortname];
        }

        return [$default, $name];
    }

    private function validateName(string $name): void
    {
        if (false !== strpos($name, "\0")) {
            throw new LoaderError('A template name cannot contain NUL bytes.');
        }

        $name = ltrim($name, '/');
        $parts = explode('/', $name);
        $level = 0;
        foreach ($parts as $part) {
            if ('..' === $part) {
                --$level;
            } elseif ('.' !== $part) {
                ++$level;
            }

            if ($level < 0) {
                throw new LoaderError(sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
            }
        }
    }

    private function isAbsolutePath(string $file): bool
    {
        return strspn($file, '/\\', 0, 1)
            || (\strlen($file) > 3 && ctype_alpha($file[0])
                && ':' === $file[1]
                && strspn($file, '/\\', 2, 1)
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
            ;
    }

}