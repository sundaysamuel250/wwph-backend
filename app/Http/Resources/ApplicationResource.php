<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\WwphJob;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function getCandidate() {
        return User::where("id", $this->candidate_id)->select("name", "id", "avatar")->first();
    }
    public function getCompany() {
        return User::where("id", $this->company_id)->select("name", "id", "avatar")->first();
    }
    public function Job() {
        return WwphJob::where("id", $this->job_id)->select("title", "id", "slug")->first();
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
            "candidate" => $this->getCandidate(),
            "company" => $this->getCompany(),
            "status" => $this->status,
            "job" => $this->Job(),
        ];
    }
}
