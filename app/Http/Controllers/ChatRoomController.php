<?php

namespace App\Http\Controllers;

use App\Services\ApiFootballService;
use App\Services\Translate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatRoomController extends Controller
{
   public function getChatRecode(Request $request, $room_id){
       $nodes = Storage::files('chatrecode');

       $selectedNode = $this->SimpleConsistentHash($room_id, $nodes);
       $file = Storage::get($selectedNode);
       $data = json_decode($file,true);
       return response()->json(['code' => 0, 'data' => $data['data'][0]['msgLst']]);
   }

    public function simpleConsistentHash($key, $nodes, $replicas = 10) {
        $hashes = [];
        foreach ($nodes as $node) {
            for ($i = 0; $i < $replicas; $i++) {
                $hash = crc32($node . $i);
                $hashes[$hash] = $node;
            }
        }

        ksort($hashes);
        $keyHash = crc32($key);

        foreach ($hashes as $hash => $node) {
            if ($keyHash <= $hash) {
                return $node;
            }
        }

        // Wrap around case
        return reset($nodes);
    }
}
