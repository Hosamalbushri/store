<?php

namespace App\Observers;

use App\Models\SubCategory;

class SubCategoryObserver
{
    /**
     * Handle the SubCatgory "created" event.
     */
    public function created(SubCategory $subCatgory): void
    {
        //
    }

    /**
     * Handle the SubCatgory "updated" event.
     */
    public function updating(SubCategory $subCategory): void
    {
        if ($subCategory->isDirty('status')) {
            $subCategory->products()->update(['status' => $subCategory->status]);
        }
    }

    /**
     * Handle the SubCatgory "deleted" event.
     */
    public function deleted(SubCategory $subCatgory): void
    {
        //
    }

    /**
     * Handle the SubCatgory "restored" event.
     */
    public function restored(SubCategory $subCatgory): void
    {
        //
    }

    /**
     * Handle the SubCatgory "force deleted" event.
     */
    public function forceDeleted(SubCategory $subCatgory): void
    {
        //
    }
}
