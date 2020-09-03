<?php

namespace MVC\Views;

class Views
{
    private $templatesPath;

    private $extraVars = [];

    public function __construct(string $templatesPath)
    {
        $this->templatesPath = $templatesPath;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function setVars(string $name, $value)
    {
        $this->extraVars[$name] = $value;
    }

    public function renderHTML(string $templateName, array $vars = [], int $code = 200)
    {
        http_response_code($code);

        extract($this->extraVars);
        extract($vars);

        ob_start();
        include $this->templatesPath . '/' . $templateName;
        $buffer = ob_get_contents();
        ob_end_clean();

        echo $buffer;
    }
}