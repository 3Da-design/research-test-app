<?php

namespace App\Repositories;

use App\Models\Item;

class ItemRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return Item::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Item $item) => [
                'id' => $item->id,
                'name' => $item->name,
            ])
            ->all();
    }
}
