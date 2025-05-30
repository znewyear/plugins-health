<?php

namespace Health\Core;

/**
 * 健康检查项配置对象
 */
class Inspection
{
    protected $name;
    protected $check;
    protected $require;
    protected $raws = [];

    public function __construct(array $config)
    {
        $this->raws = $config;
        $this->name = $config['name'] ?? '';
        $this->check = $config['check'] ?? null;
        $this->require = $config['require'] ?? Health::REQUIRE_REQUIRED;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCheck()
    {
        return $this->check;
    }

    public function getRequire()
    {
        return $this->require;
    }

    public function getLevel()
    {
        return $this->require;
    }

    public function get($key, $default = null)
    {
        return $this->raws[$key] ?? $default;
    }

    public function raws()
    {
        return $this->raws;
    }
}
