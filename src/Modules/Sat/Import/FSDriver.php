<?php

namespace SatCMS\Modules\Sat\Import;

use Symfony\Component\Finder\Finder;
use core, tf_exception, SplFileInfo;

class FSDriver extends ImportDriver {

    /**
     * @param SplFileInfo $file
     */
    function import_file($pid, SplFileInfo $file) {

        $text = file_get_contents($file->getPathname());

        $data = array(
              'text'          => $text
            , 'title'         => $file->getBasename('.'.$file->getExtension())
            , 'pid'           => $pid
            //, 'image_url'     => ($item->enclosure ? (string)$item->xpath('//enclosure/@url')[0] : false)
            //, 'updated_at'    => strtotime((string)$item->pubDate)
        );

        return $this->create($data);
    }

    /**
     * @param SplFileInfo $file
     */
    function import_section($pid, SplFileInfo $file) {

        $data = array(
              'title'         => $file->getBasename('.'.$file->getExtension())
            , 'pid'           => $pid,
        );

        return $this->create($data, true);
    }

    /**
     * @param $pid
     * @param $root
     * @return array
     */
    function import_branch($pid, SplFileInfo $file) {

        // nodes

        $root = $file->getPathname();

        $finder = new Finder();
        $finder->files()->in($root)
            ->name('*.php')
            ->name('*.txt')
            ->name('*.html')
            ->name('*.bat')
            ->name('*.sh')
            ->depth('== 0');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $this->import_file($pid, $file);
        }

        // subsections

        $finder = new Finder();
        $finder->directories()->in($root)->name('*')->depth('== 0');

        $models = array();

        /** @var \SplFileInfo $dir */
        foreach ($finder as $dir) {
            $this->import_branch($this->import_section($pid, $dir), $dir);
        }

    }

    function import($params) {

        $path = @$params['path'];

        if (!$path || !is_dir($path)) {
            throw new \InvalidArgumentException('Bad dir');
        }

        if (!$this->dry_run && $this->with_clean) {
            $this->clean();
        }

        $this->import_branch($this->node_id, new SplFileInfo($path));

        // upd.counters
        // $this->import_done();

    }

}