<?php

namespace Modules\HHParse\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HHResumesDownloader
{
    public function callForDownload($resume_id, $id, $url, $token, $ext = 'pdf') {
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Accept-language: en\r\n" .
                    "Cookie: hhtoken=".$token."\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        Log::debug("context = " . print_r($context, true));
        $file = file_get_contents($url, false, $context);
        Storage::disk('local')->put("\\$ext\\$resume_id.$ext", $file);
        $this->updateResumeStatus($id, $ext);
    }

    protected function updateResumeStatus(int $id, $ext) :void
    {
        DB::table('hh_resumes')->where('id', $id)->update(["is_$ext" => 1]);
    }
}
