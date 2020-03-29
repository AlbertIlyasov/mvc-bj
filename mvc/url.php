<?php

namespace MVC;

class Url
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function build(array $data = []): string
    {
        $url = array_merge($this->request->query, $data);
        if (isset($data[0])) {
            $url = array_merge(['r' => array_shift($data)], $data);
        }
        $query = [];
        foreach ($url as $key => $val) {
            $query[] = sprintf('%s=%s', $key, urlencode($val));
        }
        return '?' . implode('&', $query);
    }
}
