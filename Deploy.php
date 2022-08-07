<?php

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Slb\Slb;

// Set up a global client
try {
    AlibabaCloud::accessKeyClient('', '')
        ->regionId('cn-shenzhen')
        ->asDefaultClient();
} catch (ClientException $e) {
}

$options = [
    '要修改的虚拟服务器组的ID' => 'groupId:',
    '要修改的服务器的ID' => 'serverId:',
    '要修改的服务器的端口' => 'port:',
    '要修改的服务器的权重' => 'weight:',
];
$params = getopt('', $options);

$invalidParams = false;
foreach ($options as $desc => $option) {
    $option = substr($option, 0, -1);
    if (!isset($params[$option])) {
        var_dump("麻烦传一下$desc: --$option {xxx}");
        $invalidParams = true;
    }
}
if ($invalidParams) {
    var_dump(-1);
    return ;
}

try {
    // 只修改服务器权重的接口
    $result = Slb::v20140515()
        ->setVServerGroupAttribute()
        ->withVServerGroupId($params['groupId'])
        ->withBackendServers(json_encode([
            [
                'ServerId' => $params['serverId'],
                'Port' => $params['port'],
                'Weight' => $params['weight'],
            ]
        ]))
        ->request();
    var_dump($result->isSuccess() ? 1 : -1);

} catch (ClientException $exception) {
    echo $exception->getMessage(). PHP_EOL;
} catch (ServerException $exception) {
    echo $exception->getMessage() . PHP_EOL;
    echo $exception->getErrorCode(). PHP_EOL;
    echo $exception->getRequestId(). PHP_EOL;
    echo $exception->getErrorMessage(). PHP_EOL;
}