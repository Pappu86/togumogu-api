<?php

namespace App\Http\Resources\Video;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JetBrains\PhpStorm\ArrayShape;

class VideoApiCollection extends ResourceCollection
{
    /**
     * @var array
     */
    private array $pagination;

    /**
     * VideoApiCollection constructor.
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->pagination = [
            'page' => $resource->currentPage(),
            'total' => $resource->total(),
            'from' => $resource->firstItem(),
            'limit' => $resource->perPage(),
            'to' => $resource->lastItem(),
            'totalPages' => $resource->lastPage()
        ];
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    #[ArrayShape(['data' => "\Illuminate\Support\Collection", 'pagination' => "array"])] public function toArray($request): array
    {
        return [
            'data' => VideoApiResource::collection($this->collection),
            'pagination' => $this->pagination
        ];
//        return parent::toArray($request);
    }
}
