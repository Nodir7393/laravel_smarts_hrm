<?php

namespace Modules\HHParse\Services;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Support\Facades\Http;

class Test
{
    protected string $url;
    private array $cookies = [
      [
          'gssc58',
          null,
          'tashkent.hh.uz'
      ],
        [
            '_ym_isad',
            '1',
            '.hh.uz'
        ],
        [
            '_gid',
            'GA1.2.1270124047.1680243132',
            '.hh.uz'
        ],
        [
            'device_breakpoint',
            's',
            'tashkent.hh.uz'
        ],
        [
            '__ddg1_',
            'k5HYmc9nTi0a5KKGQVOF',
            '.hh.uz'
        ],
        [
            '_ym_uid',
            '168023623976232186',
            '.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            '_ym_d',
            '1680243132',
            '.hh.uz'
        ],
        [
            'regions',
            '2759',
            'tashkent.hh.uz'
        ],
        [
            'region_clarified',
            'NOT_SET',
            '.hh.uz'
        ],
        [
            'redirect_host',
            'tashkent.hh.uz',
            '.hh.uz'
        ],
        [
            '__zzatgib-w-hh',
            'MDA0dC0jViV+FmELHw4/aQsbSl1pCENQGC9LX3ovQR8lYXhbIUcOCjUoHRp+c1kLCg0XcHQmdFpCZiQZOVURCxIXRF5cVWl1FRpLSiVueCplJS0xViR8SylEW1V5Jx0VfHAnVAsNVy8NPjteLW8PKhMjZHYhP04hC00+KlwVNk0mbjN3RhsJHlksfEspNQ9QCi5KF3tuI08KCxRCRih0LUQjHxx9WSJDEH4yV0xEem0pCQwNW0MzaWVpcC9gIBIlEU1HGEVkW0I2KBVLcU8cenZffSpBbR1hS10kSFlSfSYVe0M8YwxxFU11cjgzGxBhDyMOGFgJDA0yaFF7CT4VHThHKHIzd2UqO2wfZUtdIUpHSWtlTlNCLGYbcRVNCA00PVpyIg9bOSVYCBI/CyYfGXdtJ1MMEF9CRW9vG382XRw5YxEOIRdGWF17TEA=gT1AaQ==',
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
        [
            'gssc58',
            null,
            'tashkent.hh.uz'
        ],
    ];

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getHtml() :string
    {
        $response = Http::get($this->url);

        return $response->body();
    }

    protected function setCookies() :CookieJar
    {
        $cookies = new CookieJar();
        $cookies->setCookie(new SetCookie());
        return $cookies;
    }
}
