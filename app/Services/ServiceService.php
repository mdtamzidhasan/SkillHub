<?php
namespace App\Services;
use App\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Support\Facades\Cache;

class ServiceService{
    public function __construct( protected ServiceRepository $repo) {
        
    }

    public function list(array $filters, $page = 1){
        $key = 'services:' .  md5(json_encode($filters) . ":page:$page");
        return Cache::remember($key, 60, function() use ($filters) {
            return $this->repo->paginateFiltered((int)$filters);
        });
    }

    public function create(array $data){
        $service = $this->repo->create($data);
        Cache::flush();
        return $service;
    }
    
    public function get(int $id){
        $key = "service:$id";
        return Cache::remember($key, 60, function() use ($id) {
            return $this->repo->findById($id);
        });
    }
}