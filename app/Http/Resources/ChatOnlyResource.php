<?php

namespace App\Http\Resources;

use App\Models\Message;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatOnlyResource extends JsonResource
{
    public function lastMessage() {
        $message = Message::where("chat_id", $this->id)->orderBy("id", "DESC")->first();
        return $message;
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
            "user1" => new UserResource($this->host),
            "user2" => new UserResource($this->ChatUser),
            "last_message" => $this->lastMessage()
        ];
    }
}
