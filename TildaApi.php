<?php

namespace globus\tilda;

use globus\tilda\models\TildaPage;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

class TildaApi extends Component
{
    const API_BASE_URL = 'http://api.tildacdn.info';
    const API_STATUS_SUCCESS = 'FOUND';
    const API_STATUS_ERROR = 'ERROR';

    const GET_PROJECT_LIST = '/v1/getprojectslist';
    const GET_PROJECT_INFO = '/v1/getproject';
    const GET_PROJECT_INFO_EXPORT = '/v1/getprojectexport';
    const GET_PAGE_LIST = '/v1/getpageslist';
    const GET_PAGE_INFO = '/v1/getpage';
    const GET_PAGE_INFO_FULL = '/v1/getpagefull';
    const GET_PAGE_EXPORT = '/v1/getpageexport';
    const GET_PAGE_EXPORT_FULL = '/v1/getpagefullexport';

    /** @var  string */
    public $assetsPath;
    /** @var  string */
    public $assetsUrl;
    /** @var  string */
    public $publicKey;
    /** @var  string */
    public $secretKey;
    /** @var  TildaExportPage */
    public $pageObj;
    /** @var  Client */
    public $client;

    public function init()
    {
        $this->client = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
            'baseUrl' => self::API_BASE_URL
        ]);

        if (!$this->publicKey) {
            throw new InvalidConfigException("publicKey can't be empty!");
        }
        if (!$this->secretKey) {
            throw new InvalidConfigException("secretKey can't be empty!");
        }
        if (!$this->assetsPath) {
            throw new InvalidConfigException("assetsPath can't be empty!");
        } elseif($this->assetsPath[strlen($this->assetsPath)-1] != DIRECTORY_SEPARATOR) {
            $this->assetsPath .= DIRECTORY_SEPARATOR;
        }
        if (!$this->assetsUrl) {
            throw new InvalidConfigException("assetsPath can't be empty!");
        } elseif($this->assetsUrl[strlen($this->assetsUrl)-1] != '/') {
            $this->assetsUrl .= '/';
        }
    }

    public function getPages($projectID)
    {
        $oldPage = TildaPage::find()->select(['page_id'])->column();
        $request = $this->client->createRequest()
            ->setMethod('get')
            ->setUrl(self::GET_PAGE_LIST)
            ->setData([
                'publickey' => $this->publicKey,
                'secretkey' => $this->secretKey,
                'projectid' => $projectID,
            ])->send();

        if ($request->isOk) {
            if (isset($request->data['status']) && $request->data['status'] == self::API_STATUS_SUCCESS) {
                $pageIDs = array_column($request->data['result'], 'id');
                $oldToRemove = array_diff($oldPage, $pageIDs);
                TildaPage::deleteAll(['page_id' => $oldToRemove]);

                foreach ($pageIDs as $pageID) {
                    $this->getPage($pageID);
                }
            }
        } elseif (isset($request->data['status']) && $request->data['status'] == self::API_STATUS_ERROR) {
            \Yii::warning($request->data['message'], 'yii2-tilda-api');
        }
    }

    public function getPage($pageID)
    {
        $request = $this->client->createRequest()
            ->setMethod('get')
            ->setUrl(self::GET_PAGE_EXPORT)
            ->setData([
                'publickey' => $this->publicKey,
                'secretkey' => $this->secretKey,
                'pageid' => $pageID,
            ])->send();

        if ($request->isOk) {
            if (isset($request->data['status']) && $request->data['status'] == self::API_STATUS_SUCCESS) {
                $this->pageObj = new TildaExportPage($request->data['result'], $this->assetsPath, $this->assetsUrl);
            }
        } elseif (isset($request->data['status']) && $request->data['status'] == self::API_STATUS_ERROR) {
            \Yii::warning($request->data['message'], 'yii2-tilda-api');
        }
    }
}
