<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/6
 * Time: 下午7:01
 */

namespace App\Console\Commands;


use App\Models\BookTopic;
use App\Resource\BaiduTranslateResource;
use Illuminate\Console\Command;

class UpdateBookTopic extends Command
{
    protected $name = 'translate:topic';
    protected $description = '转换英文topic为中文topic';
    public function handle()
    {
        $dels = BookTopic::query()->where(['lang'=>'zh'])->delete();
        var_dump("一共删除{$dels}个中文标签");
        $enTics = BookTopic::query()->where(['lang' => 'en'])->get()->toArray();
        var_dump(sprintf('一共%d个英文标签需要转换。', sizeof($enTics)));
        $i = 0;
        foreach ($enTics as $enTic) {
            $enTic['lang'] = 'zh';
            unset($enTic['id']);
            $topics = explode('\\\\',$enTic['topic_descr']);
            $result = app(BaiduTranslateResource::class)->translateEnToZh($topics);
            $enTic['topic_descr'] = sizeof($result)>1?implode('\\\\',$result):$result[0];
            $zhTic = new BookTopic();
            $zhTic->setRawAttributes($enTic);
            $zhTic->save();
            $i++;
        }
        var_dump("转换成功{$i}");
    }

}