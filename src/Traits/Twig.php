<?php

namespace Nongbit\Twig\Traits;

trait Twig
{
    protected $data, $twig;

    public function initTwig(): void
    {
        $this->data = [];

        $this->twig = new \Nongbit\Twig\Twig();
    }

    protected function display(string $template, array $data = []): ?string
    {
        $this->data = array_merge($this->data, $data);

        return $this->twig->display($template, $this->data);
    }
}