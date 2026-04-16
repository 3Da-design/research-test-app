<?php

namespace App\Services;

use App\Repositories\ItemRepository;

class ResearchSampleService
{
    public function __construct(private readonly ItemRepository $itemRepository)
    {
    }

    /**
     * @return array<string, string>
     */
    public function hello(): array
    {
        return [
            'message' => 'Hello from shared Laravel backend API',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function items(): array
    {
        return $this->itemRepository->all();
    }
}
