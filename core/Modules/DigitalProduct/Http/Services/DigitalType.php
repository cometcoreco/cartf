<?php

namespace Modules\DigitalProduct\Http\Services;

class DigitalType
{
    public function digitalType()
    {
        return [
            'd_image' => 'image',
            'd_video' => 'video',
            'd_audio' => 'audio',
            'd_file'  => 'file'
        ];
    }

    public function extensionType()
    {
        return [
            'd_image' => [
                'jpeg', 'jpg', 'png', 'gif', 'tif', 'bmp', 'ico', 'psd', 'webp', 'ai', 'eps', 'svg', 'cdr', 'indd', 'raw'
            ],
            'd_video' => [
                'mp4', 'avi', 'mov', 'flv', 'avchd'
            ],
            'd_audio' => [
                'm4a', 'mp3', 'wav'
            ],
            'd_file' => [
                'pdf', 'doc', 'docx', 'html', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'odp', 'zip'
            ]
        ];
    }

    public function digitalTypeWithExtension()
    {
        $digital_type = $this->digitalType();
        $extension = $this->extensionType();

        $extension_type = [];
        foreach ($digital_type as $index => $type)
        {
            $extension_type[$index][$type] = $extension[$index];
        }

        return $extension_type;
    }
}
