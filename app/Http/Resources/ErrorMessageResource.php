<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorMessageResource extends JsonResource
{
    public static $wrap = 'errors';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'status' => (string) $this['status'],
            'title' => $this['title'],
            'detail' => $this['detail']
        ];
    }

    public function with($request)
    {
        return [
            'links' => [
                'self' => $request->fullUrl()
            ]
        ];
    }
}
