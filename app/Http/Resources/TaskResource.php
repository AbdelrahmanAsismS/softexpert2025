<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'due_date'    => $this->due_date,

            'parent' => $this->parent ? [
                'id'    => $this->parent->id,
                'title' => $this->parent->title,
            ] : null,

            'children' => $this->children->map(function ($child) {
                return [
                    'id'     => $child->id,
                    'title'  => $child->title,
                    'status' => $child->status,
                ];
            }),

            'assignee' => [
                'id'    => $this->assignee?->id,
                'name'  => $this->assignee?->name,
                'email' => $this->assignee?->email,
            ],

            'creator' => [
                'id'    => $this->creator?->id,
                'name'  => $this->creator?->name,
                'email' => $this->creator?->email,
            ],

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
