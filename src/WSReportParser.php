<?php

declare(strict_types=1);

namespace WS;

use WS\Client\Client;
use WS\Contracts\ReportParserContract;
use GuzzleHttp\Client as HttpClient;
use WS\Exceptions\WSReportException;

class WSReportParser implements ReportParserContract
{
    private const ERROR_NOT_FOUND = 'not found';
    private const ERROR_RATE_LIMIT = 'rate limit';
    private const ERROR_TRUE = 'true';
    private const STICKER_NOT_FOUND = 'not found';

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(Client $clientFactory)
    {
        $this->httpClient = $clientFactory->getClient();
    }

    /**
     * @throws WSReportException
     */
    public function getData(string $vincode): string
    {
        $this->checkBeforeGetReport($vincode);
        $response = $this->httpClient->request('get','get_sticker?vin=' . $vincode);

        if ($response->getStatusCode() !== 200) {
            throw new WSReportException('Failed attempt, code ' . $response->getStatusCode() .' ' . __CLASS__);
        }

        $contents = $response->getBody()->getContents();
        $decodeResponseContents = json_decode($contents);

        if ($decodeResponseContents->error === self::ERROR_NOT_FOUND) {
            throw new WSReportException('Report not found '  . __CLASS__);
        }

        if ($decodeResponseContents->error === self::ERROR_TRUE) {
            throw new WSReportException('Failed attempt, code in response error '  . __CLASS__);
        }

        if ($decodeResponseContents->error === self::ERROR_RATE_LIMIT) {
            throw new WSReportException('Rare limit attempt, code in response error '  . __CLASS__);
        }

        return base64_decode($decodeResponseContents);
    }

    public function reportIsExist(string $vincode): bool
    {
        $response = $this->httpClient->request('get','check_reports?vin=' . $vincode);

        $contents = $response->getBody()->getContents();
        $decodeResponseContents = json_decode($contents);

        return $decodeResponseContents->sticker === 'true';
    }

    /**
     * @param string $vincode
     *
     * @return void
     * @throws WSReportException
     */
    private function checkBeforeGetReport(string $vincode): void
    {
        $response = $this->httpClient->request('get','check_sticker?vin=' . $vincode);

        $contents = $response->getBody()->getContents();
        $decodeResponseContents = json_decode($contents);

        if ($decodeResponseContents->sticker === self::STICKER_NOT_FOUND) {
            throw new WSReportException('Sticker not found '  . __CLASS__);
        }

        if ($decodeResponseContents->sticker === self::ERROR_TRUE) {
            throw new WSReportException('Sticker has error '  . __CLASS__);
        }
    }
}