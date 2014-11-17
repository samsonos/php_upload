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
    /** @var string $adapterId Adapter identifier */
    public $adapterId = 'amazon';

    /** @var Credentials $credentials access key and secret key for amazon connect */
    protected $credentials;

    /** @var S3Client $client Aws services user */
    protected $client;

    /** @var string $bucket Aws bucket name */
    protected $bucket;

    /**
     * @param string $accessKey Amazon access key
     * @param string $secretKey Amazon secret key
     * @param $bucket
     */
    public function  __construct($accessKey, $secretKey, $bucket, $dir)
    {
        $this->credentials = new Credentials('AKIAJRG2YUZ7KGMLDXRQ', 'j5TUvJNFMth9eVbTpQDY07skdCvL6zT8A0dWjqNv');

        // Instantiate the S3 client with AWS credentials
        $this->client = S3Client::factory(array(
            'credentials' => $this->credentials
        ));

        $this->bucket = 'landscapestatic';

        $this->dir = $dir;
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
            'Bucket'       => $this->bucket,
            'Key'          => $this->dir.'/'.$filename,
            'Body'         => $data,
            'CacheControl' => 'max-age=1296000',
            'ACL'          => 'public-read'
        ));

        return $this->dir.'/'.$filename;
    }

    public function getId()
    {
        return $this->adapterId;
    }
}
