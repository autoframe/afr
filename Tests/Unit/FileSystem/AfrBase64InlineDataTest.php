<?php
declare(strict_types=1);

namespace Unit;


use Autoframe\Core\FileSystem\Encode\AfrBase64InlineDataClass;
use PHPUnit\Framework\TestCase;

class AfrBase64InlineDataTest extends TestCase
{
    public static function getBase64InlineDataDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            [__DIR__ . DIRECTORY_SEPARATOR . 'AfrBase64InlineDataTest.jpg', 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/4QAiRXhpZgAATU0AKgAAAAgAAQESAAMAAAABAAEAAAAAAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAALABADASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD5p/Zw/Z0+GfgD4jf8IJJbeG/GVv8AE62WzluvEnheG6uNMSR2WSW1lSGSW3McUUxEsDQOsskR3qACdr9jL9kP4b/tA/DBrxvGVx8LfFPgm5TTNbSHTRfaTqu9f3bRF7hZhMHjnaRmRI2SaBY1HlO8ngPhVW8V/GL7dfTXTXkkskbTRTvC7KsKxquUKnaFQYHQEuRy7E/TH7Jvww0P4dfEy3m0ax+xyTN5DHzpJBtUuOA7EBiGIZhy3G4nAx9tgsPR+vvH03JKcm+VNJatJLa7Sj1bbvtpofHZhleKp5b/AGdOqnOEVH2llzXW8rbK76JWt0vqf//Z'],
            [__DIR__ . DIRECTORY_SEPARATOR . 'AfrBase64InlineDataTest.png', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAALCAYAAAB24g05AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJuSURBVChTDZBLSFQBGIW/+5yXzowz4ziaaZqKj8IkVIgkDTUXPWijQa0satWioNazclVthDYRFNFCAosWBplkkgiFSg8b03wy6pimzst5eGdud/FzNj/nfOcIFNfraFlAgP20IXlImTCZWBDyixEUFVW1kWexotgdmHJdqFY7WUNNsoooFtaAIx80aDmew8GLL2jRLiLhpxA5QHCX4HG5KKqpZSOeJrG/Ryq2ix7e5iARRtKTmp+sQXAg01SpMRC8wMfZbvYWxqlr72EjEODllXl85Z1MLUWJJ1LGbxxdlNBlK6J8/R5KxyXoPE+BOU1wNcm10gGGPkVxbk+zsxWi/1szT56Psz7WgtntJe3ykbWoZGUNQR4M6Q57Lk63lZt9DuZS+eR6ilmb3aLh0TMen26iqkLk/p2LnOlsZuTIDV5pHkzGZBkDXJQ319CCq8QmfhHXFRoqNPRUhLu9ZTjLPJR39+IsaMDfv01D+xscC7NYw2DeAtU4WVhZJG12ck4bZnC+nGq8vB8JsU0ubncGlSQTyxrxokZKOq6yM/MTyVJOcN+GIGaRfNVtfiEl0S2+5l8kwdv1Vuy1jeS0PqRHuE18aoSQXkQwLGE1yVRVVhOZG2A2ZFj/XUE60dzlP+z1MjX8jrE/ZUaqjCKJpLM5bCxvMjn9mx/hCvLsVnRd5/OiHVdyifq6enwWwdggsUf94hALU/NcbjvKbmCUaOAD4vcHzHwdN5JVjpXK+EwxNkf7OOuaxJ5ThLyzhlPIINw6eUqPRfeZdPiM9llikoQkK3gLy1DMNgNbQUvGkVWz0dmYXlKw2uxYPG5s+Yf4D5TN7so45vxeAAAAAElFTkSuQmCC'],
        ];
    }

    /**
     * @test
     * @dataProvider getBase64InlineDataDataProvider
     */
    public function getBase64InlineDataTest(string $sFile, string $sExpected): void
    {
        $sInline = AfrBase64InlineDataClass::getInstance()->getBase64InlineData($sFile);
        $this->assertSame($sExpected, $sInline);
    }

    /**
     * @test
     */
    public function getBase64InlineOnePxTest(): void
    {
        $this->assertSame('data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==', AfrBase64InlineDataClass::getInstance()->getBase64InlineOnePx());
    }
}