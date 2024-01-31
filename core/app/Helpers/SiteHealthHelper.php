<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class SiteHealthHelper
{
    public function getInfo()
    {
        $info = [];
        $issues = 0;
        $warning = false;

        try {
            if (!tenant() && function_exists('ini_get'))
            {
                $info['memory_limit'] = $memory_limit =  ini_get("memory_limit");
                $info['post_size'] = $post_max_size =  ini_get("post_max_size");
                $info['execution_time'] = $max_execution_time =  ini_get("max_execution_time");
                $info['upload_filesize'] = $upload_max_filesize =  ini_get("upload_max_filesize");

                // required version
                $required_minimum_versions = [
                    'php_version' => 8.1,
                    'mysql_version' => 5.7,
                    'laravel_version' => 9,
                    'memory_limit' => 512,
                    'execution_time' => 300,
                    'upload_filesize' => 128,
                    'post_size' => 128
                ];

                foreach (
                    [
                        [(int)str_replace('M','',$memory_limit), $required_minimum_versions['memory_limit']],
                        [(int)str_replace('M','',$post_max_size), $required_minimum_versions['post_size']],
                        [(int)$max_execution_time, $required_minimum_versions['execution_time']],
                        [(int)str_replace('M','',$upload_max_filesize), $required_minimum_versions['upload_filesize']]
                    ] ?? [] as $item)
                {
                    if (current($item) < last($item))
                    {
                        $issues++;
                    }
                }

                if (
                    !(phpversion() >= $required_minimum_versions['php_version']) ||
                    !(DB::select("SELECT VERSION() as version")[0]->version >= $required_minimum_versions['mysql_version']) ||
                    !(app()->version() >= $required_minimum_versions['laravel_version']) ||
                    !($memory_limit >= $required_minimum_versions['memory_limit']) ||
                    !($post_max_size >= $required_minimum_versions['execution_time']) ||
                    !($max_execution_time >= $required_minimum_versions['upload_filesize']) ||
                    !($upload_max_filesize >= $required_minimum_versions['post_size'])
                )
                {
                    $warning = true;
                }

                return [
                    'info' => $info,
                    'required_versions' => $required_minimum_versions,
                    'issues' => $issues,
                    'warning' => $warning
                ];
            }
        } catch (\Exception $exception) {}
    }

    public function getIssues()
    {
        $info = $this->getInfo();

        return $info['issues'] ?? 0;
    }

    public function getWarning()
    {
        return $this->getInfo()['warning'] ?? false;
    }

    public function getReadMore(): array
    {
        return [
            'route' => route('landlord.admin.health'),
            'text' => __('Read More')
        ];
    }

    public function issueMessage(): string
    {
        return __(number_to_word($this->getIssues()) . ' ' . ($this->getIssues() > 1 ? 'issues detected!' : 'issue detected!'));
    }
}
