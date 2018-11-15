<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/15
 * Time: 10:35 AM
 */

namespace App\Resource;
require_once __DIR__ . '/aliyun-php-sdk-core/Config.php';

class AliTranslateResource extends \RoaAcsRequest
{
    function __construct()
    {
        parent::__construct('Nlp', '2018-04-08', 'wordsegment');
        $this->setUriPattern("/nlp/api/translate/general");
        $this->setMethod("POST");
    }

    public function translateEnToZh($queries = [])
    {
        $query = implode(" lvbs ", $queries);
        # 创建DefaultAcsClient实例并初始化
        $clientProfile = \DefaultProfile::getProfile(
            "cn-shanghai",                   #目前支持持cn-shanghai
            "7OlAwP5eDKbOUQa9",               # 您的 AccessKey ID
            "mk71mMarFIcP4XkcqnNePQF6sbXiHH"            # 您的 AccessKey Secret
        );
        $clientProfile->addEndpoint('cn-shanghai', "cn-shanghai", "Nlp", "nlp.cn-shanghai.aliyuncs.com");
        $client = new \DefaultAcsClient($clientProfile);
        $request = new self();
        $request->setContent(json_encode([
            'q'=>$query,
            'source'=>'en',
            'target'=>'zh',
            "format"=>"text"
        ]));
        try {
            $response = $client->getAcsResponse($request);
            $text = $response->data->translated_text;
            return explode(" lvbs ", $text);
        } catch (\ServerException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        } catch (\ClientException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        }
    }


}


?>