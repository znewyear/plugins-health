<?php

namespace Health\Core\Checker;

use Exception;
use Health\Core\Health;
use Health\Core\Inspection;
use Health\Core\HealthStatus;

/**
 * OSS/S3 健康检查器
 *
 * 配置示例（config/health.php services 节点）：
 * [
 *     'check' => \Health\Core\Health::OSS,
 *     'name' => 'oss',
 *     'access_key' => 'your-access-key',
 *     'secret_key' => 'your-secret-key',
 *     'bucket' => 'your-bucket',
 *     'endpoint' => 'oss-cn-xxx.aliyuncs.com',
 *     'timeout' => 3
 * ]
 */
class OssChecker implements HealthCheckerInterface
{
    public function check(Inspection $item, HealthStatus $status): bool
    {
        $start = microtime(true);

        try {
            // 伪代码：请根据实际 OSS/S3 SDK 替换
            // $client = new OssClient($item->get('access_key'), $item->get('secret_key'), $item->get('endpoint'));
            // $buckets = $client->listBuckets();
            // $bucket = $item->get('bucket');
            // $object = 'health_check_' . uniqid() . '.txt';
            // $client->putObject($bucket, $object, 'health');
            // $client->deleteObject($bucket, $object);

            // 如需 AWS S3，可用 AWS SDK
            // $s3 = new S3Client([...]);
            // $s3->putObject([...]);
            // $s3->deleteObject([...]);

            // 这里只做伪操作
            usleep(100 * 1000); // 模拟延迟

            $status->setStatus(Health::STATUS_UP);
            $ok = true;
        } catch (Exception $e) {
            $status->setStatus(Health::STATUS_DOWN)
                ->setMessage($e->getMessage());
            $ok = false;
        }

        $latency = round((microtime(true) - $start) * 1000, 2);
        $status->setLatency($latency);

        return $ok;
    }
}
