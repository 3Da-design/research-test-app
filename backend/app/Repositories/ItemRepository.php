<?php

namespace App\Repositories;

class ItemRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return [
            ['id' => 1, 'name' => 'Laravel'],
            ['id' => 2, 'name' => 'React'],
            ['id' => 3, 'name' => 'Comparison Sample'],
        ];
    }
}
