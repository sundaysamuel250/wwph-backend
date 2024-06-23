<?php

namespace App\Http\Resources;

use App\Models\WwphJob;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentJobResource extends JsonResource
{
    public function jobs($departments) {
        $output = [];
        foreach ($departments as $depat) {
            $job = WwphJob::where("id", $depat->wwph_job_id)->where("status", "active")->first();
            if($job) {
                $output = [...$output, new JobResource($job)];
            }
        }

        return $output;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "jobs" => $this->jobs($this->DepartmentJobs)
            ];
    }
}
