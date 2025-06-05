<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Table extends Component
{
    public $headers, $footers, $id, $class;

    public function __construct($headers = [], $footers = [], $id = null, $class = null)
    {
        $this->headers = $headers;
        $this->footers = $footers;
        $this->id = $id;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.table');
    }
}
