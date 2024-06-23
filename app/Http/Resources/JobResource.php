<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'company' => $this->company,
            'work_type' => $this->worktype,
            'job_type' => $this->jobtype,
            'job_role' => $this->job_role,
            'salary' => $this->salary,
            'salary_narration' => $this->salary_narration,
            'education' => $this->education,
            'location' => $this->location,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'experience' => $this->experience,
            'date_posted' => $this->date_published,
            'slug' => $this->slug,
        ];
    }
}
