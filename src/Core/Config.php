<?php

namespace Health\Core;

/**
 * 健康检查通用配置仓库，支持点语法
 */
class Config
{
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
            return;
        }
        $segments = explode('.', $key);
        $arr = &$this->items;
        while (count($segments) > 1) {
            $segment = array_shift($segments);
            if (!isset($arr[$segment]) || !is_array($arr[$segment])) {
                $arr[$segment] = [];
            }
            $arr = &$arr[$segment];
        }
        $arr[array_shift($segments)] = $value;
    }

    public function get($key, $default = null)
    {
        $segments = explode('.', $key);
        $arr = $this->items;
        foreach ($segments as $segment) {
            if (is_array($arr) && array_key_exists($segment, $arr)) {
                $arr = $arr[$segment];
            } else {
                return $default;
            }
        }
        return $arr;
    }

    public function has($key)
    {
        return $this->get($key, '__not_found__') !== '__not_found__';
    }

    public function all()
    {
        return $this->items;
    }
}
