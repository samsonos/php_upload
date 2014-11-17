<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 17.11.2014
 * Time: 14:12
 */

namespace samson\upload;
use Aws\S3\S3Client;
use Aws\Common\Credentials\Credentials;

class AwsAdapter implements iAdapter {
    public $adapterID = 'amazon';
    /** @var Credentials $credentials access key and secret key for amazon connect */
    protected $credentials;

    /** @var S3Client $client Aws services user */
    protected $client;

    /** @var string $bucket Aws bucket name */
    public static $bucket;

    /** @var string $accessKey */
    public static $accessKey;

    /** @var string $secretKey */
    public static $secretKey;

    /** @var callable $handler External handler for creating amazon upload catalog name */
    public static $handler;

    /** @var string $awsUrl url of amazon bucket */
    public static $awsUrl;

    /**
     * @param string $accessKey Amazon access key
     * @param string $secretKey Amazon secret key
     * @param $bucket
     */
    public function  __construct()
    {
        $this->credentials = new Credentials(AwsAdapter::$accessKey, AwsAdapter::$secretKey);

        // Instantiate the S3 client with AWS credentials
        $this->client = S3Client::factory(array(
            'credentials' => $this->credentials
        ));
    }

    /**
     * @param $data
     * @param string $filename
     * @param string $uploadDir
     * @return string Path to file
     */
    public function putContent($data, $filename = '', $uploadDir = '')
    {
        $this->client->putObject(array(
            'Bucket'       => AwsAdapter::$bucket,
            'Key'          => $uploadDir.'/'.$filename,
            'Body'         => $data,
            'CacheControl' => 'max-age=1296000',
            'ACL'          => 'public-read'
        ));

        return AwsAdapter::$awsUrl.'/'.$uploadDir;
    }

    public function getID()
    {
        return $this->adapterID;
    }
}
