<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LedgerExportFromView implements FromView
{
    protected $data;
    protected $viewPath;

    public function __construct($data, $viewPath)
    {
        $this->data = $data;
        $this->viewPath = $viewPath;
    }

    public function view(): View
    {
        return view($this->viewPath, $this->data);
    }
}
