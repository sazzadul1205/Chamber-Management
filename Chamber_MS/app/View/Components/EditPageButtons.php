<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EditPageButtons extends Component
{
    public string $backUrl;
    public string $submitText;
    public string $deleteModalId;
    public string $submitColor;

    /**
     * Create a new component instance.
     *
     * @param string $backUrl       URL for the back/cancel button
     * @param string $submitText    Text for the submit button
     * @param string $deleteModalId ID of the delete modal
     * @param string $submitColor   Tailwind color for submit button (e.g., 'blue', 'green')
     */
    public function __construct(
        string $backUrl,
        string $submitText = 'Save',
        string $deleteModalId = 'deleteModal',
        string $submitColor = 'blue'
    ) {
        $this->backUrl = $backUrl;
        $this->submitText = $submitText;
        $this->deleteModalId = $deleteModalId;
        $this->submitColor = $submitColor;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.edit-page-buttons');
    }
}
