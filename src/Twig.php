<?php

namespace Nongbit\Twig;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;

class Twig
{
    private $config, $globals, $filters, $functions;
    private $loader, $twig;

    public function __construct()
    {
        $config = config('Twig');

        $this->config = [
            'debug' => getenv('CI_ENVIRONMENT') === 'development',
            'cache' => getenv('CI_ENVIRONMENT') === 'development' ? false : WRITEPATH . 'twig',
            'autoescape' => isset($config->autoescapes) ? $config->autoescape : false,
            'paths' => isset($config->paths) ? array_unique($config->paths) : [APPPATH . 'Views'],
            'fileExtension' => isset($config->fileExtension) ? $config->fileExtension : 'html',
        ];

        $this->globals = $this->filters = $this->functions = [];
    }

    public function addPath(string|array $paths): void
    {
        if (!is_array($paths)) $paths = [$paths];

        $this->config['paths'] = array_unique(array_merge($paths, $this->config['paths']));
    }

    public function addGlobals(string|array $globals, mixed $value = null): void
    {
        if (is_string($globals)) $globals = [$globals => $value];

        $this->globals = array_merge($this->globals, $globals);
    }

    public function addFilters(string|array $filters, $callable = null): void
    {
        if (is_string($filters)) $filters = [$filters => $callable];

        foreach ($filters as $filter => $callable) {
            if (!is_string($filter)) $filter = $callable;
            if (is_null($callable)) $callable = $filter;
            array_push($this->filters, [$filter => $callable]);
        }
    }

    public function addFunctions(string|array $functions, $callable = null): void
    {
        if (is_string($functions)) $functions = [$functions => $callable];

        foreach ($functions as $function => $callable) {
            if (!is_string($function)) $function = $callable;
            if (is_null($callable)) $callable = $function;
            array_push($this->functions, [$function => $callable]);
        }
    }

    private function registerGlobals(): void
    {
        foreach ($this->globals as $name => $value) {
            $this->twig->addGlobal($name, $value);
        }
    }

    private function registerFilters(): void
    {
        foreach ($this->filters as $filter) {
            $this->twig->addFilter(new TwigFilter(key($filter), current($filter)));
        }
    }

    private function registerFunctions(): void
    {
        foreach ($this->functions as $function) {
            $this->twig->addFunction(new TwigFunction(key($function), current($function)));
        }
    }

    private function init(): void
    {
        $this->loader = new FileSystemLoader($this->config['paths']);
        $this->twig = new Environment($this->loader, $this->config);

        if ($this->config['debug']) {
            $this->twig->addExtension(new DebugExtension());
            $this->addFunctions(['d', 'dd']);
        }

        $this->twig->addExtension(new IntlExtension());

        $this->addFunctions(['site_url', 'base_url']);

        $this->registerGlobals();
        $this->registerFilters();
        $this->registerFunctions();
    }

    public function display(string $template, array $data = []): ?string
    {
        $this->init();

        return $this->twig->display("{$template}.{$this->config['fileExtension']}", $data);
    }
}