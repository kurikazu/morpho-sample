<?php

namespace App\Jobs;

use \Curl\Curl;

class MorphoJob extends Job
{
    protected $filename;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filename)
    {
        //
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
/*
        //
        $file = new \SplFileObject($this->filename);
        $file->setFlags(\SplFileObject::READ_CSV);
        foreach ($file as $line) {
            $tmp = implode(",",$line);
        }
        $file = null;
*/
        //
        $curl = new Curl();
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $curl->get(env('MORPHO'), array(
            'd' => 'こんにちは、世界',
        ));
        dd($curl->response);
//json_decode($curl->response, true)
    }
}
