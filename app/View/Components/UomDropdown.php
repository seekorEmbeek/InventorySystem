<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class UomDropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public $uoms;
    public $selected;
    public $name;
    public $id;

    public function __construct($selected = null, $name = 'uom', $id = 'uom')
    {
        // $this->uoms = $uoms;
        // $this->uoms = ['PCS', 'BOX', 'KG', 'METER', 'LITER', 'PETI', 'KARUNG', 'BOSS', 'KARDUS'];
        $uoms = Product::pluck('uom')->toArray();
        $this->uoms = array_unique($uoms);
        $this->selected = $selected;
        $this->name = $name;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.uom-dropdown');
    }
}
