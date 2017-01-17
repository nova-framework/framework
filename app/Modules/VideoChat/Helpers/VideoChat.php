<?php

namespace App\Modules\VideoChat\Helpers;

use Nova\Support\Facades\DB;

use App\Modules\Users\Models\User;

use Carbon\Carbon;


/**
 * VideoChat class
 *
 * This class provides methods for video chat management.
 */
class VideoChat
{
    /**
     * Get the video chat info by its users
     *
     * @param int $fromUserId User id
     * @param int $toUserId Other user id
     * @return array The video chat info. Otherwise return false
     */
    public static function getChatRoomByUsers($fromUserId, $toUserId)
    {
        $fromUserId = intval($fromUserId);
        $toUserId   = intval($toUserId);

        if (empty($fromUserId) || empty($toUserId)) {
            return false;
        }

        $result = DB::table('chat_video')
            ->where(function($query) use ($fromUserId, $toUserId) {
                $query->where('sender_id', $fromUserId)->where('receiver_id', $toUserId);
            })
            ->orWhere(function($query) use ($fromUserId, $toUserId) {
                $query->where('sender_id', $toUserId)->where('receiver_id', $fromUserId);
            })
            ->first();

        return $result ?: false;
    }

    /**
     * Create a video chat
     * @param int $fromUser The sender user
     * @param int $toUser The receiver user
     * @return int The created video chat id. Otherwise return false
     */
    public static function createRoom($fromUserId, $toUserId)
    {
        $fromUser = User::find($fromUserId);
        $toUser   = User::find($toUserId);

        $chatName = __d('video_chat', 'Video chat between {0} and {1}', $fromUser->first_name, $toUser->first_name);

        return DB::table('chat_video')->insert(array(
            'sender_id'   => intval($fromUserId),
            'receiver_id' => intval($toUserId),
            'room_name'   => $chatName,
            'created_at'  => Carbon::now()->toDateTimeString()
        ));
    }

    /**
     * Check if the video chat exists by its room name
     * @param string $name The video chat name
     *
     * @return boolean
     */
    public static function nameExists($name)
    {
        $result = DB::table('chat_video')->where('room_name', $name)->count();

        if ($result !== false) {
            return ($result > 0);
        }

        return false;
    }
}
