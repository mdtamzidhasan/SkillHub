<?php
namespace App\Repositories;
use App\Models\Service;
class ServiceRepository {

    public function paginateFiltered(int $perPage=15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
        $query = Service::query()->with('provider:id,name')->where('is_active', true);

        if(!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if(!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if(!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        if(empty($fillters['q'])){
            $q = $filters['q'];
            $query->where(function ($q) use ($q) {
                $q->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
    public function create(array $data): Service {
        return Service::create($data);
    }

    public function findById(string $id): ?Service {
        return Service::with(['provider','category'])->findOrFail($id);
    }
}