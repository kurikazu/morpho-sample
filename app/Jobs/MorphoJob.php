<?php

namespace App\Jobs;

use \Curl\Curl;

class MorphoJob extends Job
{
    protected $filename;
    /** 接続先のElasticSearchのURL & ポート */
    const BASE_URL = 'localhost';
    const PORT     = '9200';


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
      //
      $file = new \SplFileObject($this->filename);
      $file->setFlags(\SplFileObject::READ_CSV);
      $line_cnt = count($file);
      $cnt = 0;
      foreach ($file as $line) {
          $this->output_file($line, $cnt);
          if($cnt % 100 == 0) echo $cnt . " / " . $line_cnt . " Done." . PHP_EOL;
          $cnt++;
      }
      $file = null;
    }

    private function output_file($line, $cnt)
    {
        $outname = "output_".floor ($cnt / 100).".txt";

        $tmp   = implode(",", $line);
        $words = $this->analyze_word($tmp);
        if ( ! array_key_exists('Result', $words)) {
            return;
        }
        $resultary = json_decode($words['Result'], true);
        if ( ! array_key_exists('tokens', $resultary)) {
            return;
        }
        $strary = $resultary['tokens'];
        foreach ($strary as $str) {
            file_put_contents($outname, $str['token']."\r\n", FILE_APPEND);
        }
    }

    /**
     * リクエスト用のオプションを設定
     */
    protected function create_options($body)
    {
        $url = env('MORPHO') . http_build_query(array(
          'analyzer' => 'kuromoji',
          'pretty'   => true,
        ));
        $header = [
            'Content-Type: application/json',
        ];

        return array(
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
        );
    }

    /**
     * リクエストを実行
     */
    protected function request($options)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result      = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header      = substr($result, 0, $header_size);
        $result      = substr($result, $header_size);
        curl_close($ch);

        return array(
            'Header' => $header,
            'Result' => $result,
        );
    }

    /**
     * 指定した文字列を形態素解析し、結果を返却する
     */
    public function analyze_word($text)
    {
        return $this->request($this->create_options($text));
    }
}
