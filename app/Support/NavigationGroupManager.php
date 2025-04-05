<?php

namespace App\Support;

use Filament\Navigation\NavigationGroup;

class NavigationGroupManager
{
    protected array $groups = [];
    protected ?string $activeGroupKey = null;

    public function __construct(array $fixedOrder, string $currentRoute)
    {
        foreach ($fixedOrder as $groupKey) {
            if ($currentRoute && str_contains($currentRoute, $groupKey)) {
                $this->activeGroupKey = $groupKey;
                break;
            }
        }
    }

    public function add(string $key, string $label): void
    {
        $this->groups[$key] = NavigationGroup::make($key)
            ->label($label)
            ->collapsed($key !== $this->activeGroupKey);
    }

    public function all(array $order): array
    {
        return collect($order)
            ->map(fn ($key) => $this->groups[$key] ?? null)
            ->filter()
            ->values()
            ->toArray();
    }
}
