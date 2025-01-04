<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => 'Success',
            'message' => 'Registered Successfully',
            'data' => [
                'id' => $this->id,
                'username' => $this->username,
                'email' => $this->email,
                'name' => $this->name,
                'token' => $this->whenNotNull($this->token)
            ]
        ];
    }
}
