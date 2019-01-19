<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/6
 * Time: 下午7:01
 */

namespace App\Console\Commands\Csv;

use App\Service\KfCsvService;
use Illuminate\Console\Command;

class KfzCsvCommand extends Command
{
    protected $signature = 'csv:kfz
        {--t|topic= : topic}
        {--p|price= : price}
        {--transport= : transport}
        {--offset= : transport}
        {--size= : size}
       ';
    protected $description = '生成孔夫子csv';

    public function handle()
    {
        $this->info(app(KfCsvService::class)->createCsv([
            'topic'=>$this->option('topic')??null,
            'price' =>$this->option('price')??10,
            'transport' => $this->option('transport') ?? 'free',
            'offset' => $this->option('offset') ?? 0,
            'size' => $this->option('size') ?? 1000,
        ]));
    }
}
