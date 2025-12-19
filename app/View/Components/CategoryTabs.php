<?php

namespace App\View\Components;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoryTabs extends Component
{
    public $currentCategory;

    /**
     * Create a new component instance.
     */
    public function __construct($currentCategory = null)
    {
        $this->currentCategory = $currentCategory;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $categories = Category::get();
        return view('components.category-tabs', [
            'categories' => $categories,
            'currentCategory' => $this->currentCategory,
        ]);
    }
}
