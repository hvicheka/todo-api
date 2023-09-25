<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class TodoBuilder extends Builder
{
    /**
     * Add a where clause to the query for filter.
     * @param string $q
     * @return $this
     */
    public function search($q): self
    {
        return $this->where(function ($query) use ($q) {
            return collect(str_getcsv($q, ' ', '"'))->filter()->each(function ($term) use ($query) {
                $term = "%$term%";
                return $query->where(function ($query) use ($term) {
                    $query->where('title', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            });
        });
    }
}
