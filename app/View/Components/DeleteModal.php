<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DeleteModal extends Component
{
    public $id;
    public $title;
    public $message;
    public $route;

    /**
     * Create a new component instance.
     *
     * @param string $id Unique ID for the modal
     * @param string $title Modal title
     * @param string $message Message shown in modal
     * @param string|null $route Form action route (DELETE)
     */
    public function __construct($id, $title = 'Confirm Deletion', $message = 'Are you sure?', $route = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
        $this->route = $route ?? '#'; // fallback if no route provided
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.delete-modal');
    }
}
