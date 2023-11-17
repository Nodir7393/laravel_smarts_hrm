<?php

namespace Modules\ALL\Services;

use App\Services\SearchService;
use http\Env;
use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;

class FileSystemService
{
    public $path;
    public Finder $finder;
    public $sync;

    public function __construct()
    {
        //exec('net use Z: "\\\192.168.100.100\Records\xiaomi_camera_videos" /user:"camera" "camera12" /persistent:no');
        $this->path = env('CAMERA_PATH');
        $this->finder = new Finder();
    }

    /**
     * Creating All.url in a directory
     */
    public function createUrlFile($path, $url, $name = null): void
    {
        $text = view('sync::components.url-file', compact('url'));
        $title = is_null($name) ? '/ALL.url' : '/' . $name . '.url';
        file_put_contents($path . $title, $text);
    }

    /**
     * Looking for URL file in directory
     */
    public function searchForUrl($path)
    {
        $fileUrl = new Finder();
        $files = $fileUrl->files()->name(['ALL.url', 'T.Me*.url'])->in($path);
        foreach ($files as $file) {
            return $file->getRealPath();
        }
    }

    /**
     * Getting a All.txt (coordinating) file in a root
     */
    public function searchForTxt($path): ?string
    {
        return file_exists($path . '/ALL.txt') ? $path . '/ALL.txt' : null;
    }

    /**
     * Scanning for
     * @param $path
     * @return int
     */
    public function fileExists($path): int
    {
        $folderFiles = new Finder();
        return $folderFiles->in($path)->notName(['.*', '@*', 'ALL.txt', '*.url'])->files()->depth('==0')->count();
    }

    /**
     * Generating for captions for telegram posts according to file types
     */
    public function caption($path)
    {
        $path_parts = pathinfo($path);
        if (array_key_exists('extension', $path_parts)) {
            switch ($path_parts['extension']) {
                default:
                    if ($path_parts['basename'] === 'Readme.txt') {
                        return file_get_contents($path);
                    }
                    break;
                case 'mhtml':
                    $content = file($path);
                    foreach ($content as $item) {
                        if (str_contains($item, 'Snapshot-Content-Location:')) {
                            $line = explode(' ', $item);
                            return substr($line[1], 0, -2);
                        }
                    }
            }
        } else {
            return $path_parts['filename'];
        }
    }


    function getFileNames($finder)
    {
        foreach ($finder as $file) {
            $result[] = pathinfo($file->getFilename(), PATHINFO_BASENAME);
        }

        return $result;
    }
    /**
     * Getting only files stored in a specific
     * @param $path
     * @return Finder
     */
    public function getFiles($path): Finder
    {
        $getFiles = new Finder();
        return         $return = $getFiles->in($path)->files()->notName(['.*', '*.url']);


        return $getFiles->in($path)->files()->depth('== 0')->notName(['.*', '*.url']);

        return $return;
    }

    /**
     * Comparing two arrays and getting unprocessed
     */
    public function compare($finder, $tgfiles, $type): array
    {
        $localFiles = $this->getFileNames($finder);

        $result = [];
        if ($type) {
            foreach ($localFiles as $file) {
             //   $filename =pathinfo($file->getFilename(), PATHINFO_BASENAME);
                if (array_search($file, $tgfiles, true)=== false) {
                    $result[] =  $file;
                }
            }
        } else {

            foreach ($tgfiles as $key => $file) {
                if (array_search($file, $localFiles, true) === false) {
                    $result[$key] =  $file;
                }

            }
        }
        return $result;
    }

    /**
     * Processing a .url file and getting a URL link
     */
    public function readUrl($path)
    {
        $pattern = '#(?<=URL=)(.+(?=\n))#';
        $pattern = '#(?<=URL=)(.+(?=\r\n))#';
        $content = file_get_contents($path);

        preg_match($pattern, $content, $argument);
        return Arr::get($argument, 0);
    }

    /**
     * Verifying directory
     * Looking for files except ALL.txt and ALL.url or T.Me...url
     * Processing - Theory folder
     */
    public function createPost($dir, $txt_data, $titles): void
    {
        $search = new SearchService();
        $fileExists = $this->fileExists($dir);
        $temp = $dir;
        $isTheory = $dir->getFilename() !== '- Theory';
        $theoryKey = array_search('- Theory', $titles);
        if ($fileExists != 0) {
            if (!$isTheory) $titles[$theoryKey] = 'ALL';
            $post_url = $search->searchForMessage($txt_data, $titles);
            $this->createUrlFile($dir->getRealPath(), $post_url);
            $dir = $temp;
        }
    }

    /**
     * Get All directories
     */
    public function getDirectories($path): Finder
    {
        return $this->finder->in($path)->directories()->notName(['.*', '@*']);
    }

    /**
     * Generates a Camelcase Title for file by
     * @param $link
     * @return string
     */
    public function generateUrlTitle($link): string
    {
        $link = str_contains($link, 'http') ? substr($link, 8) : $link;
        $split = str_replace('/', ' ', str_replace('_', ' ', $link));
        return ucwords($split, '.- ');
    }

    /**
     * Common
     */
    public function sync($path, $txt_data, $output): void
    {
        $progress = new Finder();
        $count = $progress->in($path)->directories()->notName(['.*', '@*'])->count();
        $dirs = $this->getDirectories($path);
        $progressBar = $output->createProgressBar($count);
        foreach ($dirs as $dir) {
            $explodedPathname = explode('\\', $dir->getRelativePathname());
            $reversePathname = array_reverse($explodedPathname);
            $this->createPost($dir, $txt_data, $reversePathname);
            $progressBar->advance();
        }
        $progressBar->finish();
        $output->newLine();
    }
}
