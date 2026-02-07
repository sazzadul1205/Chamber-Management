<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BackSubmitButtons extends Component
{
    public string $backUrl;
    public string $submitText;

    public function __construct(
        string $backUrl = 'javascript:history.back()',
        string $submitText = 'Save'
    ) {
        $this->backUrl = $backUrl;
        $this->submitText = $submitText;
    }

    public function render()
    {
        return view('components.back-submit-buttons');
    }
}
